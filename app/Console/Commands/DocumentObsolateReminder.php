<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\DocumentControl;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

class DocumentObsolateReminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:send-documentobsolate-reminder';
    protected $description = 'Send WhatsApp reminders for document obsolate';

    /**
     * The console command description.
     *
     * @var string
     */
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = Carbon::now('Asia/Jakarta');
        $this->info("Current Time: " . $now);

        // Tentukan ID grup WhatsApp yang statis
        $group_id = '120363311478624933'; // Ganti dengan Group ID Anda

        // Ambil audit control yang perlu diingatkan berdasarkan rentang waktu
        $documentControls = DocumentControl::select('document_controls.*')
            ->where('status', 'Uncomplete')
            ->where('set_reminder', '<=', $now)
            ->where('obsolete', '>=', $now)
            ->get()
            ->groupBy('department'); // Grupkan berdasarkan departemen

        $this->sendWaReminderDocument($group_id, $documentControls);
    }

    protected function sendWaReminderDocument($groupId, $documentsByDepartment)
    {
        $token = 'v2n49drKeWNoRDN4jgqcdsR8a6bcochcmk6YphL6vLcCpRZdV1';

        // Format pesan utama
        $message = "--- *WARNING DOCUMENT OBSOLATE* ---\n\n";

        // Iterasi melalui setiap departemen
        $index = 1;
        foreach ($documentsByDepartment as $departmentName => $documentsGroup) {
            // Tambahkan nama departemen ke pesan
            $message .= "[$index] *" . $departmentName . "*❌\n"; // Judul untuk departemen

            // Iterasi dokumen dalam departemen
            foreach ($documentsGroup as $document) {
                // Mengambil nama document dan waktu obsolete dari document
                $documentName = $document->name;

                // Mengonversi string menjadi objek Carbon
                $obsoleteTime = Carbon::parse($document->obsolete);
                $setReminderTime = Carbon::parse($document->set_reminder);

                // Hitung rentang waktu dalam hari
                $daysUntilObsolete = $obsoleteTime->diffInDays($setReminderTime);

                // Tambahkan setiap dokumen ke dalam pesan
                $message .= "- " . $documentName . " : " . $daysUntilObsolete . " days left\n";
            }

            $message .= "\n"; // Tambahkan baris kosong setelah setiap departemen
            $index++;
        }
        $message .= "*Please submit and verify to MS Department*\n\n";

        $message .= "------ BY AISIN BISA ------";

        // Kirim pesan sekali untuk semua departemen dan dokumen
        $response = Http::asForm()->post('https://app.ruangwa.id/api/send_message', [
            'token' => $token,
            'number' => $groupId,
            'message' => $message,
        ]);

        if ($response->successful()) {
            $this->info("Pesan berhasil dikirim ke $groupId: " . $response->body());
            // Update kolom `last_reminder_sent` untuk semua dokumen di setiap grup departemen
            $documentsByDepartment->each(function ($documentsGroup) {
                $documentsGroup->each(function ($documentControl) {
                    $documentControl->update(['last_reminder_sent' => Carbon::now()]);
                });
            });
        } else {
            $this->error("Gagal mengirim pesan ke $groupId. Respons: " . $response->body());
        }
    }
}
