<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderStatusChanged extends Notification implements ShouldQueue
{
    use Queueable;

    protected $statusMessages = [
        'confirmed' => 'Pesanan dikonfirmasi oleh penjual.',
        'processing' => 'Pesanan sedang diproses.',
        'ready' => 'Pesanan siap diambil/dikirim.',
        'shipped' => 'Pesanan telah dikirim.',
        'completed' => 'Pesanan telah selesai.',
        'cancelled' => 'Pesanan dibatalkan.',
        'rejected' => 'Pesanan ditolak oleh penjual.',
    ];

    public function __construct(public Order $order)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $message = $this->statusMessages[$this->order->status] ?? 'Status pesanan diperbarui.';

        return (new MailMessage)
            ->subject('Status Pesanan Diperbarui - ' . $this->order->order_number)
            ->greeting('Halo ' . $notifiable->name . '!')
            ->line($message)
            ->line('Nomor Pesanan: **' . $this->order->order_number . '**')
            ->line('Status: **' . ucfirst($this->order->status) . '**')
            ->action('Lihat Pesanan', url('/orders'));
    }
}
