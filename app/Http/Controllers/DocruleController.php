<?php

namespace App\Http\Controllers;

use App\Models\Departemen;
use App\Models\Dokumen;
use App\Models\IndukDokumen;
use App\Models\RuleCode;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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

        $kodeProses = RuleCode::pluck('nama_proses', 'kode_proses');

        return view('pages-rule.dokumen-rule', compact('jenis', 'tipe', 'dokumen', 'jenisDokumen', 'tipeDokumen', 'kodeProses'));
    }


    public function store(Request $request)
    {
        // Validasi data yang diterima dari form
        $request->validate([
            'nama_dokumen' => 'required',
            'rule_id' => 'required',
            'file' => 'required|file',
        ]);

        // Mendapatkan user ID, misalnya dari session atau data user yang login
        $userId = auth()->id(); // Sesuaikan dengan cara Anda mendapatkan user ID

        // Mendapatkan id departemen dari user
        $user = User::find($userId);
        if (!$user) {
            return redirect()->back()->with('error', 'User tidak ditemukan.');
        }
        $idDepartemen = $user->departemen_id;

        // Mendapatkan nama departemen berdasarkan id departemen
        $departemen = Departemen::find($idDepartemen);
        if (!$departemen) {
            return redirect()->back()->with('error', 'Departemen tidak ditemukan.');
        }
        $namaDepartemen = $departemen->nama_departemen;

        // Mendapatkan tipe dokumen dari document_id
        $document = RuleCode::find($request->rule_id);
        if (!$document) {
            return redirect()->back()->with('error', 'RuleDoc tidak ditemukan.');
        }
        $tipe = $document->tipe_dokumen;
        $kodeProses = $document->kode_proses;

        // Menghasilkan nomor dokumen berformat
        $nomorDokumen = $tipe . '-' . $namaDepartemen . '-' . $kodeProses . '-' . IndukDokumen::count() + 1;

        // Mengambil file yang di-upload
        $file = $request->file('file');

        // Menyimpan file ke direktori yang diinginkan
        $filePath = $file->store('dokumen');

        // Menyimpan data dokumen ke database
        $dokumen = new Dokumen();
        $dokumen->nama_dokumen = $request->nama_dokumen;
        $dokumen->dokumen_id = $request->document_id; // Misalnya, dokumen_id diambil dari input form document_id
        $dokumen->rule_id = $request->rule_id; // Misalnya, rule_id diambil dari input form rule_id
        $dokumen->tgl_upload = now(); // Menggunakan timestamp saat ini sebagai tanggal upload
        $dokumen->file = $filePath; // Misalnya, atribut file diisi dengan path file yang disimpan
        $dokumen->status = 'Waiting'; // Misalnya, status awal dokumen adalah 'Aktif'
        $dokumen->nomor_dokumen = $nomorDokumen;
        $dokumen->user_id = $userId;
        $dokumen->save();

        // Redirect atau kembali ke halaman yang sesuai
        return redirect()->route('rule.index', ['tipe' => $tipe])->with('success', 'Dokumen berhasil diunggah.');
    }


    // public function download($id)
    // {
    //     $dokumen = IndukDokumen::findOrFail($id);
    //     $filePath = $dokumen->file;

    //     if (Storage::disk('public')->exists($filePath)) {
    //         return Storage::disk('public')->download($filePath);
    //     } else {
    //         return redirect()->back()->with('error', 'File not found.');
    //     }
    // }

    // public function update(Request $request, $id)
    // {
    //     $request->validate([
    //         'nama_dokumen' => 'required|string|max:255',
    //         'file' => 'nullable|file|max:10240', // Optional file update, max 10MB
    //     ]);

    //     $dokumen = IndukDokumen::findOrFail($id);
    //     $dokumen->nama_dokumen = $request->nama_dokumen;

    //     $dokumen->revisi_log++;

    //     if ($request->hasFile('file')) {
    //         // Delete the old file if it exists
    //         if (!is_null($dokumen->file) && Storage::disk('public')->exists($dokumen->file)) {
    //             Storage::disk('public')->delete($dokumen->file);
    //         }

    //         // Store the new file
    //         $filePath = $request->file('file')->store('dokumen-files', 'public');
    //         $dokumen->file = $filePath;
    //     }

    //     $dokumen->save();

    //     return redirect()->back()->with('success', 'Dokumen berhasil diperbarui.');
    // }
}
