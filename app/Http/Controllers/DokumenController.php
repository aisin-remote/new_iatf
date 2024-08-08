<?php

namespace App\Http\Controllers;

use App\Models\Dokumen;
use App\Models\IndukDokumen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use RealRashid\SweetAlert\Facades\Alert;
use Symfony\Contracts\Service\Attribute\Required;

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
            'tgl_efektif' => 'required',
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
            'tipe_dokumen' => $request->input('tipe_dokumen'),
            'jenis_dokumen' => 'rule',
            'tgl_efektif' => $request->input('tgl_efektif'),
            'file' => $fileName,
            'template' => $templateName,
        ]);

        Alert::success('Success', 'Template added successfully.');
        // Redirect kembali ke halaman sebelumnya dengan pesan sukses
        return redirect()->route('template.index');
    }
    public function edit(Request $request, $id)
    {
        // Validasi data input
        $request->validate([
            'nomor_template' => 'required|string|max:255',
            'tgl_efektif' => 'required',
            'file' => 'nullable|file|mimes:pdf|max:2048',
            'template' => 'nullable|file|mimes:xlsx,doc,docx|max:2048', // Sesuaikan dengan kebutuhan
        ]);

        // Cari template berdasarkan id
        $template = Dokumen::findOrFail($id);

        // Update nomor template
        $template->nomor_template = $request->nomor_template;
        $template->tgl_efektif = $request->tgl_efektif;

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
            $template->file_pdf = $fileName;
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

        Alert::success('Success', 'Template changed successfully.');

        // Redirect kembali dengan pesan sukses
        return redirect()->back();
    }
    public function preview($id)
    {
        // Cari dokumen berdasarkan ID
        $document = Dokumen::findOrFail($id);

        // Lakukan validasi atau pengecekan apakah dokumen tersedia
        if (!$document->file_pdf) {
            abort(404, 'Document not found or not available.');
        }

        // Path ke file PDF
        $filePath = storage_path('app/public/template_dokumen/' . $document->file_pdf);

        // Verifikasi apakah file ada di path yang diharapkan
        if (!file_exists($filePath)) {
            abort(404, 'Document file not found.');
        }

        // Mendapatkan nama file berdasarkan jenis_dokumen dan tipe_dokumen
        $fileName = $document->jenis_dokumen . '_' . $document->tipe_dokumen . '.pdf';

        // Tampilkan file PDF di browser untuk pratinjau
        return response()->file($filePath, ['Content-Disposition' => 'inline; filename="' . $fileName . '"']);
    }
    public function download($id)
    {
        dd($id);
        // Cari dokumen berdasarkan ID
        $document = IndukDokumen::findOrFail($id);

        // Lakukan validasi atau pengecekan apakah dokumen tersedia
        if (!$document->file) {
            abort(404, 'Document not found or not available.');
        }

        // Path ke file template
        $filePath = storage_path('app/public/draft-rule/' . $document->template);

        // Verifikasi apakah file ada di path yang diharapkan
        if (!file_exists($filePath)) {
            abort(404, 'Document file not found.');
        }

        // Mendapatkan nama file berdasarkan jenis_dokumen dan tipe_dokumen
        $fileName = $document->jenis_dokumen . '_' . $document->tipe_dokumen . '.' . pathinfo($document->template, PATHINFO_EXTENSION);

        // Kembalikan file untuk diunduh
        return response()->download($filePath, $fileName);
    }
}
