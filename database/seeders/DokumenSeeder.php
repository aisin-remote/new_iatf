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
            'jenis_dokumen' => 'rule',
            'tipe_dokumen' => 'WI'
        ]);
        Dokumen::create([
            'jenis_dokumen' => 'rule',
            'tipe_dokumen' => 'PROSEDUR'
        ]);
        Dokumen::create([
            'jenis_dokumen' => 'rule',
            'tipe_dokumen' => 'WIS'
        ]);
        Dokumen::create([
            'jenis_dokumen' => 'rule',
            'tipe_dokumen' => 'STANDAR'
        ]);
        Dokumen::create([
            'jenis_dokumen' => 'proses',
            'tipe_dokumen' => 'FMEA'
        ]);
        Dokumen::create([
            'jenis_dokumen' => 'proses',
            'tipe_dokumen' => 'APQP'
        ]);
        Dokumen::create([
            'jenis_dokumen' => 'proses',
            'tipe_dokumen' => 'QCPC'
        ]);
        Dokumen::create([
            'jenis_dokumen' => 'proses',
            'tipe_dokumen' => 'Checksheet'
        ]);
    }
}
