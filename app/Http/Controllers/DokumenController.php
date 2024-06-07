<?php

namespace App\Http\Controllers;

use App\Models\Dokumen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DokumenController extends Controller
{
    public function upload(Request $request, $jenis, $tipe)
    {
        // Validasi input
        $request->validate([
            'file' => 'required|mimes:pdf,doc,docx|max:2048', // Format file yang diterima: pdf, doc, docx dengan maksimum ukuran 2MB
        ]);

        // Mencari dokumen berdasarkan jenis_dokumen dan tipe_dokumen
        $dokumen = Dokumen::where('jenis_dokumen', $jenis)
            ->where('tipe_dokumen', $tipe)
            ->first();

        if ($dokumen) {
            // Hapus file lama jika ada
            if (Storage::disk('public')->exists('dokumen/' . $dokumen->file)) {
                Storage::disk('public')->delete('dokumen/' . $dokumen->file);
            }

            // Simpan file yang diunggah ke storage disk
            $file = $request->file('file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            Storage::disk('public')->putFileAs('dokumen', $file, $fileName);

            // Perbarui informasi dokumen
            $dokumen->file = $fileName;
            // Tambahkan kolom-kolom lain yang sesuai dengan struktur tabel dokumen Anda
            $dokumen->save();

            return back()->with('success', 'Dokumen berhasil diperbarui.');
        } else {
            return back()->with('error', 'Dokumen tidak ditemukan.');
        }
    }

    public function download($jenis, $tipe)
    {
        // Temukan dokumen yang sesuai dengan jenis dan tipe dokumen
        $dokumen = Dokumen::where('jenis_dokumen', $jenis)
            ->where('tipe_dokumen', $tipe)
            ->first();

        // Periksa apakah dokumen ditemukan
        if ($dokumen) {
            $filePath = 'dokumen/' . $dokumen->file;

            // Jika file ditemukan, kirim file untuk diunduh
            if (Storage::disk('public')->exists($filePath)) {
                return Storage::disk('public')->download($filePath, $dokumen->file);
            } else {
                return back()->with('error', 'File tidak ditemukan di storage.');
            }
        }

        // Jika dokumen tidak ditemukan atau file tidak ada, kembalikan respons dengan pesan kesalahan
        return back()->with('error', 'Dokumen tidak ditemukan.');
    }
}
