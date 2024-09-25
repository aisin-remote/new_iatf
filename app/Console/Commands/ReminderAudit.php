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
            ->join('item_audit', 'audit_control.item_audit_id', '=', 'item_audit.id')
            ->join('audit', 'item_audit.audit_id', '=', 'audit.id')
            ->where('audit.reminder', '<=', $now)
            ->where('audit.duedate', '>=', $now)
            ->with(['itemAudit.audit']) // Eager load itemAudit dan audit
            ->get()
            ->groupBy('item_audit_id'); // Kelompokkan berdasarkan item_audit_id

        foreach ($auditControls as $itemAuditId => $auditControlsGroup) {
            // Mengirimkan pengingat WhatsApp menggunakan group_id
            $this->sendWaReminderAudit($group_id, $itemAuditId, $auditControlsGroup);
        }
    }

    protected function sendWaReminderAudit($groupId, $itemAuditId, $auditControls)
    {
        $token = 'v2n49drKeWNoRDN4jgqcdsR8a6bcochcmk6YphL6vLcCpRZdV1';
        $message = "------ WARNING SUBMIT DOCUMENT ------\n\n";

        // Ambil item audit yang terkait dengan itemAuditId
        $itemAudit = $auditControls->first()->itemAudit;

        if ($itemAudit) {
            // Mengambil nama audit melalui itemAudit
            $auditName = $itemAudit->audit ? $itemAudit->audit->nama : 'N/A';
            $dueDate = $itemAudit->audit ? $itemAudit->audit->duedate : 'N/A';

            $message .= "Audit Name: " . $auditName . "\n";
            $message .= "Due Date: " . $dueDate . "\n\n";

            // Ambil semua audit control untuk item_audit_id tertentu dan kelompokkan berdasarkan departemen
            $departemenData = $auditControls->groupBy('departemen_id');

            // Inisialisasi variabel untuk mengumpulkan informasi
            $departemenMessages = [];

            foreach ($departemenData as $departemenId => $auditControls) {
                $departemen = $auditControls->first()->departemen; // Ambil departemen dari salah satu audit control

                if ($departemen) {
                    $totalTasks = $auditControls->count();
                    $completedTasks = $auditControls->where('status', 'completed')->count();
                    $documentnotCompleted = $auditControls->where('status', 'not completed', null)->count();

                    $departemenName = $departemen->nama_departemen;
                    $departemenMessages[] = "Departemen: " . $departemenName . "\n" .
                        "Total Tasks: " . $totalTasks . "\n" .
                        "Completed Tasks: " . $completedTasks . "\n" .
                        "Document not completed: " . $documentnotCompleted . "\n";
                }
            }

            if (empty($departemenMessages)) {
                $message .= "No item audit data available.\n";
            } else {
                $message .= implode("\n", $departemenMessages);
            }
        } else {
            // Jika itemAudit tidak ada
            $message .= "No item audit data available.\n";
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
