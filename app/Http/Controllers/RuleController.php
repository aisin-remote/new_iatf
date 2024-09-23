<?php

namespace App\Http\Controllers;

use App\Models\Departemen;
use App\Models\DocumentDepartement;
use App\Models\Dokumen;
use App\Models\IndukDokumen;
use App\Models\RuleCode;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use RealRashid\SweetAlert\Facades\Alert;

class RuleController extends Controller
{
    public function index($jenis, $tipe)
    {
        $user = auth()->user();
        $departemenId = $user->departemen_id;

        // Ambil dokumen berdasarkan jenis, tipe, dan departemen user
        $dokumen = Dokumen::where('jenis_dokumen', $jenis)
            ->where('tipe_dokumen', $tipe)
            ->whereHas('indukDokumen', function ($query) use ($departemenId) {
                $query->where('user_id', auth()->id());
            })
            ->orderBy('created_at', 'desc')
            ->get();

        // Ambil induk dokumen yang sesuai dengan dokumen yang telah dipilih
        $indukDokumenList = IndukDokumen::whereIn('dokumen_id', $dokumen->pluck('id'))
            ->where('user_id', auth()->id())
            ->orderBy('tgl_upload', 'desc')
            ->get();

        $kodeProses = RuleCode::all();

        // Ambil departemen dengan code yang unik
        $uniqueDepartemens = Departemen::select('code')->distinct()->get();

        return view('pages-rule.dokumen-rule', compact('jenis', 'tipe', 'dokumen', 'indukDokumenList', 'kodeProses', 'uniqueDepartemens'));
    }

    public function store(Request $request)
    {
        // Validasi file
        $request->validate([
            'file' => 'required|mimes:doc,docx,xls,xlsx',
        ], [
            'file.mimes' => 'Only Word and Excel files are allowed.',
        ]);

        // Simpan file
        $file = $request->file('file');
        $filename = $file->getClientOriginalName();
        $path = 'draft-rule/' . $filename;
        $file->storeAs('draft-rule', $filename, 'public'); // Simpan di direktori 'draft-rule' dalam storage 'public'

        // Ambil informasi user dan departemen
        $user = auth()->user();

        // Ambil departemen berdasarkan nama_departemen dari tabel user
        $departemen = Departemen::where('nama_departemen', $user->departemen->nama_departemen)->first();

        if (!$departemen) {
            return redirect()->back()->with('error', 'User does not belong to any valid department.');
        }

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
            return redirect()->back()->with('error', 'Invalid document type and document type.');
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
            $request->status_dokumen === 'revisi' ? $request->revisi_ke : 0
        );

        // Buat entri baru di tabel IndukDokumen
        $dokumen = new IndukDokumen();
        $dokumen->nama_dokumen = $request->nama_dokumen;
        $dokumen->dokumen_id = $document->id;
        $dokumen->file = $path;
        $dokumen->revisi_log = $request->status_dokumen === 'revisi' ? $request->revisi_ke : 0;
        $dokumen->nomor_dokumen = $nomorDokumen;
        $dokumen->tgl_upload = Carbon::now();
        $dokumen->user_id = $user->id;
        $dokumen->rule_id = $request->rule_id;
        $dokumen->departemen_id = $departemen->id;
        $dokumen->status = 'Waiting check by MS';
        $dokumen->comment = 'Document "' . $dokumen->nama_dokumen . '" has been uploaded.';
        $dokumen->save();
        if ($request->has('kode_departemen')) {
            $departemenCodes = $request->input('kode_departemen');
            $departemens = Departemen::whereIn('code', $departemenCodes)->get();
            $dokumen->departments()->sync($departemens->pluck('id'));
        }
        // Tampilkan pesan sukses
        Alert::success('Success', 'Document uploaded successfully.');
        return redirect()->back();
    }

    public function download($id)
    {
        // Cari dokumen berdasarkan ID
        $doc = IndukDokumen::findOrFail($id);

        // Path ke file dokumen
        $filePath = $doc->file; // Path yang disimpan di database

        // Cek apakah file ada
        if (!Storage::disk('public')->exists($filePath)) {
            return redirect()->back()->with('error', 'File tidak ditemukan.');
        }

        // Mengembalikan response download
        return Storage::disk('public')->download($filePath);
    }

    public function final_doc(Request $request, $jenis, $tipe)
    {

        $user = Auth::user(); // Mendapatkan user yang sedang login

        // Ambil data untuk filter dropdown
        $kodeProses = RuleCode::all();
        $alldepartmens = Departemen::all();
        $departemens = Departemen::all();
        $uniqueDepartemens = $departemens->unique('code');

        // Mulai dengan query dasar
        $query = IndukDokumen::query()
            ->join('dokumen', 'induk_dokumen.dokumen_id', '=', 'dokumen.id')
            ->select('induk_dokumen.*') // Pilih kolom yang ada di induk_dokumen
            ->whereHas('dokumen', function ($query) use ($jenis, $tipe) {
                $query->where('jenis_dokumen', $jenis) // Filter berdasarkan jenis_dokumen
                    ->where('tipe_dokumen', $tipe); // Filter berdasarkan tipe_dokumen
            });

        // Sesuaikan filter berdasarkan peran pengguna
        if ($user->hasRole('admin')) {
            $query->whereIn('induk_dokumen.status', ['Waiting Final Approval', 'Approve by MS', 'Obsolete by MS']);
        } else {
            $query->whereIn('induk_dokumen.status', ['Waiting Final Approval', 'Finish check by MS', 'Approve by MS', 'Obsolete by MS'])
                ->where(function ($query) use ($user) {
                    $query->where('induk_dokumen.departemen_id', $user->departemen_id);
                });
        }

        // Terapkan filter dari query string
        if ($request->filled('date_from') && $request->filled('date_to')) {
            $query->whereBetween('induk_dokumen.tgl_upload', [$request->date_from, $request->date_to]);
        }

        if ($request->filled('tipe_dokumen_id')) {
            $query->where('dokumen.tipe_dokumen', $request->tipe_dokumen_id);
        }

        if ($request->filled('statusdoc')) {
            $query->where('induk_dokumen.statusdoc', $request->statusdoc);
        }

        $departemen = (int) $request->input('departemen', 0);

        // Filter berdasarkan Departemen (Hanya untuk admin)
        if ($departemen > 0 && $user->hasRole('admin')) { // Pastikan hanya memfilter jika departemen_id valid dan user admin
            $query->where('induk_dokumen.departemen_id', $departemen);
        }

        // Ambil hasil query
        $dokumenfinal = $query->orderBy('induk_dokumen.updated_at', 'desc')->get();

        return view('pages-rule.dokumen-final', compact('dokumenfinal', 'kodeProses', 'alldepartmens', 'uniqueDepartemens', 'jenis', 'tipe'));
    }
    public function uploadFinal(Request $request, $id)
    {
        // Validasi file hanya bisa PDF
        $request->validate([
            'file' => 'required|mimes:pdf',
        ], [
            'file.mimes' => 'Only PDF files are allowed.',
        ]);

        // Ambil dokumen berdasarkan ID
        $doc = IndukDokumen::findOrFail($id);

        // Simpan file di folder public tanpa folder tambahan
        $file_pdf = $request->file('file');
        $filename = $file_pdf->getClientOriginalName(); // Ambil nama file asli
        $path = 'final-rule/' . $filename; // Tentukan path
        $file_pdf->storeAs('final-rule', $filename, 'public'); // Simpan file dengan nama asli


        // Update path file di database
        $doc->file_pdf = $path;
        $doc->status = 'Waiting Final Approval';
        $doc->save();

        // Tampilkan pesan sukses
        return redirect()->back()->with('success', 'Document uploaded successfully.');
    }
    public function share_document(Request $request, $jenis, $tipe)
    {
        $user = auth()->user();
        $departments = Departemen::all(); // Ambil semua departemen untuk dropdown

        // Mulai dengan query dasar
        $query = IndukDokumen::query()
            ->join('dokumen', 'induk_dokumen.dokumen_id', '=', 'dokumen.id')
            ->select('induk_dokumen.*') // Pilih kolom yang ada di induk_dokumen
            ->where('dokumen.jenis_dokumen', $jenis)
            ->where('dokumen.tipe_dokumen', $tipe)
            ->where('induk_dokumen.statusdoc', 'active')
            ->orderBy('induk_dokumen.updated_at', 'desc');

        // Sesuaikan filter berdasarkan peran pengguna
        if (!$user->hasRole('admin')) {
            // Jika user bukan admin, ambil dokumen terkait dengan departemen user
            $query->join('document_departement', 'induk_dokumen.id', '=', 'document_departement.induk_dokumen_id')
                ->where('document_departement.departemen_id', $user->departemen_id);
        }

        // Terapkan filter dari query string
        if ($request->filled('date_from') && $request->filled('date_to')) {
            // Pastikan format tanggal sesuai dengan format di database (YYYY-MM-DD)
            $query->whereBetween('induk_dokumen.tgl_upload', [$request->date_from, $request->date_to]);
        }

        if ($request->filled('departemen_id')) {
            $query->where('induk_dokumen.departemen_id', $request->departemen_id);
        }

        // Ambil hasil query
        $sharedDocuments = $query->get();

        return view('pages-rule.document-shared', compact('sharedDocuments', 'jenis', 'tipe', 'departments'));
    }
    public function preview($id)
    {
        $document = IndukDokumen::findOrFail($id);

        // Tentukan path file aktif
        $filePath = storage_path('app/public/' . $document->active_doc);

        // Pastikan file ada
        if (!file_exists($filePath)) {
            abort(404, 'File not found');
        }

        // Menentukan tipe MIME untuk file
        $mimeType = mime_content_type($filePath);

        // Tandai dokumen sebagai telah dilihat
        $departemenId = Auth::user()->departemen_id; // Ambil ID departemen dari user yang login

        // Periksa apakah entri sudah ada
        $documentView = DocumentDepartement::firstOrNew([
            'induk_dokumen_id' => $id,
            'departemen_id' => $departemenId
        ]);

        // Set status has_viewed
        $documentView->has_viewed = true;
        $documentView->save();

        // Kirim notifikasi
        $this->sendDocumentDistributionNotification($id);

        // Mengembalikan file untuk ditampilkan di browser
        return response()->file($filePath, [
            'Content-Type' => $mimeType
        ]);
    }
    protected function sendDocumentDistributionNotification($documentId)
    {
        $document = IndukDokumen::findOrFail($documentId);

        // Ambil semua departemen yang telah melihat dokumen
        $views = DocumentDepartement::where('induk_dokumen_id', $documentId)
            ->where('has_viewed', true)
            ->pluck('departemen_id')
            ->toArray();

        // Ambil departemen terkait dari DocumentDepartement
        $relatedDepartemenIds = DocumentDepartement::where('induk_dokumen_id', $documentId)
            ->pluck('departemen_id')
            ->toArray();

        // Ambil hanya departemen yang terkait
        $departemen = Departemen::whereIn('id', $relatedDepartemenIds)->get();

        // Buat daftar departemen dengan ceklis
        $departemenList = $departemen->map(function ($dept) use ($views) {
            return (in_array($dept->id, $views) ? "✔️ " : "❌ ") . $dept->nama_departemen;
        })->implode("\n");

        // Ambil nama file tanpa ekstensi
        $fileBaseName = pathinfo($document->active_doc, PATHINFO_FILENAME);

        // Pesan notifikasi
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
}
