<?php

namespace Database\Seeders;

use App\Models\Dokumen;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DokumenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Dokumen::create([
            'code' => 'WI',
            'nomor_template' => 'Form WI 2024',
            'jenis_dokumen' => 'rule',
            'tipe_dokumen' => 'WI'
        ]);
        Dokumen::create([
            'code' => 'PRO',
            'nomor_template' => 'Form Prosedur 2024',
            'jenis_dokumen' => 'rule',
            'tipe_dokumen' => 'PROSEDUR'
        ]);
        Dokumen::create([
            'code' => 'WIS',
            'nomor_template' => 'Form WIS 2024',
            'jenis_dokumen' => 'rule',
            'tipe_dokumen' => 'WIS'
        ]);
        Dokumen::create([
            'code' => 'STD',
            'nomor_template' => 'Form STD 2024',
            'jenis_dokumen' => 'rule',
            'tipe_dokumen' => 'STANDAR'
        ]);
    }
}
