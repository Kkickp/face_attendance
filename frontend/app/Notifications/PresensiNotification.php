<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\DatabaseMessage;
use Illuminate\Notifications\Messages\MailMessage;

class PresensiNotification extends Notification
{
    public function __construct(
        public string $judul,
        public string $pesan
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->subject($this->judul)
                    ->line($this->pesan)
                    ->action('Lihat Dashboard', url('/dashboard'))
                    ->line('Ini adalah pesan otomatis dari sistem.');
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'judul' => $this->judul,
            'pesan' => $this->pesan,
        ];
    }
}
