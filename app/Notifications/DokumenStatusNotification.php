<?php

namespace App\Notifications;

use App\Models\IndukDokumen;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class DokumenStatusNotification extends Notification
{
    use Queueable;

    protected $dokumen;

    public function __construct($dokumen)
    {
        $this->dokumen = $dokumen;
    }

    public function via($notifiable)
    {
        return ['database']; // Menggunakan notifikasi database
    }

    public function toDatabase($notifiable)
    {
        return [
            'dokumen_id' => $this->dokumen->id,
            'nama_dokumen' => $this->dokumen->nama_dokumen,
            'status' => $this->dokumen->status,
        ];
    }
}
