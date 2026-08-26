<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\PaymentService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function callback(Request $request, Order $order)
    {
        $statusCode = $request->query('status_code') ?? $request->input('status_code');
        $transactionStatus = $request->query('transaction_status') ?? $request->input('transaction_status');

        if (!$statusCode) {
            return response()->json(['error' => 'No status code'], 400);
        }

        $paymentService = new PaymentService();
        $paymentService->handleCallback($order->order_number, $statusCode, $request->all());

        // Redirect back to order page
        if (auth()->check()) {
            return redirect()->route('buyer.orders')
                ->with('success', 'Pembayaran berhasil diproses!');
        }

        return redirect('/');
    }

    public function notification(Request $request)
    {
        $payload = $request->json()->all();

        $orderId = $payload['order_id'] ?? null;
        $statusCode = $payload['status_code'] ?? '400';

        if (!$orderId) {
            return response()->json(['error' => 'No order_id'], 400);
        }

        $paymentService = new PaymentService();
        $paymentService->handleCallback($orderId, $statusCode, $payload);

        return response()->json(['status' => 'ok']);
    }
}
