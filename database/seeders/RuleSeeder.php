<?php

namespace Database\Seeders;

use App\Models\RuleCode;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        RuleCode::create([
            'kode_proses' => 'M1',
            'nama_proses' => 'Business Plan'
        ]);
        RuleCode::create([
            'kode_proses' => 'M2',
            'nama_proses' => 'Management Review'
        ]);
        RuleCode::create([
            'kode_proses' => 'M3',
            'nama_proses' => 'Internal Audit'
        ]);
        RuleCode::create([
            'kode_proses' => 'M4',
            'nama_proses' => 'Tindakan Perbaikan dan Pencegahan'
        ]);
        RuleCode::create([
            'kode_proses' => 'M5',
            'nama_proses' => 'Perbaikan Berkesinambungan (QCC, SS)'
        ]);
        RuleCode::create([
            'kode_proses' => 'M7',
            'nama_proses' => 'Risk Assesment'
        ]);
        RuleCode::create([
            'kode_proses' => 'P1',
            'nama_proses' => 'Perencanaan Mutu (Pembuatan APQP)'
        ]);
        RuleCode::create([
            'kode_proses' => 'P2',
            'nama_proses' => 'Perubahan Proses / Produk'
        ]);
        RuleCode::create([
            'kode_proses' => 'P3',
            'nama_proses' => 'Perencanaan Produksi'
        ]);
        RuleCode::create([
            'kode_proses' => 'P4',
            'nama_proses' => 'Penanganan Peralatan Milik Customer'
        ]);
        RuleCode::create([
            'kode_proses' => 'P6',
            'nama_proses' => 'Proses Pemesanan, Penerimaan & Penyimpanan'
        ]);
        RuleCode::create([
            'kode_proses' => 'P7',
            'nama_proses' => 'Proses Produksi Unit Plant & Body'
        ]);
        RuleCode::create([
            'kode_proses' => 'P8',
            'nama_proses' => 'Palletizing, Packaging, Penyimpanan Produk dan Delivery'
        ]);
        RuleCode::create([
            'kode_proses' => 'P9',
            'nama_proses' => 'Pengendalian Barang Tidak Sesuai'
        ]);
        RuleCode::create([
            'kode_proses' => 'P10',
            'nama_proses' => 'Pengendalian Produk NG'
        ]);
        RuleCode::create([
            'kode_proses' => 'P11',
            'nama_proses' => 'Penanganan Claim (Customer & Warranty Claim)'
        ]);
        RuleCode::create([
            'kode_proses' => 'S8',
            'nama_proses' => 'Pengendalian Dokumen & Data'
        ]);
        RuleCode::create([
            'kode_proses' => 'S9',
            'nama_proses' => 'Tooling Management'
        ]);
        RuleCode::create([
            'kode_proses' => 'S10',
            'nama_proses' => 'Corrective Maintenance'
        ]);
        RuleCode::create([
            'kode_proses' => 'S11',
            'nama_proses' => 'Predictive & Preventive Maintenance'
        ]);
        RuleCode::create([
            'kode_proses' => 'S12',
            'nama_proses' => 'Pengendalian Alat Ukur, Uji & Lab'
        ]);
        RuleCode::create([
            'kode_proses' => 'S13',
            'nama_proses' => 'IT Infrastructure'
        ]);

        RuleCode::create([
            'kode_proses' => 'S15',
            'nama_proses' => 'Penerapan dan Operasional S&E'
        ]);
        RuleCode::create([
            'kode_proses' => 'S16',
            'nama_proses' => 'Contingency Plan'
        ]);
    }
}
