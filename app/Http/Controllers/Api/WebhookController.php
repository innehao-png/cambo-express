<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\ABAService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    public function handleCallBack(Request $request)
    {
        Log::info('[ABA Callback] received', $request->all());

        $tran_id = $request->input('tran_id');

        if (!$tran_id) {
            return response()->json(['message' => 'OK']);
        }

        $order = Order::where('tran_id', $tran_id)->first();

        if (!$order) {
            return response()->json(['message' => 'OK']);
        }


        /** @var \App\Services\ABAService $abaService */
        $abaService = app(ABAService::class);
        $response = $abaService->checkTransaction($tran_id);

        // reuse the same logic from CheckoutController
        (new ABAController)->updateOrderStatus($order, $response);

        return response()->json(['message' => 'OK']);
    }
}
