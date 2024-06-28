<?php

namespace App\Http\Controllers;

use App\Models\Dokumen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DokumenController extends Controller
{
    public function index()
    {
        $dokumen = Dokumen::all();
        return view('dokumen', compact('dokumen'));
    }

    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'jenis_dokumen' => 'required',
            'tipe_dokumen' => 'required',
            'file' => 'required|mimes:pdf,doc,docx|max:2048', // Format file yang diterima: pdf, doc, docx dengan maksimum ukuran 2MB
            'nomor_template' => 'required|string|max:255', // Validasi untuk nomor_template
        ]);

        // Mendapatkan data dari request
        $jenis = $request->input('jenis_dokumen');
        $tipe = $request->input('tipe_dokumen');
        $nomorTemplate = $request->input('nomor_template');

        // Simpan file yang diunggah ke storage disk
        $file = $request->file('file');
        $fileName = time() . '_' . $file->getClientOriginalName();
        Storage::disk('public')->putFileAs('template_dokumen', $file, $fileName);

        // Perbarui informasi dokumen
        $dokumen = new Dokumen();
        $dokumen->jenis_dokumen = $jenis;
        $dokumen->tipe_dokumen = $tipe;
        $dokumen->file = $fileName;
        $dokumen->nomor_template = $nomorTemplate; // Tambahkan nomor_template
        // Tambahkan kolom-kolom lain yang sesuai dengan struktur tabel dokumen Anda
        $dokumen->save();

        return back()->with('success', 'Dokumen have been added.');
    }

    public function edit(Request $request, $id)
    {
        // Validasi input
        $request->validate([
            'file' => 'required|mimes:pdf,doc,docx|max:2048', // Format file yang diterima: pdf, doc, docx dengan maksimum ukuran 2MB
            'nomor_template' => 'required|string|max:255', // Validasi untuk nomor_template
        ]);

        // Mencari dokumen berdasarkan ID
        $dokumen = Dokumen::find($id);

        if ($dokumen) {
            // Hapus file lama jika ada
            if (Storage::disk('public')->exists('template_dokumen/' . $dokumen->file)) {
                Storage::disk('public')->delete('template_dokumen/' . $dokumen->file);
            }

            // Simpan file yang diunggah ke storage disk
            $file = $request->file('file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            Storage::disk('public')->putFileAs('template_dokumen', $file, $fileName);

            // Perbarui informasi dokumen
            $dokumen->file = $fileName;
            $dokumen->nomor_template = $request->input('nomor_template'); // Perbarui nomor_template

            // Simpan perubahan ke database
            $dokumen->save();

            return back()->with('success', 'Dokumen berhasil diperbarui.');
        } else {
            return back()->with('error', 'Dokumen tidak ditemukan.');
        }
    }
    public function download($id)
    {
        // Temukan dokumen yang sesuai dengan jenis dan tipe dokumen
        $dokumen = Dokumen::findOrFail($id);
        $filePath = 'template_dokumen/' . $dokumen->file;

        if (Storage::disk('public')->exists($filePath)) {
            // Mendapatkan ukuran file dan memeriksa keberadaan file
            try {
                $fileSize = Storage::disk('public')->size($filePath);
            } catch (\Exception $e) {
                return back()->with('error', 'Tidak dapat mengambil ukuran file: ' . $e->getMessage());
            }

            // Membuat nama file yang diunduh dengan format yang diinginkan
            $fileExtension = pathinfo($filePath, PATHINFO_EXTENSION);
            $fileName = $dokumen->jenis_dokumen . '_' . $dokumen->tipe_dokumen . '.' . $fileExtension;

            // Jika file ditemukan, kirim file untuk diunduh
            return Storage::disk('public')->download($filePath, $fileName);
        } else {
            return back()->with('error', 'File tidak ditemukan di storage.');
        }
    }
}
