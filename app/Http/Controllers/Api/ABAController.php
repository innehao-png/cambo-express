<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\ABAService;
use App\Services\KHPAY;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ABAController extends Controller
{
    public function updateOrderStatus(Order $order, array $response)
    {
        $statusCode = $response['status'] ?? '';

        // Update order status based on PayWay response
        if (in_array($statusCode, [0, '0'], true)) {
            if (!$order->isPaid()) {
                $order->markAsPaid();
            }

            if ($order->payment && $order->payment->status !== 'success') {
                $order->payment->update(['status' => 'success']);
            }
        } elseif (in_array($statusCode, [1, '1'], true)) {
            if ($order->status !== 'failed') {
                $order->update(['status' => 'failed']);
            }

            if ($order->payment && $order->payment->status !== 'failed') {
                $order->payment->update(['status' => 'failed']);
            }
        }
    }

    public function checkTransaction(string $tran_id)
    {
        $order = Order::where('tran_id', $tran_id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        /** @var \App\Services\ABAService $abaService */
        $abaService = app(ABAService::class);

        $response = $abaService->checkTransaction($tran_id);

        // Log::info('Check transaction response', $response);

        // dd($response);

        $this->updateOrderStatus($order, $response);

        $order->refresh();

        return $this->successResponse([
            'order'  => [
                'tran_id'      => $order->tran_id,
                'status'       => $order->status,
                'total_amount' => $order->total_amount,
                'paid_at'      => $order->paid_at,
            ],
            'payway' => $response,
        ], 'Transaction checked successfully');
    }

    public function getTransactionDetail(string $tran_id)
    {
        $order = Order::where('tran_id', $tran_id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        /** @var \App\Services\ABAService $abaService */
        $abaService = app(ABAService::class);

        $response = $abaService->getTransactionDetail($tran_id);

        return $this->successResponse($response);
    }

    public function getTransactionsList(Request $request)
    {
        $filters = array_filter($request->only([
            'from_date',
            'to_date',
            'from_amount',
            'to_amount',
            'status',
            'page',
            'pagination',
        ])); // array_filter removes null/empty values

        /** @var \App\Services\ABAService $abaService */
        $abaService = app(ABAService::class);

        $response = $abaService->getTransactionsList($filters);

        return $this->successResponse($response);
    }

    public function closeTransaction(string $tran_id)
    {
        $order = Order::where('tran_id', $tran_id)
            ->where('user_id', Auth::id())
            ->whereNot('status', 'cancelled')
            ->firstOrFail();

        /** @var \App\Services\ABAService $abaService */
        $abaService = app(ABAService::class);

        $response = $abaService->closeTransaction($tran_id);

        if ($response['status']['code'] === '00' || $response['status'] === '0') {

            $order->update(['status' => 'cancelled']);

            return $this->successResponse($response);
        }

        return $this->errorResponse($response['status']['message'], 500);
    }

    public function refundTransaction(Request $request, string $tran_id)
    {
        $request->validate([
            'amount' => ['required', 'numeric', 'min:0.01'],
        ]);

        $order = Order::where('tran_id', $tran_id)
            ->where('user_id', Auth::id())
            ->where('status', 'paid')
            ->firstOrFail();

        /** @var \App\Services\ABAService $abaService */
        $abaService = app(ABAService::class);

        $response = $abaService->refundTransaction($tran_id, (float) $request->amount);

        Log::info('[PayWay] refundTransaction response', $response);

        if (($response['status']['code'] ?? null) === '00') {
            $order->update(['status' => 'refunded']);

            return $this->successResponse($response, 'Refund processed successfully');
        }

        return $this->errorResponse(
            $response['status']['message'] ?? 'Refund failed',
            502
        );
    }

    public function getExchangeRate()
    {
        /** @var \App\Services\ABAService $abaService */
        $abaService = app(ABAService::class);

        $response = $abaService->exchangeRate();

        return $this->successResponse($response);
    }

    public function generateQr(Request $request){
        $request->validate([
            'amount' => 'required|numeric',
            'note' => 'required|string',
        ]);

        $khpay = app(KHPAY::class);
        $response = $khpay->generate($request->amount, $request->note);

        return $this->successResponse($response);
    }
}
