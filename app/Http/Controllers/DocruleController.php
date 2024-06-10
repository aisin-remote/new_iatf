<?php

namespace App\Http\Controllers;

use App\Models\Departemen;
use App\Models\Dokumen;
use App\Models\IndukDokumen;
use App\Models\RuleCode;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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

        $kodeProses = RuleCode::all();

        return view('pages-rule.dokumen-rule', compact('jenis', 'tipe', 'dokumen', 'jenisDokumen', 'tipeDokumen', 'kodeProses'));
    }

    public function store(Request $request)
    {
        // Validasi data yang diterima dari form
        $request->validate([
            'nama_dokumen' => 'required',
            'rule_id' => 'required',
            'file' => 'required|file',
            'jenis_dokumen' => 'required',
            'tipe_dokumen' => 'required',
        ]);

        // Mulai transaksi database
        DB::beginTransaction();

        try {
            // Mendapatkan user ID dari user yang sedang login
            $userId = auth()->id();

            // Cari atau buat ID dokumen berdasarkan jenis dan tipe yang dipilih
            $dokumen = Dokumen::firstOrCreate(
                ['jenis_dokumen' => $request->jenis_dokumen, 'tipe_dokumen' => $request->tipe_dokumen],
                ['other_attributes' => 'values'] // Tambahkan atribut lain yang diperlukan untuk membuat dokumen baru
            );

            // Menghasilkan nomor dokumen
            $nomorDokumen = IndukDokumen::generateNomorDokumen($request->tipe_dokumen, $userId, $request->rule_id, 0);

            // Mengambil file yang di-upload
            $file = $request->file('file');

            // Menyimpan file ke direktori yang diinginkan
            $filePath = $file->store('dokumen');

            // Menyimpan data dokumen ke tabel induk_dokumen
            $dokumenInduk = new IndukDokumen([
                'nama_dokumen' => $request->nama_dokumen,
                'user_id' => $userId,
                'dokumen_id' => $dokumen->id,
                'rule_id' => $request->rule_id,
                'tgl_upload' => now(),
                'revisi_log' => 0,
                'file' => $filePath,
                'status' => 'Waiting',
                'nomor_dokumen' => $nomorDokumen
            ]);
            $dokumenInduk->save();

            // Commit transaksi
            DB::commit();

            // Redirect atau kembali ke halaman yang sesuai
            return redirect()->route('rule.index', ['tipe' => $request->tipe_dokumen])->with('success', 'Dokumen berhasil diunggah.');
        } catch (\Exception $e) {
            // Rollback transaksi jika terjadi kesalahan
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
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
