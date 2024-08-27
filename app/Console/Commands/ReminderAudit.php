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
    // public function handle()
    // {
    //     $today = Carbon::now();
    //     $dayOfWeek = $today->dayOfWeek;

    //     if ($dayOfWeek == 1 || $dayOfWeek == 5) { // Senin (1) dan Jumat (5)
    //         // Tentukan ID grup WhatsApp yang dituju
    //         $groupId = '120363311478624933@g.us'; // Ganti dengan ID grup Anda
    //         $this->sendWaReminderAudit($groupId);
    //     }
    // }
    public function handle()
    {
        // Langsung memanggil pengiriman WA tanpa cek hari
        $groupId = '120363311478624933@g.us'; // Ganti dengan ID grup Anda
        $this->sendWaReminderAudit($groupId);
    }
    
    protected function sendWaReminderAudit($groupId)
    {
        // Send WA notification
        $token = 'v2n49drKeWNoRDN4jgqcdsR8a6bcochcmk6YphL6vLcCpRZdV1';
        $message = sprintf("------ AIIA HENKATEN ALERT ------ %c%c%c------ BY AISIN BISA ------", 10, 10, 10, 10, 10, 10, 10, 10, 10, 10, 10, 10);

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
                'number' => $groupId,
                'message' => $message,
            ]),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/x-www-form-urlencoded'
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
