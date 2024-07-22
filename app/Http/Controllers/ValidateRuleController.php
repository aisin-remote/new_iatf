<?php

namespace App\Http\Controllers;

use App\Models\Dokumen;
use App\Models\IndukDokumen;
use App\Models\RuleCode;
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
        try {

            // Temukan dokumen berdasarkan ID
            $dokumen = IndukDokumen::findOrFail($id);


            // Periksa apakah status dokumen adalah "waiting approval"
            if ($dokumen->status != 'waiting approval') {
                return redirect()->back()->with('error', 'Dokumen tidak dalam status waiting approval.');
            }

            // Jika pengguna mengirimkan file, proses file tersebut
            if ($request->hasFile('file')) {
                // Hapus file draft lama jika ada
                if ($dokumen->file) {
                    Storage::disk('public')->delete($dokumen->file);
                }

                $file = $request->file('file');
                $filename = time() . '_' . $file->getClientOriginalName();
                Storage::disk('public')->putFileAs('rule', $file, $filename);

                // Simpan path file draft ke dalam database
                $dokumen->file = 'rule/' . $filename;
            }

            // Lakukan perubahan status menjadi "approved"
            $dokumen->status = 'approved'; // Sesuaikan dengan kolom status di tabel IndukDokumen Anda

            // Lakukan perubahan statusdoc menjadi ""
            $dokumen->statusdoc = 'not yet active'; // Sesuaikan dengan kolom status di tabel IndukDokumen Anda

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
    public function activateDocument(Request $request, $id)
    {
        // Temukan dokumen berdasarkan ID
        $dokumen = IndukDokumen::findOrFail($id);

        // Periksa apakah dokumen belum aktif atau sudah obsolete
        if ($dokumen->statusdoc == 'not yet active' || $dokumen->statusdoc == 'obsolete') {
            // Set status dokumen
            $dokumen->statusdoc = 'active';
            $dokumen->comment = 'Dokumen berhasil diaktifkan.';
            $dokumen->tgl_efektif = $request->input('activation_date');
            $dokumen->save();

            Alert::success('Dokumen berhasil diaktifkan.');
            return redirect()->route('document.final');
        }

        Alert::error('Dokumen tidak dapat diaktifkan.');
        return redirect()->back();
    }

    public function obsoleteDocument(Request $request, $id)
    {
        // Temukan dokumen berdasarkan ID
        $dokumen = IndukDokumen::findOrFail($id);

        // Periksa apakah dokumen aktif atau belum aktif
        if ($dokumen->statusdoc == 'active' || $dokumen->statusdoc == 'not yet active') {
            // Set status dokumen ke 'obsolete'
            $dokumen->statusdoc = 'obsolete';
            $dokumen->comment = 'Dokumen berhasil diobsoletkan.';
            $dokumen->tgl_obsolete = $request->input('obsoleted_date');
            $dokumen->save();

            Alert::success('Dokumen berhasil diobsoletkan.');
            return redirect()->back();
        } elseif ($dokumen->statusdoc == 'obsolete') {
            // Jika sudah obsolete, maka tidak bisa diobsoletkan kembali
            Alert::error('Dokumen sudah dalam status obsolete.');
            return redirect()->back();
        }

        Alert::error('Dokumen tidak dapat diobsoletkan.');
        return redirect()->back();
    }
}
