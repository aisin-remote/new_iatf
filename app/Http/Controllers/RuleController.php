<?php

namespace App\Http\Controllers;

use App\Models\Departemen;
use App\Models\Dokumen;
use App\Models\IndukDokumen;
use App\Models\RuleCode;
use App\Models\User;
use App\Notifications\DocumentStatusChanged;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

class RuleController extends Controller
{
    public function index($jenis, $tipe)
    {
        $user = auth()->user();
        $departemen_user = $user->departemen->nama_departemen;

        // Ambil dokumen berdasarkan jenis, tipe, departemen user yang login, dan status selain 'final approved'
        $dokumen = Dokumen::where('jenis_dokumen', $jenis)
            ->where('tipe_dokumen', $tipe)
            ->whereHas('indukDokumen.user.departemen', function ($query) use ($departemen_user) {
                $query->where('nama_departemen', $departemen_user);
            })
            ->whereHas('indukDokumen', function ($query) {
                $query->where('status', '!=', 'final approved');
            })
            ->get();

        // Ambil induk dokumen yang sesuai dengan dokumen yang telah dipilih
        $indukDokumenList = IndukDokumen::whereIn('dokumen_id', $dokumen->pluck('id'))
            ->whereHas('user.departemen', function ($query) use ($departemen_user) {
                $query->where('nama_departemen', $departemen_user);
            })
            ->where('status', '!=', 'final approved')
            ->get();

        $kodeProses = RuleCode::all();
        $departemens = Departemen::all();

        return view('pages-rule.dokumen-rule', compact('jenis', 'tipe', 'dokumen', 'indukDokumenList', 'kodeProses', 'departemens'));
    }
    public function store(Request $request)
    {
        $request->validate([
            'nama_dokumen' => 'required',
            'file_draft' => 'required|file',
            'rule_id' => 'required|exists:rule,id',
            'jenis_dokumen' => 'required',
            'tipe_dokumen' => 'required',
            'departemen' => 'nullable|array',
            'departemen.*' => 'exists:departemen,id',
            'revisi_ke' => 'nullable|integer',
        ]);

        $file = $request->file('file_draft');
        $filename = time() . '_' . $file->getClientOriginalName();
        $path = 'draft_rule/' . $filename;
        $file->storeAs('draft_rule', $filename, 'public');

        $userId = auth()->id();
        $user = auth()->user();
        $departemen_user = $user->departemen->nama_departemen;
        $revisi_log = $request->status_dokumen === 'revisi' ? $request->revisi_ke : 0;

        $rule = RuleCode::find($request->rule_id);
        if (!$rule) {
            return redirect()->back()->with('error', 'Rule tidak valid.');
        }
        $kode_proses = $rule->kode_proses;

        $documentId = Dokumen::where('jenis_dokumen', $request->jenis_dokumen)
            ->where('tipe_dokumen', $request->tipe_dokumen)
            ->value('id');

        if (!$documentId) {
            return redirect()->back()->with('error', 'Jenis dan tipe dokumen tidak valid.');
        }

        $latestDokumen = IndukDokumen::where('dokumen_id', $documentId)
            ->orderBy('id', 'desc')
            ->first();

        $currentNumber = $latestDokumen ? intval(substr($latestDokumen->nomor_dokumen, -5, 3)) : 0;
        $newNumber = $currentNumber + 1;
        $number = str_pad($newNumber, 3, '0', STR_PAD_LEFT);

        $nomorDokumen = sprintf(
            '%s-%s-%s-%s-%02d',
            strtoupper(substr($request->tipe_dokumen, 0, 3)),
            strtoupper(substr($departemen_user, 0, 3)),
            strtoupper($kode_proses),
            $number,
            $revisi_log
        );

        $dokumen = new IndukDokumen();
        $dokumen->nama_dokumen = $request->nama_dokumen;
        $dokumen->dokumen_id = $documentId;
        $dokumen->file_draft = $path;
        $dokumen->revisi_log = $revisi_log;
        $dokumen->nomor_dokumen = $nomorDokumen;
        $dokumen->tgl_upload = Carbon::now();
        $dokumen->user_id = $userId;
        $dokumen->rule_id = $request->rule_id;
        $dokumen->status = 'waiting approval';
        $dokumen->comment = 'Dokumen "' . $dokumen->nama_dokumen . '" telah diunggah.';
        $dokumen->save();

        if ($request->has('departemen')) {
            $dokumen->departments()->attach($request->departemen);
        }

        return redirect()->back()->with('success', 'Dokumen berhasil diunggah.');
    }

    public function uploadFinal(Request $request, $id)
    {
        try {
            // Validasi request
            $request->validate([
                'file_final' => 'required|file|mimes:pdf,doc,docx|max:2048', // Sesuaikan dengan kebutuhan Anda
            ]);

            // Ambil dokumen berdasarkan ID yang diterima
            $dokumen = IndukDokumen::findOrFail($id);

            // Ambil file yang diunggah
            $file = $request->file('file_final');

            // Generate nama file unik dengan timestamp
            $filename = time() . '_' . $file->getClientOriginalName();

            // Tentukan path penyimpanan file
            $path = 'final_rule/' . $filename;

            // Simpan file di penyimpanan publik
            $file->storeAs('final_rule', $filename, 'public');

            // Update kolom file_final di database
            $dokumen->file_final = $path;

            // Update status dokumen menjadi "pending final approved"
            $dokumen->status = 'waiting final approval';
            // Simpan perubahan
            $dokumen->save();

            // Redirect atau kembalikan respons sukses
            return redirect()->back()->with('success', 'Dokumen berhasil diunggah.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mengunggah dokumen: ' . $e->getMessage());
        }
    }

    public function download_draft($jenis, $tipe, $id)
    {
        // Lakukan validasi jika diperlukan
        // Misalnya, pastikan jenis dan tipe sesuai dengan kebutuhan bisnis Anda.

        // Ambil dokumen berdasarkan ID
        $dokumen = IndukDokumen::find($id);

        if (!$dokumen) {
            return redirect()->back()->with('error', 'Dokumen tidak ditemukan.');
        }

        // Ambil path file dari database
        $path = $dokumen->file_draft;

        // Periksa apakah path tidak null dan merupakan string
        if (is_null($path) || !is_string($path)) {
            return redirect()->back()->with('error', 'Path file tidak valid.');
        }

        // Periksa apakah file ada di storage
        if (!Storage::disk('public')->exists($path)) {
            return redirect()->back()->with('error', 'File tidak ditemukan di storage.');
        }

        // Tentukan nama file yang akan diunduh
        $downloadFilename = $dokumen->nomor_dokumen . '_' . $dokumen->nama_dokumen . '.' . pathinfo($path, PATHINFO_EXTENSION);

        // Lakukan download file dengan nama yang ditentukan
        return Storage::disk('public')->download($path, $downloadFilename);
    }
    public function share_document()
    {
        // Mendapatkan user yang sedang login
        $user = auth()->user();
        // Mendapatkan nama departemen dari user
        $departemen_user = $user->departemen->nama_departemen;

        // $sharedDocuments = IndukDokumen::first()->with('distributions')->where('statusdoc', 'active')->get();
        $sharedDocuments = IndukDokumen::select('induk_dokumen.*')
            ->join('document_departement', 'induk_dokumen.id', 'document_departement.induk_dokumen_id')
            ->where('document_departement.departemen_id', $user->departemen_id)
            ->where('induk_dokumen.statusdoc', 'active')
            ->get();
        // $test = $sharedDocuments->departments;
        // Mengambil dokumen yang dibagikan berdasarkan departemen user dan memiliki status final approved
        // $sharedDocuments = IndukDokumen::whereHas('departments', function ($query) use ($departemen_user) {
        //     $query->where('nama_departemen', $departemen_user);
        // })
        //     ->where('statusdoc', 'active')
        //     ->whereNotNull('file_final')
        //     ->orderByDesc('created_at')
        //     ->get();

        return view('pages-rule.document-shared', compact('sharedDocuments'));
    }

    public function downloadSharedDocument($id)
    {
        $user = auth()->user();
        $departemen_user = $user->departemen->nama_departemen;

        // Periksa apakah dokumen tersebut dibagikan kepada departemen user yang sedang login
        // dan memiliki status final approved serta kolom file_final tidak null
        $dokumen = IndukDokumen::where('id', $id)
            ->whereHas('departments', function ($query) use ($departemen_user) {
                $query->where('nama_departemen', $departemen_user);
            })
            ->where('statusdoc', 'active')
            ->whereNotNull('file_final')
            ->first();

        if (!$dokumen) {
            return redirect()->back()->with('error', 'Dokumen tidak ditemukan atau tidak diizinkan untuk diakses.');
        }

        // Ambil path file dari database
        $path = $dokumen->file_final;

        // Periksa apakah path tidak null dan merupakan string
        if (is_null($path) || !is_string($path)) {
            return redirect()->back()->with('error', 'Path file tidak valid.');
        }

        // Periksa apakah file ada di storage
        if (!Storage::disk('public')->exists($path)) {
            return redirect()->back()->with('error', 'File tidak ditemukan di storage.');
        }

        // Tentukan nama file yang akan diunduh
        $downloadFilename = $dokumen->nomor_dokumen . '_' . $dokumen->nama_dokumen . '.' . pathinfo($path, PATHINFO_EXTENSION);

        // Lakukan download file dengan nama yang ditentukan
        return Storage::disk('public')->download($path, $downloadFilename);
    }
    public function final_doc()
    {
        // Mengambil semua dokumen yang memiliki status final approved
        $dokumenfinal = IndukDokumen::where('status', 'final approved')
            ->whereNotNull('file_final')
            ->orderByDesc('created_at')
            ->get();

        return view('pages-rule.dokumen-final', compact('dokumenfinal'));
    }
    public function downloadfinal($id)
    {
        // Lakukan validasi jika diperlukan
        // Misalnya, pastikan jenis dan tipe sesuai dengan kebutuhan bisnis Anda.

        // Ambil dokumen berdasarkan ID
        $dokumen = IndukDokumen::find($id);

        if (!$dokumen) {
            return redirect()->back()->with('error', 'Dokumen tidak ditemukan.');
        }

        // Periksa apakah dokumen memiliki file final
        if (is_null($dokumen->file_final)) {
            return redirect()->back()->with('error', 'Dokumen belum memiliki file final atau tidak diizinkan untuk diunduh.');
        }

        // Periksa apakah dokumen memiliki status final approved
        if ($dokumen->status != 'final approved') {
            return redirect()->back()->with('error', 'Dokumen belum disetujui final atau tidak diizinkan untuk diunduh.');
        }

        // Ambil path file dari database
        $path = $dokumen->file_final;

        // Periksa apakah path tidak null dan merupakan string
        if (is_null($path) || !is_string($path)) {
            return redirect()->back()->with('error', 'Path file tidak valid.');
        }

        // Periksa apakah file ada di storage
        if (!Storage::disk('public')->exists($path)) {
            return redirect()->back()->with('error', 'File tidak ditemukan di storage.');
        }

        // Tentukan nama file yang akan diunduh
        $downloadFilename = $dokumen->nomor_dokumen . '_' . $dokumen->nama_dokumen . '.' . pathinfo($path, PATHINFO_EXTENSION);

        // Lakukan download file dengan nama yang ditentukan
        return Storage::disk('public')->download($path, $downloadFilename);
    }
    public function DownloadDocFinal($id)
    {
        // Ambil dokumen berdasarkan ID
        $dokumen = IndukDokumen::find($id);

        if (!$dokumen) {
            return redirect()->back()->with('error', 'Dokumen tidak ditemukan.');
        }

        // Periksa apakah dokumen memiliki status final approved
        if ($dokumen->status != 'waiting final approved') {
            return redirect()->back()->with('error', 'Dokumen belum disetujui final atau tidak diizinkan untuk diunduh.');
        }

        // Periksa apakah dokumen memiliki file final
        if (is_null($dokumen->file_final)) {
            return redirect()->back()->with('error', 'Dokumen belum memiliki file final atau tidak diizinkan untuk diunduh.');
        }

        // Ambil path file dari database
        $path = $dokumen->file_final;

        // Periksa apakah path tidak null dan merupakan string
        if (is_null($path) || !is_string($path)) {
            return redirect()->back()->with('error', 'Path file tidak valid.');
        }

        // Periksa apakah file ada di storage
        if (!Storage::disk('public')->exists($path)) {
            return redirect()->back()->with('error', 'File tidak ditemukan di storage.');
        }

        // Tentukan nama file yang akan diunduh
        $downloadFilename = $dokumen->nomor_dokumen . '_' . $dokumen->nama_dokumen . '.' . pathinfo($path, PATHINFO_EXTENSION);

        // Lakukan download file dengan nama yang ditentukan
        return Storage::disk('public')->download($path, $downloadFilename);
    }
}
