<?php

namespace App\Exports;

use App\Models\IndukDokumen;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class IndukDokumenExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    protected $dokumen;

    public function __construct($dokumen)
    {
        $this->dokumen = $dokumen;
    }

    public function collection()
    {
        return $this->dokumen->map(function ($dok) {
            return [
                $dok->id,
                $dok->nomor_dokumen,
                $dok->nama_dokumen,
                $dok->dokumen->jenis_dokumen, // Akses jenis dokumen dari relasi
                $dok->dokumen->tipe_dokumen,  // Akses tipe dokumen dari relasi
                $dok->rule->kode_proses,
                $dok->tgl_upload,
                $dok->revisi_log,
                $dok->getDepartemenTersebar(), // Misalnya, metode untuk mendapatkan departemen tersebar
            ];
        });
    }

    public function headings(): array
    {
        return [
            'ID',
            'Nomor Dokumen',
            'Nama Dokumen',
            'Jenis Dokumen',
            'Tipe Dokumen',
            'Kode Proses',
            'Tanggal Upload',
            'Revisi Log',
            'Departemen Tersebar',
        ];
    }

    protected function getDepartemenTersebar($item)
    {
        if ($item->documentDepartements) {
            return $item->documentDepartements->pluck('departemen.nama_departemen')->implode(', ');
        }

        return ''; // Atau nilai default lainnya jika tidak ada documentDepartements
    }
}
