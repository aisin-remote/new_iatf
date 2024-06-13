<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DokumenMasukNotification extends Notification
{
    use Queueable;
    private $docId;

    /**
     * Create a new notification instance.
     */
    public function __construct($dokumen)
    {
        $this->dokumen = $dokumen;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->line('Dokumen baru telah diunggah: ' . $this->dokumen->nama_dokumen)
                    ->action('Lihat Dokumen', url('/dokumen/' . $this->dokumen->id))
                    ->line('Terima kasih telah menggunakan aplikasi kami!');

    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'nama_dokumen' => $this->dokumen->nama_dokumen,
            'id_dokumen' => $this->dokumen->id,
            'tgl_upload' => $this->dokumen->tgl_upload,
        ];
    }
}
