<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class PaymentService
{
    protected $clientKey;
    protected $serverKey;
    protected $isProduction;
    protected $apiUrl;

    public function __construct()
    {
        $this->clientKey = config('midtrans.client_key');
        $this->serverKey = config('midtrans.server_key');
        $this->isProduction = config('midtrans.is_production', false);
        $this->apiUrl = $this->isProduction
            ? 'https://api.midtrans.com'
            : 'https://api.sandbox.midtrans.com';
    }

    public function createTransaction(Order $order): array
    {
        $orderId = $order->order_number;

        $params = [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => (int) $order->total_amount,
            ],
            'customer_details' => [
                'first_name' => $order->user->name,
                'email' => $order->user->email,
                'phone' => $order->user->phone ?? '-',
            ],
            'item_details' => $order->items->map(function ($item) {
                return [
                    'id' => $item->product_id,
                    'price' => (int) $item->price,
                    'quantity' => $item->quantity,
                    'name' => $item->product->name,
                ];
            })->toArray(),
            'callbacks' => [
                'finish' => url("/orders/{$order->id}/payment/callback"),
            ],
        ];

        if ($order->delivery_type === 'delivery') {
            $params['customer_details']['address'] = $order->delivery_address ?? '';
        }

        $response = Http::withBasicAuth($this->serverKey, '')
            ->post("{$this->apiUrl}/v1/payment-links", $params);

        $data = $response->json();

        if ($response->successful()) {
            Payment::create([
                'order_id' => $order->id,
                'transaction_id' => $data['id'] ?? $orderId,
                'status' => 'pending',
                'amount' => $order->total_amount,
                'response_data' => $data,
            ]);

            return [
                'success' => true,
                'payment_url' => $data['payment_url'] ?? null,
                'data' => $data,
            ];
        }

        return [
            'success' => false,
            'error' => $data['error_messages'] ?? ['Gagal membuat transaksi'],
        ];
    }

    public function handleCallback(string $orderId, string $statusCode, array $data): void
    {
        $order = Order::where('order_number', $orderId)->first();

        if (!$order) {
            return;
        }

        $payment = $order->payments()->latest()->first();

        if (!$payment) {
            return;
        }

        $statusMap = [
            '200' => 'success',
            '201' => 'success',
            '400' => 'failed',
            '401' => 'failed',
            '404' => 'failed',
        ];

        $paymentStatus = $statusMap[$statusCode] ?? 'failed';

        $payment->update([
            'status' => $paymentStatus,
            'paid_at' => $paymentStatus === 'success' ? now() : null,
            'payment_method' => $data['payment_type'] ?? null,
            'response_data' => array_merge($payment->response_data ?? [], $data),
        ]);

        if ($paymentStatus === 'success') {
            $order->update(['status' => 'confirmed']);
        }
    }

    public function getClientKey(): string
    {
        return $this->clientKey;
    }

    public function getOrderId($orderId): string
    {
        return $orderId;
    }
}
