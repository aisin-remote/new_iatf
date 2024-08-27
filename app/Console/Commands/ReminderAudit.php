<?php

namespace App\Console\Commands;

use App\Models\Audit;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ReminderAudit extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'wa:send-reminder';
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
    
    // Tentukan group_id di sini
    $group_id = '120363311478624933'; // Ganti dengan Group ID Anda
    
    // Ambil audit yang perlu diingatkan berdasarkan rentang waktu
    $auditControls = Audit::where('reminder', '<=', $now)
        ->where('duedate', '>=', $now)
        ->get();

    foreach ($auditControls as $auditControl) {
        // Mengirimkan pengingat WhatsApp menggunakan group_id
        $this->sendWaReminderAudit($group_id);
        
        // Update status atau lakukan sesuatu untuk menandai bahwa reminder sudah dikirim
        // $auditControl->update(['reminder_audit' => 0]);
    }
}
    
protected function sendWaReminderAudit($groupId)
{
    // Send WA notification
    $token = 'v2n49drKeWNoRDN4jgqcdsR8a6bcochcmk6YphL6vLcCpRZdV1';
    $message = "------ REMINDER DOCUMENT CONTROL ------\n\n\n------ BY AISIN BISA ------"; // Menggunakan \n untuk newline

    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => 'https://app.ruangwa.id/api/send_message',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => http_build_query([
            'token' => $token,
            'number' => $groupId, // Pastikan ini adalah ID grup yang benar atau nomor WhatsApp
            'message' => $message,
        ]),
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/x-www-form-urlencoded',
        ],
    ]);

    $response = curl_exec($curl);
    $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);

    // Log the response or handle errors
    if ($httpCode == 200) {
        $this->info("Pesan berhasil dikirim: $response");
    } else {
        $this->error("Gagal mengirim pesan. Respons: $response");
    }
}
}
