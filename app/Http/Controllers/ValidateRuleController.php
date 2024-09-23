<?php

namespace App\Http\Controllers;

use App\Models\Departemen;
use App\Models\DocumentDepartement;
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
    public function validate_index($jenis, $tipe, Request $request)
    {
        // Ambil input dari request
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');
        $tipeDokumenId = $request->input('tipe_dokumen_id');
        $statusDoc = $request->input('statusdoc');
        $departemen = (int) $request->input('departemen', 0);

        // Mulai kueri dokumen
        $query = Dokumen::where('jenis_dokumen', $jenis)
            ->where('tipe_dokumen', $tipe);

        // Filter berdasarkan tanggal upload
        if ($request->filled('date_from') && $request->filled('date_to')) {
            $query->whereBetween('induk_dokumen.tgl_upload', [$dateFrom, $dateTo]);
        }

        // Filter berdasarkan tipe dokumen
        if ($request->filled('tipe_dokumen_id')) {
            $query->where('dokumen.tipe_dokumen', $tipeDokumenId);
        }

        // Filter berdasarkan status dokumen
        if ($request->filled('statusdoc')) {
            $query->where('induk_dokumen.statusdoc', $statusDoc);
        }

        // Filter berdasarkan Departemen (Hanya untuk admin)
        if ($departemen > 0 && $user->hasRole('admin')) {
            $query->where('induk_dokumen.departemen_id', $departemen);
        }

        // Eksekusi kueri untuk mendapatkan dokumen
        $dokumen = $query->get();

        // Ambil induk dokumen yang sesuai dengan dokumen yang telah dipilih
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
        $request->validate([
            'file' => 'nullable|mimes:doc,docx,xls,xlsx',
        ], [
            'file.mimes' => 'Only Word and Excel files are allowed.',
        ]);

        try {
            // Temukan dokumen berdasarkan ID
            $dokumen = IndukDokumen::findOrFail($id);

            // Periksa apakah status dokumen adalah "waiting approval"
            if ($dokumen->status != 'Waiting check by MS') {
                return redirect()->back()->with('error', 'Documents are not in waiting approval status.');
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
            return redirect()->back()->with('error', 'There is an error: ' . $e->getMessage());
        }
    }

    public function upload_old_doc(Request $request)
    {
        // Validasi file
        $request->validate([
            'file' => 'required|mimes:pdf',
        ], [
            'file.mimes' => 'Only PDF files are allowed.',
        ]);

        // Simpan file ke direktori public
        $file_pdf = $request->file('file');
        $filename = $file_pdf->getClientOriginalName();
        $path = 'final-rule/' . $filename;
        $file_pdf->storeAs('final-rule', $filename, 'public'); // Simpan file

        // Ambil informasi departemen dari input form
        $departemen_id = $request->input('department');
        $departemen = Departemen::find($departemen_id);
        if (!$departemen) {
            return redirect()->back()->with('error', 'Invalid department.');
        }

        $revisi_log = $request->status_dokumen === 'revisi' ? $request->revisi_ke : 0;

        // Ambil rule
        $rule = RuleCode::find($request->rule_id);
        if (!$rule) {
            return redirect()->back()->with('error', 'Rule is invalid.');
        }
        $kode_proses = $rule->kode_proses;

        // Ambil dokumen
        $document = Dokumen::where('jenis_dokumen', $request->jenis_dokumen)
            ->where('tipe_dokumen', $request->tipe_dokumen)
            ->first();

        if (!$document) {
            return redirect()->back()->with('error', 'Invalid type and document type.');
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
        $dokumen->file_pdf = $path; // Path file yang benar
        $dokumen->revisi_log = $revisi_log;
        $dokumen->nomor_dokumen = $nomorDokumen;
        $dokumen->tgl_upload = Carbon::now();
        $dokumen->departemen_id = $departemen_id;
        $dokumen->rule_id = $request->rule_id;
        $dokumen->status = 'Waiting Final Approval';
        $dokumen->statusdoc = 'not yet active';
        $dokumen->comment = 'Document "' . $dokumen->nama_dokumen . '" has been checked.';
        $dokumen->save();

        // Jika ada departemen yang dipilih, kaitkan dokumen dengan departemen tersebut
        if ($request->has('kode_departemen')) {
            $departemenCodes = $request->input('kode_departemen');
            $departemens = Departemen::whereIn('code', $departemenCodes)->get();
            $dokumen->departments()->sync($departemens->pluck('id'));
        }

        // Tampilkan pesan sukses
        Alert::success('Success', 'Document uploaded successfully.');
        return redirect()->back();
    }
    public function activateDocument(Request $request, $id)
    {
        $id = (int) $id;

        // Temukan dokumen berdasarkan ID
        $dokumen = IndukDokumen::findOrFail($id);

        // Periksa apakah dokumen belum aktif atau sudah obsolete
        if ($dokumen->statusdoc == 'not yet active' || $dokumen->statusdoc == 'obsolete') {

            // Cek apakah kolom file_pdf tidak null dan file tersebut ada
            if (!is_null($dokumen->file_pdf) && Storage::disk('public')->exists($dokumen->file_pdf)) {
                // Tambahkan watermark pada PDF
                $watermarkedPath = $this->addWatermarkToPdf(
                    $dokumen->file_pdf,
                    'Controlled Copy',
                    'stamp_controlled_copy.png',
                    20,
                    150
                );

                // Perbarui kolom active_doc dengan path file yang sudah di-watermark
                $dokumen->active_doc = $watermarkedPath;
            } else {
                // Jika file_pdf tidak ada, tampilkan pesan kesalahan
                Alert::error('The document PDF file is missing.');
                return redirect()->back();
            }

            // Set status dokumen
            $dokumen->statusdoc = 'active';
            $dokumen->status = 'Approve by MS';
            $dokumen->comment = 'The document has been successfully activated.';
            $dokumen->tgl_efektif = $request->input('activation_date');
            $dokumen->save();

            // Ambil nama dasar dari file yang telah di-watermark
            $fileBaseName = pathinfo($dokumen->active_doc, PATHINFO_FILENAME);

            $departemenIds = DocumentDepartement::where('induk_dokumen_id', $dokumen->id)
                ->pluck('departemen_id')
                ->toArray(); // Pastikan ini mengembalikan array

            $departemenNames = [];
            foreach ($departemenIds as $index => $departemenId) {
                $departemen = Departemen::find($departemenId);
                if ($departemen) {
                    $departemenNames[] = ($index + 1) . '. ' . $departemen->nama_departemen;
                }
            }

            // Buat pesan yang mencakup semua departemen yang mendapatkan distribusi
            $departemenList = implode("\n", $departemenNames); // Menggunakan newline untuk format daftar
            $message = "------ DOCUMENT DISTRIBUTION NOTIFICATION ------\n\nDocument Activated: $fileBaseName\n\nDistributed To Departments:\n$departemenList\n\nSilakan lihat dan download pada menu distributed document\n\n------ BY AISIN BISA ------";

            // Define group IDs for notifications
            $groupIds = [
                '120363311478624933', // Ganti dengan ID grup WhatsApp yang relevan
            ];

            // Pastikan groupIds adalah array, meskipun hanya ada satu ID
            if (is_string($groupIds)) {
                $groupIds = [$groupIds];
            }

            foreach ($groupIds as $groupId) {
                // Kirim pesan WhatsApp ke grup
                $this->sendWaReminderAudit($groupId, $message);
            }

            Alert::success('The document has been successfully activated.');
            return redirect()->back();
        }

        Alert::error('The document cannot be activated.');
        return redirect()->back();
    }
    protected function sendWaReminderAudit($groupId, $message)
    {
        // Send WA notification
        $token = 'v2n49drKeWNoRDN4jgqcdsR8a6bcochcmk6YphL6vLcCpRZdV1';

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => 'https://app.ruangwa.id/api/send_message',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30, // Atur waktu timeout untuk lebih realistis
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => http_build_query([
                'token' => $token,
                'number' => $groupId, // Pastikan ini adalah ID grup yang benar atau nomor WhatsApp
                'message' => $message,
            ]),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/x-www-form-urlencoded',
            ],
        ]);

        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
    }

    public function obsoleteDocument(Request $request, $id)
    {
        // Temukan dokumen berdasarkan ID
        $dokumen = IndukDokumen::findOrFail($id);

        // Periksa apakah dokumen belum aktif atau sudah obsolete
        if ($dokumen->statusdoc != 'obsolete') {
            // Tambahkan watermark pada PDF jika kolom file_pdf tidak null
            if (!is_null($dokumen->file_pdf) && Storage::disk('public')->exists($dokumen->file_pdf)) {
                $watermarkedPath = $this->addWatermarkToPdf(
                    $dokumen->file_pdf,
                    'Obsolete',
                    'stamp_obsolete.png',
                    50,
                    120
                );

                // Simpan path file yang sudah di-watermark ke kolom obsolete_doc
                $dokumen->obsolete_doc = $watermarkedPath;
            } else {
                Alert::error('The document PDF file is missing.');
                return redirect()->back();
            }

            // Set status dokumen
            $dokumen->statusdoc = 'obsolete';
            $dokumen->status = 'Obsolete by MS';
            $dokumen->comment = 'The document has been successfully marked as obsolete.';
            $dokumen->tgl_efektif = $request->input('obsolete_date');
            $dokumen->save();

            Alert::success('The document has been successfully marked as obsolete.');
            return redirect()->back();
        }

        Alert::error('The document cannot be marked as obsolete.');
        return redirect()->back();
    }
}
