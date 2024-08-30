<?php

namespace App\Console\Commands;

use App\Models\Audit;
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

        // Ambil audit yang perlu diingatkan berdasarkan rentang waktu
        $auditControls = Audit::where('reminder', '<=', $now)
            ->where('duedate', '>=', $now)
            ->get();

        foreach ($auditControls as $auditControl) {
            // Mengirimkan pengingat WhatsApp menggunakan group_id
            $this->sendWaReminderAudit($group_id, $auditControl);
        }
    }

    protected function sendWaReminderAudit($groupId, $auditControl)
    {
        $token = 'v2n49drKeWNoRDN4jgqcdsR8a6bcochcmk6YphL6vLcCpRZdV1';
        $message = "------ REMINDER AUDIT CONTROL ------\n\n";
        $message .= "Audit Name: " . $auditControl->nama . "\n";
        $message .= "Reminder Date: " . $auditControl->reminder. "\n";
        $message .= "Due Date: " . $auditControl->duedate. "\n";
        $message .= "\n------ BY AISIN BISA ------";

        $response = Http::asForm()->post('https://app.ruangwa.id/api/send_message', [
            'token' => $token,
            'number' => $groupId,
            'message' => $message,
        ]);

        if ($response->successful()) {
            $this->info("Pesan berhasil dikirim ke $groupId: " . $response->body());
            // Update kolom `last_reminder_sent` untuk auditControl
            $auditControl->update(['last_reminder_sent' => Carbon::now()]);
        } else {
            $this->error("Gagal mengirim pesan ke $groupId. Respons: " . $response->body());
        }
    }
}
