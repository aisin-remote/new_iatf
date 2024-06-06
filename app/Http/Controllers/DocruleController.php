<?php

namespace App\Http\Controllers;

use App\Models\Dokumen;
use App\Models\IndukDokumen;
use Illuminate\Http\Request;

class DocruleController extends Controller
{
    public function index($jenis, $tipe)
    {
        $jenisDokumen = ['rule', 'proses',];

        $tipeDokumen = [
            'rule' => ['WI', 'WIS', 'Standar', 'prosedur'],
            'proses' => ['tipeA', 'tipeB', 'tipeC'],
            
        ];

        $dokumen = Dokumen::where('jenis_dokumen', $jenis)
            ->where('tipe_dokumen', $tipe)
            ->get();

        return view('pages-rule.dokumen-rule', compact('jenis', 'tipe', 'dokumen', 'jenisDokumen', 'tipeDokumen'));
    }


    public function store(Request $request)
    {
        // Validasi request
        $request->validate([
            'nama_dokumen' => 'required',
            'file' => 'required|file',
            'tipe_dokumen' => 'required|in:prosedur,WI,WIS,Standar',
        ]);

        // Ambil file yang diunggah
        $file = $request->file('file');

        // Generate nama file unik dengan timestamp
        $filename = time() . '_' . $file->getClientOriginalName();

        // Tentukan path penyimpanan file
        $path = 'dokumen-files/' . $filename;

        // Simpan file di penyimpanan publik
        $file->storeAs('dokumen-files', $filename, 'public');

        // Tentukan tipe dokumen dari request
        $tipe = $request->tipe_dokumen;

        // Ambil ID dari jenis dokumen yang sesuai dari tabel referensi
        $jenisDokumenID = Dokumen::where('jenis', 'rule')->value('id');

        // Generate nomor dokumen berdasarkan tipe
        $nomor_dokumen = Dokumenrule::generateNomorDokumen($tipe);

        // Cari dokumen dengan nama file yang sama
        $existingDocument = Dokumenrule::where('file', $path)->first();

        // Tetapkan revisi log
        $revisi_log = $existingDocument ? $existingDocument->revisi_log + 1 : 0;

        // Buat instance Dokumenrule dan isi propertinya
        $dokumen = new Dokumenrule();
        $dokumen->nama_dokumen = $request->nama_dokumen;
        $dokumen->tipe_dokumen = $tipe;
        $dokumen->jenis_dokumen = $jenisDokumenID; // Atur ID jenis dokumen
        $dokumen->file = $path;
        $dokumen->revisi_log = $revisi_log;
        $dokumen->nomor_dokumen = $nomor_dokumen;
        $dokumen->save();

        // Redirect dengan pesan sukses
        return redirect()->route('admin-lihat-dokumen-prosedur', ['tipe' => $tipe])->with('success', 'Dokumen berhasil diunggah.');
    }

    public function download($id)
    {
        $dokumen = Dokumenrule::findOrFail($id);
        $filePath = $dokumen->file;

        if (Storage::disk('public')->exists($filePath)) {
            return Storage::disk('public')->download($filePath);
        } else {
            return redirect()->back()->with('error', 'File not found.');
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_dokumen' => 'required|string|max:255',
            'file' => 'nullable|file|max:10240', // Optional file update, max 10MB
        ]);

        $dokumen = Dokumenrule::findOrFail($id);
        $dokumen->nama_dokumen = $request->nama_dokumen;

        $dokumen->revisi_log++;

        if ($request->hasFile('file')) {
            // Delete the old file if it exists
            if (!is_null($dokumen->file) && Storage::disk('public')->exists($dokumen->file)) {
                Storage::disk('public')->delete($dokumen->file);
            }

            // Store the new file
            $filePath = $request->file('file')->store('dokumen-files', 'public');
            $dokumen->file = $filePath;
        }

        $dokumen->save();

        return redirect()->back()->with('success', 'Dokumen berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $dokumen = Dokumenrule::findOrFail($id);

        // Delete the file from storage
        if (Storage::disk('public')->exists($dokumen->file)) {
            Storage::disk('public')->delete($dokumen->file);
        }

        // Delete the document record from the database
        $dokumen->delete();

        return redirect()->back()->with('success', 'Dokumen berhasil dihapus.');
    }
}
