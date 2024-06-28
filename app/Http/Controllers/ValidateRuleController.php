<?php

namespace App\Http\Controllers;

use App\Models\Dokumen;
use App\Models\IndukDokumen;
use App\Models\RuleCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ValidateRuleController extends Controller
{
    public function validate_index($jenis, $tipe)
    {
        // Ambil dokumen yang sesuai dengan jenis dan tipe dokumen
        $dokumen = Dokumen::where('jenis_dokumen', $jenis)
            ->where('tipe_dokumen', $tipe)
            ->get();

        // Ambil induk dokumen yang sesuai dengan dokumen yang telah dipilih dan memiliki status "waiting approval"
        $indukDokumenList = IndukDokumen::whereIn('dokumen_id', $dokumen->pluck('id'))
            ->where('status', 'waiting approval')
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
            'file_draft' => 'nullable|file|mimes:pdf,doc,docx|max:2048', // File opsional, jika perlu diunggah
        ]);

        // Temukan dokumen berdasarkan ID
        $dokumen = IndukDokumen::findOrFail($id);

        // Periksa apakah status dokumen adalah "waiting approval"
        if ($dokumen->status != 'waiting approval') {
            return redirect()->back()->with('error', 'Dokumen tidak dalam status waiting approval.');
        }

        // Jika pengguna mengirimkan file draft, proses file tersebut
        if ($request->hasFile('file_draft')) {
            $file = $request->file('file_draft');
            $filename = time() . '_' . $file->getClientOriginalName();
            Storage::disk('public')->putFileAs('dokumen_rule', $file, $filename);

            // Simpan path file draft ke dalam database
            $dokumen->file_draft = 'dokumen_rule/' . $filename;
        }

        // Lakukan perubahan status menjadi "draft approved"
        $dokumen->status = 'draft approved'; // Sesuaikan dengan kolom status di tabel IndukDokumen Anda

        // Simpan komentar yang diambil dari inputan form
        $dokumen->comment = $request->input('comment');

        // Simpan perubahan
        $dokumen->save();

        // Redirect atau kembali ke halaman sebelumnya dengan pesan sukses
        return redirect()->back()->with('success', 'Dokumen berhasil diapprove.');
    }

    // public function RejectedDocument(Request $request, $id)
    // {
    //     // Temukan dokumen berdasarkan ID
    //     $dokumen = IndukDokumen::findOrFail($id);

    //     // Validasi input comment
    //     $request->validate([
    //         'comment' => 'required|string|max:255',
    //     ]);

    //     if ($dokumen->status != 'waiting approval') {
    //         return redirect()->back()->with('error', 'Dokumen tidak dalam status waiting approval.');
    //     }

    //     // Lakukan perubahan status menjadi "rejected"
    //     $dokumen->status = 'draft rejected'; // Sesuaikan dengan kolom status di tabel IndukDokumen Anda

    //     // Simpan comment dari input form ke kolom comment
    //     $dokumen->comment = 'Your draft "' . $dokumen->nama_dokumen . '" has been rejected. ' . $request->input('comment');

    //     // Simpan perubahan
    //     $dokumen->save();

    //     // Redirect atau kembali ke halaman sebelumnya dengan pesan sukses
    //     return redirect()->back()->with('success', 'Dokumen berhasil direject. Tolong upload kembali dokumen yang benar.');
    // }

    public function Validate_final($jenis, $tipe)
    {
        $dokumen = Dokumen::where('jenis_dokumen', $jenis)
            ->where('tipe_dokumen', $tipe)
            ->get();

        // Ambil induk dokumen yang sesuai dengan dokumen yang telah dipilih
        $indukDokumenList = IndukDokumen::whereIn('dokumen_id', $dokumen->pluck('id'))
            ->where('status', ['waiting final approval'])
            ->get();

        $kodeProses = RuleCode::all();

        return view('pages-rule.validasi_final', compact('jenis', 'tipe', 'dokumen', 'indukDokumenList', 'kodeProses'));
    }
    public function finalapproved(Request $request, $id)
    {
        // Temukan dokumen berdasarkan ID
        $dokumen = IndukDokumen::findOrFail($id);

        // Periksa apakah status dokumen adalah "waiting final approval"
        if ($dokumen->status != 'waiting final approval') {
            return redirect()->back()->with('error', 'Dokumen tidak dalam status waiting approval.');
        }

        // Lakukan perubahan status menjadi "approved"
        $dokumen->status = 'final approved'; // Sesuaikan dengan kolom status di tabel IndukDokumen Anda

        // Simpan komentar yang diambil dari inputan form
        $dokumen->comment = 'Your "' . $dokumen->nama_dokumen . '" has been approved. ';

        // Set status dokumen menjadi "belum aktif"
        $dokumen->statusdoc = 'belum aktif'; // Sesuaikan dengan kolom status_doc di tabel IndukDokumen Anda

        // Simpan perubahan
        $dokumen->save();

        // Redirect atau kembali ke halaman sebelumnya dengan pesan sukses
        return redirect()->back()->with('success', 'Dokumen berhasil diapprove.');
    }
    public function finalrejected(Request $request, $id)
    {
        // Temukan dokumen berdasarkan ID
        $dokumen = IndukDokumen::findOrFail($id);

        // Validasi input comment
        $request->validate([
            'comment' => 'required|string|max:255',
        ]);

        // Periksa apakah status dokumen adalah "waiting approval"
        if ($dokumen->status != 'waiting final approval') {
            return redirect()->back()->with('error', 'Dokumen tidak dalam status waiting approval.');
        }

        // Lakukan perubahan status menjadi "final rejected"
        $dokumen->status = 'final rejected'; // Sesuaikan dengan kolom status di tabel IndukDokumen Anda

        // Simpan komentar yang diambil dari inputan form
        $dokumen->comment = 'Your "' . $dokumen->nama_dokumen . '" has been rejected. ' . $request->input('comment');

        // Simpan perubahan
        $dokumen->save();

        // Redirect atau kembali ke halaman sebelumnya dengan pesan sukses
        return redirect()->back()->with('success', 'Dokumen berhasil direject. Tolong upload kembali dokumen yang benar.');
    }
    public function updateStatusDoc(Request $request, $id, $action)
    {
        // Temukan dokumen berdasarkan ID
        $dokumen = IndukDokumen::findOrFail($id);

        // Lakukan pengecekan berdasarkan aksi yang diterima
        switch ($action) {
            case 'activate':
                // Jika status belum aktif, set menjadi aktif
                if ($dokumen->statusdoc == 'belum aktif') {
                    $dokumen->statusdoc = 'active';
                    $message = 'Dokumen berhasil diaktifkan.';
                }
                break;
            case 'obsolate':
                // Jika status aktif, set menjadi obsolate
                if ($dokumen->statusdoc == 'active') {
                    $dokumen->statusdoc = 'obsolate';
                    $message = 'Dokumen berhasil diobsolatkan.';
                }
                // Jika status obsolate, set menjadi aktif kembali
                elseif ($dokumen->statusdoc == 'obsolate') {
                    $dokumen->statusdoc = 'active';
                    $message = 'Dokumen berhasil diaktifkan kembali.';
                }
                break;
            default:
                // Aksi tidak valid
                return redirect()->back()->with('error', 'Aksi tidak valid.');
        }

        // Simpan perubahan status
        $dokumen->save();

        // Redirect kembali dengan pesan sukses
        return redirect()->back()->with('success', $message);
    }
}
