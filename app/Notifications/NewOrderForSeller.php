<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewOrderForSeller extends Notification implements ShouldQueue
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
            ->subject('Pesanan Baru di ' . $this->order->seller->shop_name)
            ->greeting('Halo ' . $this->order->seller->shop_name . '!')
            ->line('Anda menerima pesanan baru dari **' . $this->order->user->name . '**.')
            ->line('Nomor Pesanan: **' . $this->order->order_number . '**')
            ->line('Total: **Rp ' . number_format($this->order->total_amount, 0, ',', '.') . '**')
            ->line('Metode: ' . ($this->order->delivery_type === 'pickup' ? 'Ambil di Toko' : 'Pengiriman'))
            ->action('Kelola Pesanan', url('/seller/orders'))
            ->line('Segera konfirmasi pesanan ini.');
    }
}
