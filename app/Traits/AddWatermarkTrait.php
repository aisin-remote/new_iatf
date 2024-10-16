<?php

namespace App\Traits;

use Log;
use setasign\Fpdi\TcpdfFpdi;
use Storage;

trait AddWatermarkTrait
{
    protected function addWatermarkToPdf($filePath, $watermarkText, $textXPos, $textYPos)
    {
        // Buat direktori rule_watermark jika belum ada
        $watermarkDirectory = 'rule_watermark';
        $storagePath = storage_path('app/public/' . $watermarkDirectory);

        if (!file_exists($storagePath)) {
            mkdir($storagePath, 0755, true);
        }

        // Path lengkap file PDF asli
        $fullPath = storage_path('app/public/' . $filePath);

        if (!file_exists($fullPath)) {
            Log::error("File tidak ditemukan: $fullPath");
            return null;
        }

        // Ambil nama file tanpa ekstensi dan ekstensi file
        $fileNameWithoutExtension = pathinfo($filePath, PATHINFO_FILENAME);
        $fileExtension = pathinfo($filePath, PATHINFO_EXTENSION);

        // Buat instance TCPDF dengan FPDI
        $pdf = new TcpdfFpdi();
        $pdf->SetAutoPageBreak(false);

        $pageCount = $pdf->setSourceFile($fullPath);

        // Proses setiap halaman
        for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
            $tplId = $pdf->importPage($pageNo);
            $size = $pdf->getTemplateSize($tplId);

            // Tambahkan halaman dengan ukuran yang sama
            $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);

            // Import halaman asli
            $pdf->useTemplate($tplId, 0, 0, $size['width'], $size['height']);

            // Tambahkan watermark teks dengan transparansi abu-abu pada halaman
            $pdf->SetFont('helvetica', 'B', 72); // Ukuran font tetap 72
            $pdf->SetTextColor(150, 150, 150); // Warna abu-abu

            // Set opacity (transparansi) untuk teks watermark
            $pdf->SetAlpha(0.3); // Sesuaikan nilai opacity (0.0 sampai 1.0, di mana 1.0 adalah sepenuhnya tidak transparan)

            // Menyesuaikan posisi watermark teks
            $pdf->StartTransform();
            $pdf->Rotate(45, $textXPos, $textYPos); // Rotasi sekitar titik tengah watermark teks
            $pdf->Text($textXPos - 100, $textYPos, $watermarkText); // Gunakan variabel posisi X dan Y
            $pdf->StopTransform();

            // Kembalikan opacity ke default
            $pdf->SetAlpha(1.0);
        }

        // Tentukan path file baru dengan nama file asli dan prefix/suffix
        $watermarkedPath = $watermarkDirectory . '/' . $fileNameWithoutExtension . '_watermarked.' . $fileExtension;
        $fullWatermarkedPath = storage_path('app/public/' . $watermarkedPath);

        // Simpan file PDF yang di-watermark
        $pdf->Output($fullWatermarkedPath, 'F');

        if (!file_exists($fullWatermarkedPath)) {
            Log::error("File watermark tidak dapat disimpan: $fullWatermarkedPath");
            return null;
        }

        return $watermarkedPath;
    }
}
