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
        ]);

        // Mendapatkan jenis_dokumen dan tipe_dokumen dari request
        $jenis = $request->input('jenis_dokumen');
        $tipe = $request->input('tipe_dokumen');

        // Simpan file yang diunggah ke storage disk
        $file = $request->file('file');
        $fileName = time() . '_' . $file->getClientOriginalName();
        Storage::disk('public')->putFileAs('dokumen', $file, $fileName);

        // Perbarui informasi dokumen
        $dokumen =  new Dokumen();
        $dokumen->jenis_dokumen = $jenis;
        $dokumen->tipe_dokumen = $tipe;
        $dokumen->file = $fileName;
        // Tambahkan kolom-kolom lain yang sesuai dengan struktur tabel dokumen Anda
        $dokumen->save();

        return back()->with('success', 'Dokumen have been added.');
    }

    public function edit(Request $request, $id)
    {
        // Validasi input
        $request->validate([
            'file' => 'required|mimes:pdf,doc,docx|max:2048', // Format file yang diterima: pdf, doc, docx dengan maksimum ukuran 2MB
        ]);

        // Mencari dokumen berdasarkan ID
        $dokumen = Dokumen::find($id);

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
        $filePath = $dokumen->file;
        $filePath = 'dokumen/' . $dokumen->file;
        if (Storage::disk('public')->exists($filePath)) {
            // Membuat nama file yang diunduh dengan format yang diinginkan
            $fileExtension = pathinfo($filePath, PATHINFO_EXTENSION);
            $fileName = $dokumen->Template . '_' . $dokumen->jenis_dokumen . '_' . $dokumen->tipe_dokumen . '.' . $fileExtension;
            // Jika file ditemukan, kirim file untuk diunduh
            return Storage::disk('public')->download($filePath, $fileName);
    } else {
        return back()->with('error', 'File tidak ditemukan di storage.');
    }
        }
    }

