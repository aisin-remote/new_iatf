<?php

namespace App\Http\Controllers;

use App\Models\Dokumen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use RealRashid\SweetAlert\Facades\Alert;


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
            'nomor_template' => 'required|string|max:255',
            'jenis_dokumen' => 'required|string|in:Rule,Process', // Sesuaikan dengan jenis dokumen yang tersedia
            'tipe_dokumen' => 'required|string|max:255',
            'file' => 'required|mimes:pdf,doc,docx|max:2048', // Format file yang diterima: pdf, doc, docx dengan maksimum ukuran 2MB
        ]);

        // Simpan file yang diunggah ke storage disk
        $file = $request->file('file');
        $fileName = time() . '_' . $file->getClientOriginalName();
        $file->storeAs('template_dokumen', $fileName, 'public');

        // Buat entry baru dalam database untuk template dokumen
        Dokumen::create([
            'nomor_template' => $request->input('nomor_template'),
            'jenis_dokumen' => $request->input('jenis_dokumen'),
            'tipe_dokumen' => $request->input('tipe_dokumen'),
            'file' => $fileName,
        ]);

        // Redirect kembali ke halaman sebelumnya dengan pesan sukses
        return back()->with('success', 'Template berhasil ditambahkan.');
    }

    public function edit(Request $request, $id)
    {
        // dd($request);
        // Validasi data input
        // $request->validate([
        //     'nomor_template' => 'required|string|max:255',
        //     'file' => 'nullable|file|mimes:pdf,doc,docx|max:2048', // Sesuaikan dengan kebutuhan
        // ]);

        // Cari template berdasarkan id
        $template = Dokumen::findOrFail($id);

        // Update nomor template
        $template->nomor_template = $request->nomor_template;

        // Jika ada file yang diupload
        if ($request->hasFile('file')) {
            // Simpan file yang diunggah ke storage disk
            $file = $request->file('file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('template_dokumen', $fileName, 'public');

            // Hapus file lama jika ada
            if ($template->file) {
                Storage::disk('public')->delete('template_dokumen/' . $template->file);
            }

            // Update path file pada template
            $template->file = $fileName;
        }

        // Simpan perubahan
        $template->save();

        // Redirect kembali dengan pesan sukses
        return redirect()->back()->with('success', 'Template berhasil diupdate!');
    }

    public function download($id)
    {
        // Temukan dokumen yang sesuai dengan id
        $dokumen = Dokumen::findOrFail($id);

        // Periksa apakah file ada dalam database
        if (!$dokumen->file) {
            return back()->with('error', 'File tidak ada dalam database.');
        }

        $filePath = 'template_dokumen/' . $dokumen->file;

        if (Storage::disk('public')->exists($filePath)) {
            // Mendapatkan ukuran file
            try {
                $fileSize = Storage::disk('public')->size($filePath);
            } catch (\Exception $e) {
                return back()->with('error', 'File tidak ditemukan di storage.');
            }

            // Membuat nama file yang diunduh dengan format yang diinginkan
            $fileExtension = pathinfo($filePath, PATHINFO_EXTENSION);
            $fileName = $dokumen->jenis_dokumen . '_' . $dokumen->tipe_dokumen . '.' . $fileExtension;

            // Jika file ditemukan, kirim file untuk diunduh
            return Storage::disk('public')->download($filePath, $fileName);
        } else {
            // File tidak ditemukan, kembalikan ke halaman sebelumnya dengan pesan error
            return back()->with('error', 'File tidak ditemukan di storage.');
        }
    }
}
