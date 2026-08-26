<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderConfirmed extends Notification implements ShouldQueue
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
            ->subject('Pesanan ' . $this->order->order_number . ' Dikonfirmasi')
            ->greeting('Halo ' . $notifiable->name . '!')
            ->line('Pesanan Anda dengan nomor **' . $this->order->order_number . '** telah dikonfirmasi oleh penjual.')
            ->line('Total: **Rp ' . number_format($this->order->total_amount, 0, ',', '.') . '**')
            ->action('Lihat Pesanan', url('/orders'))
            ->line('Terima kasih telah berbelanja di KaryaLokal!');
    }
}
