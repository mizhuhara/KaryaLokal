<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentSuccess extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Order $order)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Pembayaran Berhasil - ' . $this->order->order_number)
            ->greeting('Pembayaran Berhasil!')
            ->line('Pembayaran untuk pesanan **' . $this->order->order_number . '** telah berhasil diterima.')
            ->line('Total: **Rp ' . number_format($this->order->total_amount, 0, ',', '.') . '**')
            ->action('Lihat Pesanan', url('/orders'))
            ->line('Pesanan Anda sedang diproses oleh penjual.');
    }
}
