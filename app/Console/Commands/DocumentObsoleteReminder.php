<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\DocumentControl;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

class DocumentObsoleteReminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:send-documentobsolete-reminder';
    protected $description = 'Send WhatsApp reminders for document obsolete';

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
        $now = Carbon::now()->format('Y-m-d');
        $this->info("Current Time: " . $now);

        // Tentukan ID grup WhatsApp yang statis
        $group_id = '120363311478624933'; // Ganti dengan Group ID Anda

        // Ambil audit control yang perlu diingatkan berdasarkan rentang waktu
        $documentControls = DocumentControl::select('document_controls.*')
            ->where('status', 'Uncomplete', 'Rejected')
            ->where('set_reminder', '<=', $now)
            ->where('obsolete', '>=', $now) // Menggunakan >= untuk termasuk hari ini
            ->get()
            ->groupBy('department'); // Grupkan berdasarkan departemen

        // Ambil dokumen yang sudah melewati tanggal review
        $documentIssues = DocumentControl::select('document_controls.*')
            ->where('status', 'Uncomplete', 'Rejected')
            ->where('obsolete', '<', $now) // Dokumen yang sudah obsolete
            ->get()
            ->groupBy('department');

        // dd($documentIssues);

        // Jika tidak ada dokumen yang perlu diingatkan atau yang sudah melewati tanggal review, hentikan proses
        if ($documentControls->isEmpty() && $documentIssues->isEmpty()) {
            $this->info("Tidak ada dokumen yang perlu diingatkan atau yang sudah melewati tanggal review.");
            return; // Berhenti jika tidak ada data
        }

        // Lanjutkan jika ada dokumen untuk diingatkan atau yang bermasalah
        $this->sendWaReminderDocument($group_id, $documentControls, $documentIssues, $now);
    }

    protected function sendWaReminderDocument($groupId, $documentsByDepartment, $issuesByDepartment, $now)
    {
        $token = 'v2n49drKeWNoRDN4jgqcdsR8a6bcochcmk6YphL6vLcCpRZdV1';

        // Format pesan utama
        $message = "--- *WARNING OBSOLETE DOCUMENT* ---\n\n";

        // Iterasi melalui setiap departemen untuk reminder
        $index = 1;
        foreach ($documentsByDepartment as $departmentName => $documentsGroup) {
            // Tambahkan nama departemen ke pesan
            $message .= "[$index] *" . $departmentName . "*\n"; // Judul untuk departemen

            // Iterasi dokumen dalam departemen
            foreach ($documentsGroup as $document) {
                // Mengambil nama dokumen dan waktu obsolete dari dokumen
                $documentName = $document->name;

                // Mengonversi string menjadi objek Carbon
                $obsoleteTime = Carbon::parse($document->obsolete);

                // Periksa apakah tanggal obsolete adalah hari ini
                if ($obsoleteTime->isToday()) {
                    $daysLeftMessage = 'Today'; // Jika tepat hari ini, tampilkan Today
                } else {
                    // Hitung rentang waktu dalam hari
                    $daysUntilObsolete = $obsoleteTime->diffInDays($now);
                    $daysLeftMessage = $daysUntilObsolete . ' days left';
                }

                // Tambahkan setiap dokumen ke dalam pesan
                $message .= "- " . $documentName . " : " . $daysLeftMessage . " ❗\n";
            }

            $message .= "\n"; // Tambahkan baris kosong setelah setiap departemen
            $index++;
        }

        // Menambahkan pesan untuk dokumen yang perlu diingatkan
        $message .= "*Please submit and verify to MS Department*\n\n";

        // Menambahkan bagian "Dokumen issue" jika ada
        if ($issuesByDepartment->isNotEmpty()) {
            $message .= "Pending Issue :\n\n";
            $index = 1;
            foreach ($issuesByDepartment as $departmentName => $issuesGroup) {
                // Tambahkan nama departemen ke pesan
                $message .= "[$index] *" . $departmentName . "*\n"; // Judul untuk departemen

                // Iterasi dokumen yang melewati tanggal review
                foreach ($issuesGroup as $issue) {
                    // Mengambil nama dokumen dan menghitung jumlah hari yang terlewat
                    $documentName = $issue->name;
                    $obsoleteDate = Carbon::parse($issue->obsolete);

                    // Hitung jumlah hari yang sudah terlewat
                    $daysOverdue = $obsoleteDate->diffInDays($now);

                    // Periksa apakah tanggal obsolete adalah hari ini
                    if ($obsoleteDate->isToday()) {
                        continue; // Lewati dokumen ini jika obsolete adalah hari ini
                    }

                    // Tambahkan ke pesan "Dokumen issue" jika tidak hari ini
                    $message .= "- " . $documentName . " : Overdue by " . $daysOverdue . " days ❗\n";
                }

                $message .= "\n"; // Tambahkan baris kosong setelah setiap departemen
                $index++;
            }
            $message .= "*Please submit and verify to MS Department ASAP*\n\n";
        }

        $message .= "------ BY AISIN BISA ------";

        // Kirim pesan ke WhatsApp Group
        $response = Http::asForm()->post('https://app.ruangwa.id/api/send_message', [
            'token' => $token,
            'number' => $groupId,
            'message' => $message,
        ]);

        if ($response->successful()) {
            $this->info("Pesan berhasil dikirim ke $groupId: " . $response->body());

            // Update kolom `last_reminder_sent` untuk semua dokumen yang sudah diproses
            $documentsByDepartment->each(function ($documentsGroup) {
                $documentsGroup->each(function ($documentControl) {
                    $documentControl->update(['last_reminder_sent' => Carbon::now()]);
                });
            });

            // Juga update dokumen yang ada di issues
            $issuesByDepartment->each(function ($issuesGroup) {
                $issuesGroup->each(function ($documentControl) {
                    $documentControl->update(['last_reminder_sent' => Carbon::now()]);
                });
            });
        } else {
            $this->error("Gagal mengirim pesan ke $groupId. Respons: " . $response->body());
        }
    }
}
