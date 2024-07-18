<?php

namespace App\Http\Controllers;

use App\Models\Dokumen;
use App\Models\IndukDokumen;
use App\Models\RuleCode;
use App\Models\User;
use App\Notifications\DocumentStatusChanged;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use RealRashid\SweetAlert\Facades\Alert;

class ValidateRuleController extends Controller
{
    public function validate_index($jenis, $tipe)
    {
        // Ambil dokumen yang sesuai dengan jenis dan tipe dokumen dan urutkan berdasarkan tanggal upload
        $dokumen = Dokumen::where('jenis_dokumen', $jenis)
            ->where('tipe_dokumen', $tipe) // Urutkan berdasarkan tanggal upload secara menurun
            ->get();

        // Ambil induk dokumen yang sesuai dengan dokumen yang telah dipilih dan memiliki status "waiting approval"
        $indukDokumenList = IndukDokumen::whereIn('dokumen_id', $dokumen->pluck('id'))
            ->orderBy('tgl_upload', 'desc') // Pastikan ini juga diurutkan jika perlu
            ->get();

        // Ambil semua kode proses
        $kodeProses = RuleCode::all();

        // Return view dengan data yang sudah difilter
        return view('pages-rule.validasi-rule', compact('jenis', 'tipe', 'dokumen', 'indukDokumenList', 'kodeProses'));
    }
    public function approveDocument(Request $request, $id)
    {
        // Validasi input
        $request->validate([
            'comment' => 'required|string|max:255',
            'file' => 'nullable|file|mimes:pdf,doc,docx|max:2048', // File opsional, jika perlu diunggah
        ]);

        try {
            // Temukan dokumen berdasarkan ID
            $dokumen = IndukDokumen::findOrFail($id);

            // Periksa apakah status dokumen adalah "waiting approval"
            if ($dokumen->status != 'waiting approval') {
                return redirect()->back()->with('error', 'Dokumen tidak dalam status waiting approval.');
            }

            // Jika pengguna mengirimkan file draft, proses file tersebut
            if ($request->hasFile('file_draft')) {
                // Hapus file draft lama jika ada
                if ($dokumen->file_draft) {
                    Storage::disk('public')->delete($dokumen->file_draft);
                }

                $file = $request->file('file_draft');
                $filename = time() . '_' . $file->getClientOriginalName();
                Storage::disk('public')->putFileAs('draft_rule', $file, $filename);

                // Simpan path file draft ke dalam database
                $dokumen->file_draft = 'draft_rule/' . $filename;
            }

            // Lakukan perubahan status menjadi "draft approved"
            $dokumen->status = 'draft approved'; // Sesuaikan dengan kolom status di tabel IndukDokumen Anda

            // Simpan komentar yang diambil dari inputan form
            $dokumen->comment = $request->input('comment');

            // Simpan perubahan
            $dokumen->save();

            Alert::success('Success', 'Dokumen berhasil diapprove.');

            // Redirect atau kembali ke halaman sebelumnya dengan pesan sukses
            return redirect()->back();
        } catch (\Exception $e) {
            // Tangani pengecualian jika terjadi kesalahan
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
    public function activateDocument($id)
    {
        // Temukan dokumen berdasarkan ID
        $dokumen = IndukDokumen::findOrFail($id);

        // Periksa apakah dokumen belum aktif atau sudah obsolate
        if ($dokumen->statusdoc == 'not yet active' || $dokumen->statusdoc == 'obsolate') {
            $dokumen->statusdoc = 'active';
            $dokumen->comment = 'Dokumen berhasil diaktifkan.';
            $dokumen->save();

            alert::success('Dokumen berhasil diaktifkan.');

            return redirect()->back();
        }
        alert::error('Dokumen tidak dapat diaktifkan.');
        return redirect()->back();
    }

    public function obsoleteDocument($id)
    {
        // Temukan dokumen berdasarkan ID
        $dokumen = IndukDokumen::findOrFail($id);

        // Periksa apakah dokumen aktif
        if ($dokumen->statusdoc == 'active' || $dokumen->statusdoc == 'not yet active') {
            $dokumen->statusdoc = 'obsolate';
            $dokumen->comment = 'Dokumen berhasil diobsoletkan.';
            $dokumen->save();

            alert::success('Dokumen berhasil diobsoletkan.');
            return redirect()->back();
        } elseif ($dokumen->statusdoc == 'obsolate') {
            // Jika sudah obsolate, maka tidak bisa diobsoletkan kembali
            alert::error('Dokumen sudah dalam status obsolate.');
            return redirect()->back();
        }
        alert::error('Dokumen tidak dapat diobsoletkan.');
        return redirect()->back();
    }
}
