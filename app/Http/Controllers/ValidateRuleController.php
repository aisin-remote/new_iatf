<?php

namespace App\Http\Controllers;

use App\Models\Departemen;
use App\Models\Dokumen;
use App\Models\IndukDokumen;
use App\Models\RuleCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use RealRashid\SweetAlert\Facades\Alert;
use setasign\Fpdi\Fpdi;
use setasign\Fpdi\Tcpdf\Fpdi as TcpdfFpdi;

class ValidateRuleController extends Controller
{
    public function validate_index($jenis, $tipe)
    {
        // Ambil dokumen yang sesuai dengan jenis dan tipe dokumen dan urutkan berdasarkan tanggal upload
        $dokumen = Dokumen::where('jenis_dokumen', $jenis)
            ->where('tipe_dokumen', $tipe)
            ->get();

        // Ambil induk dokumen yang sesuai dengan dokumen yang telah dipilih dan memiliki status "waiting approval"
        $indukDokumenList = IndukDokumen::whereIn('dokumen_id', $dokumen->pluck('id'))
            ->orderBy('tgl_upload', 'desc')
            ->get();

        // Ambil semua kode proses
        $kodeProses = RuleCode::all();

        // Ambil semua departemen
        $allDepartemen = Departemen::all();

        // Return view dengan data yang sudah difilter
        return view('pages-rule.validasi-rule', compact('jenis', 'tipe', 'dokumen', 'indukDokumenList', 'kodeProses', 'allDepartemen'));
    }
    public function approveDocument(Request $request, $id)
    {
        try {

            // Temukan dokumen berdasarkan ID
            $dokumen = IndukDokumen::findOrFail($id);


            // Periksa apakah status dokumen adalah "waiting approval"
            if ($dokumen->status != 'Waiting check by MS') {
                return redirect()->back()->with('error', 'Dokumen tidak dalam status waiting approval.');
            }

            // Jika pengguna mengirimkan file, proses file tersebut
            if ($request->hasFile('file')) {
                // Hapus file draft lama jika ada
                if ($dokumen->file) {
                    Storage::disk('public')->delete($dokumen->file);
                }

                $file = $request->file('file');
                $filename = time() . '_' . $file->getClientOriginalName();
                Storage::disk('public')->putFileAs('rule', $file, $filename);

                // Simpan path file draft ke dalam database
                $dokumen->file = 'rule/' . $filename;
            }

            // Lakukan perubahan status menjadi "approved"
            $dokumen->status = 'Finish check by MS'; // Sesuaikan dengan kolom status di tabel IndukDokumen Anda

            // Lakukan perubahan statusdoc menjadi ""
            $dokumen->statusdoc = 'not yet active'; // Sesuaikan dengan kolom status di tabel IndukDokumen Anda

            // Simpan komentar yang diambil dari inputan form
            $dokumen->comment = $request->input('comment');

            // Simpan perubahan
            $dokumen->save();

            Alert::success('Success', 'Finish check by MS.');

            // Redirect atau kembali ke halaman sebelumnya dengan pesan sukses
            return redirect()->back();
        } catch (\Exception $e) {
            // Tangani pengecualian jika terjadi kesalahan
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
    public function uploadFinal(Request $request, $id)
    {
        // Validasi file hanya bisa PDF
        $request->validate([
            'file' => 'required|mimes:pdf|max:10240',
        ], [
            'file.mimes' => 'Only PDF files are allowed.',
        ]);

        // Ambil dokumen berdasarkan ID
        $doc = IndukDokumen::findOrFail($id);

        // Simpan file di folder final-rule
        $file = $request->file('file');
        $filename = time() . '_' . $file->getClientOriginalName();
        $path = 'final-rule/' . $filename;
        $file->storeAs('final-rule', $filename, 'public');

        // Update path file di database
        $doc->file_pdf = $path;
        $doc->save();

        // Tampilkan pesan sukses
        return redirect()->back()->with('success', 'Dokumen berhasil diunggah.');
    }
    public function activateDocument(Request $request, $id)
    {
        // Temukan dokumen berdasarkan ID
        $dokumen = IndukDokumen::findOrFail($id);

        // Periksa apakah dokumen belum aktif atau sudah obsolete
        if ($dokumen->statusdoc == 'not yet active' || $dokumen->statusdoc == 'obsolete') {
            // Tambahkan watermark pada PDF jika kolom pdf_file tidak null
            if (!is_null($dokumen->file_pdf) && Storage::disk('public')->exists($dokumen->file_pdf)) {
                $watermarkedPath = $this->addWatermarkToPdf($dokumen->file_pdf, 'Controlled Copy');

                // Simpan path file yang sudah di-watermark ke kolom active_doc
                $dokumen->active_doc = $watermarkedPath;
            }

            // Set status dokumen
            $dokumen->statusdoc = 'active';
            $dokumen->status = 'Approve by MS';
            $dokumen->comment = 'Dokumen berhasil diaktifkan.';
            $dokumen->tgl_efektif = $request->input('activation_date');
            $dokumen->save();

            Alert::success('Dokumen berhasil diaktifkan.');
            return redirect()->back();
        }

        Alert::error('Dokumen tidak dapat diaktifkan.');
        return redirect()->back();
    }
    protected function addWatermarkToPdf($filePath, $watermarkText)
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

        // Path gambar watermark
        $watermarkImagePath = storage_path('app/public/stamp_controlled_copy.png');

        if (!file_exists($watermarkImagePath)) {
            Log::error("Gambar watermark tidak ditemukan: $watermarkImagePath");
            return null;
        }

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

            // Tambahkan watermark gambar kecil di kiri bawah halaman
            $imageWidth = 36; // Lebar gambar watermark
            $imageHeight = 36; // Tinggi gambar watermark
            $xPos = 10; // Posisi X dari kiri
            $yPos = $size['height'] - $imageHeight - 10; // Posisi Y dari bawah (10mm dari bagian bawah)

            // Menggunakan metode "Image" untuk menempatkan gambar
            $pdf->Image($watermarkImagePath, $xPos, $yPos, $imageWidth, $imageHeight, 'PNG');

            // Tambahkan watermark teks dengan transparansi abu-abu pada halaman
            $pdf->SetFont('helvetica', 'B', 72); // Ukuran font tetap 72
            $pdf->SetTextColor(150, 150, 150); // Warna abu-abu

            // Set opacity (transparansi) untuk teks watermark
            $pdf->SetAlpha(0.3); // Sesuaikan nilai opacity (0.0 sampai 1.0, di mana 1.0 adalah sepenuhnya tidak transparan)

            // Menyesuaikan posisi watermark teks
            $textXPos = 20; // Posisi X untuk teks
            $textYPos = 150; // Posisi Y untuk teks tetap

            $pdf->StartTransform();
            $pdf->Rotate(45, $textXPos + 50, $textYPos); // Rotasi sekitar titik tengah watermark teks
            $pdf->Text($textXPos, $textYPos, $watermarkText); // Posisi teks dengan rotasi
            $pdf->StopTransform();

            // Kembalikan opacity ke default
            $pdf->SetAlpha(1.0);
        }

        // Tentukan path file baru
        $watermarkedPath = $watermarkDirectory . '/watermarked_' . uniqid() . '.pdf';
        $fullWatermarkedPath = storage_path('app/public/' . $watermarkedPath);

        // Simpan file PDF yang di-watermark
        $pdf->Output($fullWatermarkedPath, 'F');

        if (!file_exists($fullWatermarkedPath)) {
            Log::error("File watermark tidak dapat disimpan: $fullWatermarkedPath");
            return null;
        }

        return $watermarkedPath;
    }
    public function previewsAndDownload(Request $request, $id)
    {
        // Ambil dokumen berdasarkan ID
        $doc = IndukDokumen::findOrFail($id);

        // Cek apakah ada file PDF
        if (!$doc->file_pdf) {
            return redirect()->back()->with('error', 'File tidak ditemukan.');
        }

        // Jika permintaan adalah untuk mengunduh file
        if ($request->input('action') === 'download') {
            return Storage::disk('public')->download($doc->file_pdf);
        }

        // Menampilkan pratinjau PDF
        $filePath = storage_path('app/public/' . $doc->file_pdf);
        if (!file_exists($filePath)) {
            return redirect()->back()->with('error', 'File tidak ditemukan.');
        }

        return response()->file($filePath);
    }
    public function downloadWatermarkedDocument($id)
    {
        // Temukan dokumen berdasarkan ID
        $dokumen = IndukDokumen::findOrFail($id);

        // Periksa apakah dokumen memiliki file yang sudah di-watermark
        if (is_null($dokumen->active_doc) || !Storage::disk('public')->exists($dokumen->active_doc)) {
            Alert::error('File watermark tidak ditemukan.');
            return redirect()->back();
        }

        $path = storage_path('app/public/' . $dokumen->active_doc);
        $headers = [
            'Content-Type' => 'application/pdf',
        ];

        // Unduh file yang sudah di-watermark
        return response()->download($path, 'watermarked_' . $dokumen->nomor_dokumen . '.pdf', $headers);
    }
    public function obsoleteDocument(Request $request, $id)
    {
        // Temukan dokumen berdasarkan ID
        $dokumen = IndukDokumen::findOrFail($id);

        // Periksa apakah dokumen aktif atau belum aktif
        if ($dokumen->statusdoc == 'active' || $dokumen->statusdoc == 'not yet active') {
            // Set status dokumen ke 'obsolete'
            $dokumen->statusdoc = 'obsolete';
            $dokumen->comment = 'Dokumen berhasil diobsoletkan.';
            $dokumen->tgl_obsolete = $request->input('obsoleted_date');
            $dokumen->save();

            Alert::success('Dokumen berhasil diobsoletkan.');
            return redirect()->back();
        } elseif ($dokumen->statusdoc == 'obsolete') {
            // Jika sudah obsolete, maka tidak bisa diobsoletkan kembali
            Alert::error('Dokumen sudah dalam status obsolete.');
            return redirect()->back();
        }

        Alert::error('Dokumen tidak dapat diobsoletkan.');
        return redirect()->back();
    }
}
