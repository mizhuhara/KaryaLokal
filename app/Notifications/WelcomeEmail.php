<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WelcomeEmail extends Notification implements ShouldQueue
{
    use Queueable;

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Selamat Datang di KaryaLokal!')
            ->greeting('Selamat Datang, ' . $notifiable->name . '!')
            ->line('Terima kasih telah bergabung dengan KaryaLokal.')
            ->line('Temukan kerajinan handmade terbaik dari pengrajin lokal Indonesia.')
            ->action('Jelajahi Produk', url('/products'))
            ->line('Mulai berbelanja dan dukung pengrajin lokal!');
    }
}
