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
            'file_draft' => 'nullable|file|mimes:pdf,doc,docx|max:2048', // File opsional, jika perlu diunggah
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
    public function finalapproved(Request $request, $id)
    {
        // Temukan dokumen berdasarkan ID
        $dokumen = IndukDokumen::findOrFail($id);

        // Periksa apakah status dokumen adalah "waiting final approval"
        if ($dokumen->status != 'waiting final approval') {
            return redirect()->back()->with('error', 'Dokumen tidak dalam status waiting approval.');
        }

        // Lakukan perubahan status menjadi "final approved"
        $dokumen->status = 'final approved'; // Sesuaikan dengan kolom status di tabel IndukDokumen Anda

        // Simpan komentar yang diambil dari inputan form
        $dokumen->comment = 'Your "' . $dokumen->nama_dokumen . '" has been approved. ';

        // Set status dokumen menjadi "belum aktif"
        $dokumen->statusdoc = 'not yet active'; // Sesuaikan dengan kolom status_doc di tabel IndukDokumen Anda

        // Simpan perubahan
        $dokumen->save();

        // Tambahkan stempel pada PDF
        $pdf = Pdf::loadView('pdf.stampel', compact('dokumen')); // Memuat view 'pdf.stampel' untuk menampilkan stempel

        // Simpan PDF dengan stempel ke storage atau lokasi yang diinginkan
        $filename = 'stamped_' . $dokumen->nomor_dokumen . '.pdf';
        Storage::disk('public')->put($filename, $pdf->output());

        Alert::success('Success', 'Dokumen berhasil diapprove.');

        // Redirect atau kembali ke halaman sebelumnya dengan pesan sukses
        return redirect()->back();
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

        Alert::success('Success', 'Dokumen berhasil direject.');

        // Redirect atau kembali ke halaman sebelumnya dengan pesan sukses
        return redirect()->back();
    }
    public function updateStatusDoc(Request $request, $id, $action)
    {
        // Temukan dokumen berdasarkan ID
        $dokumen = IndukDokumen::findOrFail($id);

        // dd($action);
        // if ($action == 'activate_not_yet') {
        //     $dokumen->statusdoc = 'active';
        //     $dokumen->comment = 'Dokumen berhasil diaktifkan.';
        // }

        // Lakukan pengecekan berdasarkan aksi yang diterima
        switch ($action) {
            case 'activate':
                // Jika status belum aktif, set menjadi aktif
                if ($dokumen->statusdoc == 'not yet active') {
                    $dokumen->statusdoc = 'active';
                    $dokumen->comment = 'Dokumen berhasil diaktifkan.';
                }
                break;
            case 'obsolate':
                // Jika status aktif, set menjadi obsolate
                if ($dokumen->statusdoc == 'active') {
                    $dokumen->statusdoc = 'obsolate';
                    $dokumen->comment = 'Dokumen berhasil diobsolatkan.';
                }
                // Jika status obsolate, set menjadi aktif kembali
                elseif ($dokumen->statusdoc == 'obsolate') {
                    $dokumen->statusdoc = 'active';
                    $dokumen->comment = 'Dokumen berhasil diaktifkan kembali.';
                }
                break;
            default:
                // Aksi tidak valid
                return redirect()->back()->with('error', 'Aksi tidak valid.');
        }

        // Simpan perubahan status
        $dokumen->save();

        // Redirect kembali dengan pesan sukses
        return redirect()->back()->with('success');
    }
}
