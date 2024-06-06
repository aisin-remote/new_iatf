<?php

namespace App\Http\Controllers;

use App\Models\Dokumen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DokumenController extends Controller
{
    public function upload(Request $request, $jenisDokumen, $tipeDokumen)
    {
        // Validasi input
        $request->validate([
            'file' => 'required|mimes:pdf,doc,docx|max:2048', // Format file yang diterima: pdf, doc, docx dengan maksimum ukuran 2MB
        ]);

        // Simpan file yang diunggah ke storage disk
        $file = $request->file('file');
        $fileName = time() . '_' . $file->getClientOriginalName();
        Storage::disk('public')->putFileAs('dokumen', $file, $fileName);

        // Simpan informasi dokumen ke dalam tabel dengan jenis dan tipe dokumen terkait
        Dokumen::create([
            'nama_file' => $fileName,
            'jenis_dokumen' => $jenisDokumen,
            'tipe_dokumen' => $tipeDokumen,
            // Tambahkan kolom-kolom lain yang sesuai dengan struktur tabel dokumen Anda
        ]);

        return back()->with('success', 'Dokumen berhasil diunggah.');
    }
    public function download($jenisDokumen, $tipeDokumen)
    {
        // Temukan dokumen yang sesuai dengan jenis dan tipe dokumen
        $dokumen = Dokumen::where('jenis_dokumen', $jenisDokumen)
            ->where('tipe_dokumen', $tipeDokumen)
            ->first();

        // Periksa apakah dokumen ditemukan
        if ($dokumen) {
            $filePath = 'uploads/' . $dokumen->nama_file;

            // Jika file ditemukan, kirim file untuk diunduh
            if (Storage::disk('public')->exists($filePath)) {
                return Storage::disk('public')->download($filePath, $dokumen->nama_file);
            }
        }

        // Jika dokumen tidak ditemukan atau file tidak ada, kembalikan respons dengan pesan kesalahan
        return back()->with('error', 'Dokumen tidak ditemukan.');
    }
}
