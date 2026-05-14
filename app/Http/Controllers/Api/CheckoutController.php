<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Services\ABAService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

use App\Services\KHPAY;

class CheckoutController extends Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function abaCheckout(Request $request)
    {
        $user = $request->user();

        // dd($user);

        $cart = Cart::with('cart_items.product')
            ->where('user_id', $user->id)
            ->firstOrFail();

        if ($cart->cart_items->isEmpty()) {
            return $this->errorResponse('Cart is empty', 400);
        }

        $total = 0;
        foreach ($cart->cart_items as $item) {
            $total += $item->product->price * $item->quantity;
        }

        DB::beginTransaction();
        try {

            $order = Order::create([
                'user_id'          => $user->id,
                'total_amount'     => $total,
                'shipping_address' => $request->input('shipping_address', ''),
                'status'           => 'pending',
            ]);

            // dd($order);

            $tranId = 'ORD' . $order->id . '-' . substr(uniqid(), -8);

            // update tran_id in Order
            $order->tran_id = $tranId;
            $order->save();

            foreach ($cart->cart_items as $item) {
                OrderItem::create([
                    'order_id'   => $order->id,
                    'product_id' => $item->product_id,
                    'price'      => $item->product->price,
                    'quantity'   => $item->quantity,
                ]);
            }

            /** @var \App\Services\ABAService $abaService */
            $abaService = app(ABAService::class);

            $currency = $request->input('currency', 'USD');
            $amount = $total;

            if($currency === 'KHR'){
               $exchangeRate = $abaService->exchangeRate();
               $rate = 4100;

               if(($exchangeRate['status']['code'] ?? null) === '00' && isset($exchangeRate['exchange_rates']['usd'])){
                    $rate = (float) $exchangeRate['exchange_rates']['usd']['sell'];
               }

               $amount = $total * $rate;

            }

            // Build payload for ABA service
            $payloadData = [
                'tran_id'        => $tranId,
                'firstname'      => $user->name,
                'phone'          => $user->phone,
                'email'          => $user->email,
                'payment_option' => 'abapay_khqr',
                'amount'         => $amount,
                'currency'       => $currency,
                'callback_url'   => base64_encode(url('api/webhook/aba/callback')),
            ];

            $paywayResponse = $abaService->purchase($payloadData);

            // Log::info('[PayWay] abaCheckout response', $paywayResponse);

            $statusCode = $paywayResponse['status']['code'] ?? 'UNKNOWN';
            if ($statusCode !== '00') {
                DB::rollBack();
                return $this->errorResponse('Payment gateway error', 502, [
                    'code'    => $statusCode,
                    'detail'  => $paywayResponse['status']['message'] ?? 'No detail',
                    'raw'     => $paywayResponse['raw'] ?? null,
                ]);
            }

            $payment = Payment::create([
                'order_id'    => $order->id,
                'provider'    => 'aba',
                'reference'   => $tranId,
                'status'      => 'pending',
                'khqr_string' => $paywayResponse['qrString'] ?? '',
                // 'meta'        => json_encode($paywayResponse),
            ]);

            $cart->cart_items()->delete();

            DB::commit();

            return $this->successResponse([
                'order_id'        => $order->id,
                'payment_id'      => $payment->id,
                'tran_id'         => $tranId,
                'qr_image'        => $paywayResponse['qr_image']        ?? null,
                'qr_string'       => $paywayResponse['qr_string']       ?? null,
                'abapay_deeplink' => $paywayResponse['abapay_deeplink'] ?? null,
                'app_store'       => $paywayResponse['app_store']       ?? null,
                'play_store'      => $paywayResponse['play_store']      ?? null,
                // Include payload fields for ABA plugin form
                'req_time'        => $paywayResponse['req_time']        ?? now()->utc()->format('YmdHis'),
                'merchant_id'     => config('services.aba.merchant_id'),
                'amount'          => $amount,
                'currency'        => $currency,
                'firstname'       => $user->name,
                'email'           => $user->email,
                'phone'           => $user->phone,
                'hash'            => $paywayResponse['hash']            ?? null,
            ], 'Checkout initiated successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('[PayWay] abaCheckout exception', ['error' => $e->getMessage()]);
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function PayWayCheckout(Request $request)
    {
        $user = $request->user();

        $cart = Cart::with('cart_items.product')
            ->where('user_id', $user->id)
            ->firstOrFail();

        if ($cart->cart_items->isEmpty()) {
            return $this->errorResponse('Cart is empty', 400);
        }

        $total = 0;
        foreach ($cart->cart_items as $item) {
            $total += $item->product->price * $item->quantity;
        }

        DB::beginTransaction();
        try {
            $order = Order::create([
                'user_id'          => $user->id,
                'total_amount'     => $total,
                'shipping_address' => $request->input('shipping_address', ''),
                'status'           => 'pending',
            ]);

            $tranId = 'ORD' . $order->id . '-' . substr(uniqid(), -8);
            $order->tran_id = $tranId;
            $order->save();

            foreach ($cart->cart_items as $item) {
                OrderItem::create([
                    'order_id'   => $order->id,
                    'product_id' => $item->product_id,
                    'price'      => $item->product->price,
                    'quantity'   => $item->quantity,
                ]);
            }

            /** @var KHPAY $khpayService */
            $khpayService = app(KHPAY::class);

            $currency = $request->input('currency', 'USD');
            $amount = $total;

            $paywayResponse = $khpayService->createPayment($currency, $amount);

            // Detailed check for the response format
            if (!$paywayResponse || (!isset($paywayResponse['qr_image']) && !isset($paywayResponse['deeplink']))) {
                DB::rollBack();

                $errorMessage = 'Unexpected response format from gateway';
                if (!$paywayResponse) {
                    $errorMessage = 'Gateway returned empty response';
                } elseif (isset($paywayResponse['message'])) {
                    $errorMessage = $paywayResponse['message'];
                } elseif (isset($paywayResponse['error'])) {
                    $errorMessage = $paywayResponse['error'];
                }

                return $this->errorResponse('Payment gateway error', 502, [
                    'detail'  => $errorMessage,
                    'raw'     => $paywayResponse,
                ]);
            }

            $payment = Payment::create([
                'order_id'    => $order->id,
                'provider'    => 'payway_khpay',
                'reference'   => $tranId,
                'md5'         => $paywayResponse['md5_hash'] ?? null,
                'status'      => 'pending',
                'khqr_string' => $paywayResponse['qr_string'] ?? '',
            ]);

            $cart->cart_items()->delete();

            DB::commit();

            return $this->successResponse([
                'order_id'   => $order->id,
                'payment_id' => $payment->id,
                'tran_id'    => $tranId,
                'data'       => $paywayResponse
            ], 'Checkout initiated successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('[KHPAY] PayWayCheckout exception', ['error' => $e->getMessage()]);
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function checkPayWayStatus(Request $request)
    {

        $request->validate([
            'tran_id' => 'required|string',
        ]);

        $payment = Payment::with('order')->where('reference', $request->tran_id)->first();

        if (!$payment) {
            return $this->errorResponse('Payment not found', 404);
        }

        // Log::info('payment md5', $payment);

        if (!$payment->md5) {
            return $this->errorResponse('MD5 hash not found for this payment', 404);
        }

        /** @var KHPAY $khpayService */
        $khpayService = app(KHPAY::class);
        $status = $khpayService->checkStatusByMd5($payment->md5);

        // Log::info('payment status:', $status);

        if (isset($status['status'])) {
            if (in_array($status['status'], ['SUCCESS', 'PAID'])) {
                $payment->update(['status' => 'success']);
                $payment->order->markAsPaid();
            }
            // elseif ($status['status'] == 1) {
            //     $payment->update(['status' => 'failed']);
            // }
        }

        return $this->successResponse([
            // 'payment' => $payment,
            'payway' => $status,
            'order'  => $payment->order->fresh(),
        ], 'Payment status fetched');
    }

    public function generateKHQR(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric',
            'note'   => 'nullable|string',
        ]);

        /** @var KHPAY $khpayService */
        $khpayService = app(KHPAY::class);

        $qrData = $khpayService->generateQR((float)$request->amount, $request->note);

        // if (isset($qrData['message']) || !isset($qrData['qr_string'])) {
        //     return $this->errorResponse($qrData['message'] ?? 'Failed to generate QR', 502, [
        //         'raw' => $qrData
        //     ]);
        // }

        return $this->successResponse($qrData, 'QR generated successfully');
    }
}
