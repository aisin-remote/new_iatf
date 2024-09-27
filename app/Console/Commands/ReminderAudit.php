<?php

namespace App\Console\Commands;

use App\Models\Audit;
use App\Models\AuditControl;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class ReminderAudit extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:send-audit-reminder';
    protected $description = 'Send WhatsApp reminders for upcoming audits';

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
        $auditControls = AuditControl::select('audit_control.*')
            ->join('audit', 'audit_control.audit_id', '=', 'audit.id') // Relasi langsung ke audit
            ->where('audit.reminder', '<=', $now)
            ->where('audit.duedate', '>=', $now)
            ->with(['audit', 'departemen', 'itemAudit']) // Load relasi audit, departemen, dan item_audit
            ->get()
            ->groupBy('audit_id'); // Kelompokkan berdasarkan item_audit_id

        foreach ($auditControls as $auditId  => $auditControlsGroup) {
            // Mengirimkan pengingat WhatsApp menggunakan group_id
            $this->sendWaReminderAudit($group_id, $auditId, $auditControlsGroup);
        }
    }

    protected function sendWaReminderAudit($groupId, $auditId, $auditControls)
    {
        $token = 'v2n49drKeWNoRDN4jgqcdsR8a6bcochcmk6YphL6vLcCpRZdV1';

        // Ambil audit yang terkait dengan audit_id
        $audit = $auditControls->first()->audit;

        if ($audit) {
            // Mengambil nama audit dan due date dari audit
            $auditName = $audit->nama;
            $dueDate = $audit->duedate;

            // Format pesan untuk nama audit dan due date
            $message = "------ *WARNING SUBMIT DOCUMENT AUDIT* ------\n\n";
            $message .= "*Audit Name: " . $auditName . "*\n";
            $message .= "Due Date: " . $dueDate . "\n\n";

            // Mengelompokkan audit controls berdasarkan departemen
            $departemenData = $auditControls->groupBy('departemen_id');

            // Inisialisasi variabel untuk mengumpulkan informasi per departemen
            $departemenMessages = [];
            $counter = 1; // Inisialisasi nomor urut departemen

            foreach ($departemenData as $departemenId => $auditControlsForDepartemen) {
                $departemen = $auditControlsForDepartemen->first()->departemen;

                if ($departemen) {
                    // Hitung total task, completed task, dan uncompleted task per departemen
                    $totalTasks = $auditControlsForDepartemen->count();
                    $completedTasks = $auditControlsForDepartemen->where('status', 'completed')->count();
                    $uncompletedTasks = $auditControlsForDepartemen->where('status', 'uncompleted')->count() +
                        $auditControlsForDepartemen->whereNull('status')->count();

                    // Cek apakah ada uncompleted task
                    $statusSymbol = $uncompletedTasks > 0 ? "❌" : "✅"; // Tanda silang jika ada uncompleted task, tanda centang jika semua selesai

                    // Buat pesan per departemen dengan nomor urut dan status task
                    $departemenName = $departemen->nama_departemen;
                    $departemenMessages[] = "[" . $counter . "] *Departemen: " . $departemenName . "* " . $statusSymbol . "\n" .
                        "- Total Tasks: " . $totalTasks . "\n" .
                        "- Completed Tasks: " . $completedTasks . "\n" .
                        "- Incomplete Tasks: " . $uncompletedTasks;

                    // Tampilkan daftar item_audit yang uncompleted atau null
                    if ($uncompletedTasks > 0) {
                        $uncompletedItems = $auditControlsForDepartemen->where('status', 'not completed')
                            ->merge($auditControlsForDepartemen->whereNull('status'));

                        $departemenMessages[] .= "\n";

                        foreach ($uncompletedItems as $item) {
                            $itemAuditName = $item->itemAudit->nama_item ?? 'No Item Name'; // Nama item audit
                            $departemenMessages[] .= "        - " . $itemAuditName . "\n"; // Hapus nomor, hanya tampilkan item dengan "-"
                        }
                    }

                    $departemenMessages[] .= "\n"; // Pisahkan tiap departemen dengan baris baru
                    $counter++; // Tambahkan nomor urut untuk departemen berikutnya
                }
            }

            // Gabungkan semua pesan per departemen
            if (empty($departemenMessages)) {
                $message .= "No data available for departments.\n";
            } else {
                $message .= implode("", $departemenMessages); // Tidak ada tambahan newline di antara pesan
            }
        } else {
            // Jika audit tidak ada
            $message .= "No audit data available.\n";
        }

        $message .= "\n------ BY AISIN BISA ------";


        // Kirim pesan sekali dengan informasi yang terkumpul
        $response = Http::asForm()->post('https://app.ruangwa.id/api/send_message', [
            'token' => $token,
            'number' => $groupId,
            'message' => $message,
        ]);

        if ($response->successful()) {
            $this->info("Pesan berhasil dikirim ke $groupId: " . $response->body());
            // Update kolom `last_reminder_sent` untuk auditControl
            $auditControls->each(function ($auditControl) {
                $auditControl->update(['last_reminder_sent' => Carbon::now()]);
            });
        } else {
            $this->error("Gagal mengirim pesan ke $groupId. Respons: " . $response->body());
        }
    }
}
