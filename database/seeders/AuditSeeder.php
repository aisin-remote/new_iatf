<?php

namespace Database\Seeders;

use App\Models\Audit;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AuditSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Audit::create([
            'nama' => 'ISO 9001 & IATF (Sistem Management Mutu)'
        ]);
        Audit::create([
            'nama' => 'ISO 14001 & ISO 45001 (Sistem Management Lingkungan dan K3)'
        ]);
        Audit::create([
            'nama' => 'AGC & AFC (Astra Green Company & Astra Friendly Company)'
        ]);
    }
}
