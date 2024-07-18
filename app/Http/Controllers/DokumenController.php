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
        $dokumen = Dokumen::orderBy('updated_at', 'desc')->get();

        return view('dokumen', compact('dokumen'));
    }

    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'nomor_template' => 'required|string|max:255',
            'jenis_dokumen' => 'required|string|in:Rule,Process', // Sesuaikan dengan jenis dokumen yang tersedia
            'tipe_dokumen' => 'required|string|max:255',
            'file' => 'required|mimes:pdf|max:2048',
            'template' => 'required|mimes:xlsx,doc,docx|max:2048', // Format file yang diterima: pdf, doc, docx dengan maksimum ukuran 2MB
        ]);

        // Simpan file yang diunggah ke storage disk
        $file = $request->file('file');
        $fileName = time() . '_file_' . $file->getClientOriginalName();
        $file->storeAs('template_dokumen', $fileName, 'public');

        // Simpan template yang diunggah ke storage disk
        $template = $request->file('template');
        $templateName = time() . '_template_' . $template->getClientOriginalName();
        $template->storeAs('template_dokumen', $templateName, 'public');

        // Buat entry baru dalam database untuk template dokumen
        Dokumen::create([
            'nomor_template' => $request->input('nomor_template'),
            'jenis_dokumen' => $request->input('jenis_dokumen'),
            'tipe_dokumen' => $request->input('tipe_dokumen'),
            'file' => $fileName,
            'template' => $templateName,
        ]);

        Alert::success('Success', 'Template berhasil ditambahkan.');
        // Redirect kembali ke halaman sebelumnya dengan pesan sukses
        return redirect()->route('template.index');
    }
    public function edit(Request $request, $id)
    {
        // Validasi data input
        $request->validate([
            'nomor_template' => 'required|string|max:255',
            'file' => 'nullable|file|mimes:pdf|max:2048',
            'template' => 'nullable|file|mimes:xlsx,doc,docx|max:2048', // Sesuaikan dengan kebutuhan
        ]);

        // Cari template berdasarkan id
        $template = Dokumen::findOrFail($id);

        // Update nomor template
        $template->nomor_template = $request->nomor_template;

        // Jika ada file yang diupload
        if ($request->hasFile('file')) {
            // Simpan file yang diunggah ke storage disk
            $file = $request->file('file');
            $fileName = time() . '_file_' . $file->getClientOriginalName();
            $file->storeAs('template_dokumen', $fileName, 'public');

            // Hapus file lama jika ada
            if ($template->file) {
                Storage::disk('public')->delete('template_dokumen/' . $template->file);
            }

            // Update path file pada template
            $template->file = $fileName;
        }

        // Jika ada template yang diupload
        if ($request->hasFile('template')) {
            // Simpan template yang diunggah ke storage disk
            $templateFile = $request->file('template');
            $templateName = time() . '_template_' . $templateFile->getClientOriginalName();
            $templateFile->storeAs('template_dokumen', $templateName, 'public');

            // Hapus template lama jika ada
            if ($template->template) {
                Storage::disk('public')->delete('template_dokumen/' . $template->template);
            }

            // Update path template pada template
            $template->template = $templateName;
        }

        // Simpan perubahan
        $template->save();

        Alert::success('Success', 'Template berhasil diubah.');

        // Redirect kembali dengan pesan sukses
        return redirect()->back();
    }
    public function preview($id)
    {
        // Cari dokumen berdasarkan ID
        $document = Dokumen::findOrFail($id);

        // Lakukan validasi atau pengecekan apakah dokumen tersedia
        if (!$document->file) {
            abort(404, 'Dokumen tidak ditemukan atau tidak tersedia.');
        }

        // Path ke file PDF
        $filePath = storage_path('app/public/template_dokumen/' . $document->file);

        // Verifikasi apakah file ada di path yang diharapkan
        if (!file_exists($filePath)) {
            abort(404, 'File dokumen tidak ditemukan.');
        }

        // Mendapatkan nama file berdasarkan jenis_dokumen dan tipe_dokumen
        $fileName = $document->jenis_dokumen . '_' . $document->tipe_dokumen . '.pdf';

        // Tampilkan file PDF di browser untuk pratinjau
        return response()->file($filePath, ['Content-Disposition' => 'inline; filename="' . $fileName . '"']);
    }
    public function download($id)
    {
        // Cari dokumen berdasarkan ID
        $document = Dokumen::findOrFail($id);

        // Lakukan validasi atau pengecekan apakah dokumen tersedia
        if (!$document->template) {
            abort(404, 'Dokumen tidak ditemukan atau tidak tersedia.');
        }

        // Path ke file PDF
        $filePath = storage_path('app/public/template_dokumen/' . $document->template);

        // Verifikasi apakah file ada di path yang diharapkan
        if (!file_exists($filePath)) {
            abort(404, 'File dokumen tidak ditemukan.');
        }

        // Mendapatkan nama file berdasarkan jenis_dokumen dan tipe_dokumen
        $fileName = $document->jenis_dokumen . '_' . $document->tipe_dokumen . '.pdf';

        // Kembalikan file untuk diunduh
        return response()->download($filePath, $fileName);
    }
}
