<?php

namespace App\Http\Controllers;

use App\Models\Departemen;
use App\Models\Dokumen;
use App\Models\IndukDokumen;
use App\Models\RuleCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use RealRashid\SweetAlert\Facades\Alert;
use App\Traits\AddWatermarkTrait;
use Carbon\Carbon;

class ValidateRuleController extends Controller
{
    use AddWatermarkTrait;
    public function validate_index($jenis, $tipe)
    {
        // Ambil dokumen yang sesuai dengan jenis dan tipe dokumen dan urutkan berdasarkan tanggal upload
        $dokumen = Dokumen::where('jenis_dokumen', $jenis)
            ->where('tipe_dokumen', $tipe)
            ->get();

        // Ambil induk dokumen yang sesuai dengan dokumen yang telah dipilih dan memiliki status "waiting approval"
        $indukDokumenList = IndukDokumen::whereIn('dokumen_id', $dokumen->pluck('id'))
            ->orderBy('tgl_upload', 'desc')
            ->get();

        // Ambil semua kode proses
        $kodeProses = RuleCode::all();

        // Ambil semua departemen
        $allDepartemen = Departemen::all();

        // Return view dengan data yang sudah difilter
        return view('pages-rule.validasi-rule', compact('jenis', 'tipe', 'dokumen', 'indukDokumenList', 'kodeProses', 'allDepartemen'));
    }
    public function approveDocument(Request $request, $id)
    {
        try {

            // Temukan dokumen berdasarkan ID
            $dokumen = IndukDokumen::findOrFail($id);


            // Periksa apakah status dokumen adalah "waiting approval"
            if ($dokumen->status != 'Waiting check by MS') {
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
            $dokumen->status = 'Finish check by MS'; // Sesuaikan dengan kolom status di tabel IndukDokumen Anda

            // Lakukan perubahan statusdoc menjadi ""
            $dokumen->statusdoc = 'not yet active'; // Sesuaikan dengan kolom status di tabel IndukDokumen Anda

            // Simpan komentar yang diambil dari inputan form
            $dokumen->comment = $request->input('comment');

            // Simpan perubahan
            $dokumen->save();

            Alert::success('Success', 'Finish check by MS.');

            // Redirect atau kembali ke halaman sebelumnya dengan pesan sukses
            return redirect()->back();
        } catch (\Exception $e) {
            // Tangani pengecualian jika terjadi kesalahan
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
    public function uploadFinal(Request $request, $id)
    {
        // Validasi file hanya bisa PDF
        $request->validate([
            'file' => 'required|mimes:pdf|max:10240',
        ], [
            'file.mimes' => 'Only PDF files are allowed.',
        ]);

        // Ambil dokumen berdasarkan ID
        $doc = IndukDokumen::findOrFail($id);

        // Simpan file di folder final-rule
        $file = $request->file('file');
        $filename = $file->getClientOriginalName();
        $path = $filename;
        $file->storeAs('final-rule', $filename, 'public');

        // Update path file di database
        $doc->file_pdf = $path;
        $doc->save();

        // Tampilkan pesan sukses
        return redirect()->back()->with('success', 'Dokumen berhasil diunggah.');
    }
    // public function previewFinal($id)
    // {
    //     // Cari dokumen berdasarkan ID
    //     $document = IndukDokumen::findOrFail($id);

    //     // Lakukan validasi atau pengecekan apakah dokumen tersedia
    //     if (!$document->file_pdf) {
    //         abort(404, 'Dokumen tidak ditemukan atau tidak tersedia.');
    //     }

    //     // Path ke file PDF
    //     $filePath = storage_path('app/public/final-rule' . $document->file_pdf);

    //     // Verifikasi apakah file ada di path yang diharapkan
    //     if (!file_exists($filePath)) {
    //         abort(404, 'File dokumen tidak ditemukan.');
    //     }

    //     // Mendapatkan nama file berdasarkan jenis_dokumen dan tipe_dokumen
    //     $fileName = $document->jenis_dokumen . '_' . $document->tipe_dokumen . '.pdf';

    //     // Tampilkan file PDF di browser untuk pratinjau
    //     return response()->file($filePath, ['Content-Disposition' => 'inline; filename="' . $fileName . '"']);
    // }

    public function upload_old_doc(Request $request)
    {
        $file_pdf = $request->file('file');
        $filename = $file_pdf->getClientOriginalName(); // Hanya nama file
        $path = $filename; // Hanya nama file

        // Simpan file ke storage tanpa folder tambahan
        $file_pdf->storeAs('', $filename, 'public'); // Simpan di root storage folder

        // Ambil informasi departemen dari input form
        $departemen_id = $request->input('department');
        $departemen = Departemen::find($departemen_id);
        if (!$departemen) {
            return redirect()->back()->with('error', 'Departemen tidak valid.');
        }

        $revisi_log = $request->status_dokumen === 'revisi' ? $request->revisi_ke : 0;

        // Ambil rule
        $rule = RuleCode::find($request->rule_id);
        if (!$rule) {
            return redirect()->back()->with('error', 'Rule tidak valid.');
        }
        $kode_proses = $rule->kode_proses;

        // Ambil dokumen
        $document = Dokumen::where('jenis_dokumen', $request->jenis_dokumen)
            ->where('tipe_dokumen', $request->tipe_dokumen)
            ->first();

        if (!$document) {
            return redirect()->back()->with('error', 'Jenis dan tipe dokumen tidak valid.');
        }

        $tipe_dokumen_code = $document->code;

        // Format nomor dokumen
        $nomor_list = str_pad($request->nomor_list, 3, '0', STR_PAD_LEFT);
        $nomorDokumen = sprintf(
            '%s-%s-%s-%s-%02d',
            strtoupper($tipe_dokumen_code),
            strtoupper($departemen->code),
            strtoupper($kode_proses),
            $nomor_list,
            $revisi_log
        );

        // Buat entri baru di tabel IndukDokumen
        $dokumen = new IndukDokumen();
        $dokumen->nama_dokumen = $request->nama_dokumen;
        $dokumen->dokumen_id = $document->id;
        $dokumen->file_pdf = $path; // Hanya nama file
        $dokumen->revisi_log = $revisi_log;
        $dokumen->nomor_dokumen = $nomorDokumen;
        $dokumen->tgl_upload = Carbon::now();
        $dokumen->departemen_id = $departemen_id; // Simpan departemen_id sebagai user_id
        $dokumen->rule_id = $request->rule_id;
        $dokumen->status = 'Finish check by MS';
        $dokumen->statusdoc = 'not yet active';
        $dokumen->comment = 'Document "' . $dokumen->nama_dokumen . '" has been checked.';
        $dokumen->save();

        // Jika ada departemen yang dipilih, kaitkan dokumen dengan departemen tersebut
        if ($request->has('kode_departemen')) {
            $departemenCodes = $request->input('kode_departemen');
            $departemens = Departemen::whereIn('code', $departemenCodes)->get();
            $dokumen->departments()->sync($departemens->pluck('id')); // Menggunakan sync() untuk update relasi
        }

        // Tampilkan pesan sukses
        Alert::success('Success', 'Dokumen berhasil diunggah.');
        return redirect()->back();
    }

    public function activateDocument(Request $request, $id)
    {
        $dokumen = IndukDokumen::findOrFail($id);

        if ($dokumen->statusdoc == 'not yet active' || $dokumen->statusdoc == 'obsolete') {
            if (!is_null($dokumen->file_pdf) && Storage::disk('public')->exists($dokumen->file_pdf)) {
                $imageWidth = 36; // Lebar gambar watermark
                $imageHeight = 36; // Tinggi gambar watermark

                // Path ke file yang telah di-watermark
                $watermarkedPath = 'active/' . basename($dokumen->file_pdf); // Menggunakan folder watermarked
                $this->addWatermarkToPdf($dokumen->file_pdf, 'Controlled Copy', 'stamp_controlled_copy.png', 20, 150, $imageWidth, $imageHeight);

                // Simpan path file yang sudah di-watermark
                $dokumen->active_doc = $watermarkedPath;
            }

            $dokumen->statusdoc = 'active';
            $dokumen->status = 'Approve by MS';
            $dokumen->comment = 'Dokumen berhasil diaktifkan.';
            $dokumen->tgl_efektif = $request->input('activation_date');
            $dokumen->save();

            Alert::success('Dokumen berhasil diaktifkan.');
            return redirect()->back();
        }

        Alert::error('Dokumen tidak dapat diaktifkan.');
        return redirect()->back();
    }

    public function previewActive($id)
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
    public function obsoleteDocument(Request $request, $id)
    {
        $dokumen = IndukDokumen::findOrFail($id);

        if ($dokumen->statusdoc != 'obsolete') {
            if (!is_null($dokumen->file_pdf) && Storage::disk('public')->exists($dokumen->file_pdf)) {
                $imageWidth = 36; // Lebar gambar watermark
                $imageHeight = 36; // Tinggi gambar watermark

                // Path ke file yang telah di-watermark
                $watermarkedPath = 'watermarked/' . basename($dokumen->file_pdf); // Menggunakan folder watermarked
                $this->addWatermarkToPdf($dokumen->file_pdf, 'Obsolete', 'stamp_obsolete.png', 50, 120, $imageWidth, $imageHeight);

                // Simpan path file yang sudah di-watermark
                $dokumen->obsolete_doc = $watermarkedPath;
            }

            $dokumen->statusdoc = 'obsolete';
            $dokumen->status = 'Obsolete by MS';
            $dokumen->comment = 'Dokumen berhasil di-obsalete-kan.';
            $dokumen->tgl_obsolete = $request->input('obsolete_date');
            $dokumen->save();

            Alert::success('Dokumen berhasil di-obsalete-kan.');
            return redirect()->back();
        }

        Alert::error('Dokumen tidak dapat di-obsalete-kan.');
        return redirect()->back();
    }

    public function previewObsolete($id)
    {
        // Cari dokumen berdasarkan ID
        $document = IndukDokumen::findOrFail($id);

        // Lakukan validasi atau pengecekan apakah dokumen tersedia
        if (!$document->obsolete_doc) {
            abort(404, 'Dokumen tidak ditemukan atau tidak tersedia.');
        }

        // Path ke file PDF
        $filePath = storage_path('app/public/' . $document->obsolete_doc);

        // Verifikasi apakah file ada di path yang diharapkan
        if (!file_exists($filePath)) {
            abort(404, 'File dokumen tidak ditemukan.');
        }

        // Mendapatkan nama file berdasarkan jenis_dokumen dan tipe_dokumen
        $fileName = $document->jenis_dokumen . '_' . $document->tipe_dokumen . '.pdf';

        // Tampilkan file PDF di browser untuk pratinjau
        return response()->file($filePath, ['Content-Disposition' => 'inline; filename="' . $fileName . '"']);
    }
}
