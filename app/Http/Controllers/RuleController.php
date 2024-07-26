<?php

namespace App\Http\Controllers;

use App\Models\Departemen;
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
        $departemen_user = $user->departemen->nama_departemen;

        // Ambil dokumen berdasarkan jenis, tipe, departemen user yang login, dan status 'Waiting check by MS' atau 'Finish Check by MS'
        $dokumen = Dokumen::where('jenis_dokumen', $jenis)
            ->where('tipe_dokumen', $tipe)
            ->whereHas('indukDokumen.user.departemen', function ($query) use ($departemen_user) {
                $query->where('nama_departemen', $departemen_user);
            })
            ->whereHas('indukDokumen', function ($query) {
            })
            ->orderBy('created_at', 'desc')
            ->get();

        // Ambil induk dokumen yang sesuai dengan dokumen yang telah dipilih
        $indukDokumenList = IndukDokumen::whereIn('dokumen_id', $dokumen->pluck('id'))
            ->whereHas('user.departemen', function ($query) use ($departemen_user) {
                $query->where('nama_departemen', $departemen_user);
            })
            ->orderBy('tgl_upload', 'desc')
            ->get();

        $kodeProses = RuleCode::all();
        $departemens = Departemen::all();

        // Array untuk melacak code yang sudah ditampilkan
        $uniqueDepartemens = $departemens->unique('code');

        return view('pages-rule.dokumen-rule', compact('jenis', 'tipe', 'dokumen', 'indukDokumenList', 'kodeProses', 'uniqueDepartemens'));
    }

    public function store(Request $request)
    {
        // dd($request);
        // $request->validate([
        //     'file' => 'required|mimes:doc,docx,xls,xlsx|max:2048',
        // ], [
        //     'file.mimes' => 'Only Word and Excel files are allowed.',
        // ]);
        // Simpan file
        $file = $request->file('file');
        $filename = time() . '_' . $file->getClientOriginalName();
        $path = 'rule/' . $filename;
        $file->storeAs('rule', $filename, 'public');

        // Ambil informasi user
        $userId = auth()->id();
        $user = auth()->user();
        $departemen_user_code = $user->departemen->code;
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
            strtoupper($departemen_user_code),
            strtoupper($kode_proses),
            $nomor_list,
            $revisi_log
        );

        // Buat entri baru di tabel IndukDokumen
        $dokumen = new IndukDokumen();
        $dokumen->nama_dokumen = $request->nama_dokumen;
        $dokumen->dokumen_id = $document->id;
        $dokumen->file = $path;
        $dokumen->revisi_log = $revisi_log;
        $dokumen->nomor_dokumen = $nomorDokumen;
        $dokumen->tgl_upload = Carbon::now();
        $dokumen->user_id = $userId;
        $dokumen->rule_id = $request->rule_id;
        $dokumen->status = 'Waiting check by MS';
        $dokumen->comment = 'Dokumen "' . $dokumen->nama_dokumen . '" telah diunggah.';
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
    public function download($jenis, $tipe, $id)
    {
        // Ambil dokumen berdasarkan ID
        $dokumen = IndukDokumen::find($id);

        if (!$dokumen) {
            return redirect()->back()->with('error', 'Dokumen tidak ditemukan.');
        }

        // Tentukan path file draft
        $path = $dokumen->file;

        // Periksa apakah path tidak null dan merupakan string
        if (is_null($path) || !is_string($path)) {
            return redirect()->back()->with('error', 'Path file tidak valid.');
        }

        // Periksa apakah file ada di storage
        if (!Storage::disk('public')->exists($path)) {
            return redirect()->back()->with('error', 'File tidak ditemukan di storage.');
        }

        // Tentukan nama file untuk download
        $downloadFilename = $dokumen->nomor_dokumen . '_' . $dokumen->nama_dokumen . '.' . pathinfo($path, PATHINFO_EXTENSION);

        // Lakukan download dengan nama file yang ditentukan
        return Storage::disk('public')->download($path, $downloadFilename);
    }
    public function final_doc($jenis, $tipe)
    {
        $user = Auth::user(); // Mendapatkan user yang sedang login

        // Jika user adalah admin, mengambil semua dokumen final approved
        if ($user->hasRole('admin')) {
            $dokumenfinal = IndukDokumen::whereIn('status', ['Finish check by MS', 'Approve by MS'])
                ->whereHas('dokumen', function ($query) use ($jenis, $tipe) {
                    $query->where('jenis_dokumen', $jenis) // Filter berdasarkan jenis_dokumen
                        ->where('tipe_dokumen', $tipe); // Filter berdasarkan tipe_dokumen
                })
                ->orderByDesc('updated_at')
                ->get();
        } else {
            // Jika user bukan admin, mengambil dokumen final approved yang terkait dengan departemen user
            $dokumenfinal = IndukDokumen::where('statusdoc', 'active')
                ->whereHas('dokumen', function ($query) use ($jenis, $tipe) {
                    $query->where('jenis_dokumen', $jenis) // Filter berdasarkan jenis_dokumen
                        ->where('tipe_dokumen', $tipe); // Filter berdasarkan tipe_dokumen
                })
                ->whereNotNull('file_pdf') // Pastikan ini sesuai dengan nama kolom Anda
                ->whereHas('user', function ($query) use ($user) {
                    $query->where('departemen_id', $user->departemen_id);
                })
                ->orderByDesc('updated_at')
                ->get();
        }

        return view('pages-rule.dokumen-final', compact('dokumenfinal', 'jenis', 'tipe'));
    }

    public function downloadFinal($id)
    {
        // Ambil induk dokumen berdasarkan ID
        $dokumen = IndukDokumen::find($id);

        // Lakukan validasi jika dokumen tidak ditemukan
        if (!$dokumen) {
            return redirect()->back()->with('error', 'Dokumen tidak ditemukan.');
        }

        // Periksa apakah dokumen memiliki file final
        if (is_null($dokumen->file)) {
            return redirect()->back()->with('error', 'Dokumen belum memiliki file final atau tidak diizinkan untuk diunduh.');
        }

        // Periksa apakah dokumen memiliki status final approved
        if ($dokumen->status != 'approved') {
            return redirect()->back()->with('error', 'Dokumen belum disetujui final atau tidak diizinkan untuk diunduh.');
        }

        // Ambil path file dari database
        $path = $dokumen->file;

        // Periksa apakah path tidak null dan merupakan string
        if (is_null($path) || !is_string($path)) {
            return redirect()->back()->with('error', 'Path file tidak valid.');
        }

        // Periksa apakah file ada di storage
        if (!Storage::disk('public')->exists($path)) {
            return redirect()->back()->with('error', 'File tidak ditemukan di storage.');
        }

        // Tentukan nama file untuk unduhan
        $filename = $dokumen->nomor_dokumen . '_' . $dokumen->nama_dokumen . '.' . pathinfo($path, PATHINFO_EXTENSION);

        // Unduh file dari storage
        return Storage::disk('public')->download($path, $filename);
    }
    public function share_document($jenis, $tipe)
    {
        $user = auth()->user();

        // Jika user adalah admin, mengambil semua dokumen dengan status 'active' sesuai jenis dan tipe
        if ($user->hasRole('admin')) {
            $sharedDocuments = IndukDokumen::select('induk_dokumen.*')
                ->join('dokumen', 'induk_dokumen.dokumen_id', '=', 'dokumen.id')
                ->where('dokumen.jenis_dokumen', $jenis)
                ->where('dokumen.tipe_dokumen', $tipe)
                ->where('induk_dokumen.statusdoc', 'active')
                ->orderBy('induk_dokumen.updated_at', 'desc')
                ->get();
        } else {
            // Jika user bukan admin, mengambil dokumen yang terkait dengan departemen user dan memiliki status 'active' sesuai jenis dan tipe
            $sharedDocuments = IndukDokumen::select('induk_dokumen.*')
                ->join('dokumen', 'induk_dokumen.dokumen_id', '=', 'dokumen.id')
                ->join('document_departement', 'induk_dokumen.id', '=', 'document_departement.induk_dokumen_id')
                ->where('document_departement.departemen_id', $user->departemen_id)
                ->where('dokumen.jenis_dokumen', $jenis)
                ->where('dokumen.tipe_dokumen', $tipe)
                ->where('induk_dokumen.statusdoc', 'active')
                ->orderBy('induk_dokumen.updated_at', 'desc')
                ->get();
        }

        return view('pages-rule.document-shared', compact('sharedDocuments', 'jenis', 'tipe'));
    }
    public function previewAndDownload($id)
    {
        $user = auth()->user();
        $departemen_user = $user->departemen->nama_departemen;

        // Periksa apakah dokumen tersebut dibagikan kepada departemen user yang sedang login
        // dan memiliki status final approved serta kolom file tidak null
        $dokumen = IndukDokumen::where('id', $id)
            ->whereHas('departments', function ($query) use ($departemen_user) {
                $query->where('nama_departemen', $departemen_user);
            })
            ->where('statusdoc', 'active')
            ->whereNotNull('file_pdf')
            ->first();

        if (!$dokumen) {
            return redirect()->back()->with('error', 'Dokumen tidak ditemukan atau tidak diizinkan untuk diakses.');
        }

        // Ambil path file dari database
        $path = $dokumen->file_pdf;

        // Periksa apakah path tidak null dan merupakan string
        if (is_null($path) || !is_string($path)) {
            return redirect()->back()->with('error', 'Path file tidak valid.');
        }

        // Periksa apakah file ada di storage
        if (!Storage::disk('public')->exists($path)) {
            return redirect()->back()->with('error', 'File tidak ditemukan di storage: ' . $path);
        }

        // Tentukan nama file yang akan diunduh
        $downloadFilename = $dokumen->nomor_dokumen . '_' . $dokumen->nama_dokumen . '.' . pathinfo($path, PATHINFO_EXTENSION);

        // Tentukan header untuk download
        $headers = [
            'Content-Type' => mime_content_type(storage_path('app/public/' . $path)),
        ];

        // Periksa jenis file untuk menentukan pratinjau
        $fileExtension = pathinfo($path, PATHINFO_EXTENSION);
        if ($fileExtension == 'pdf') {
            // Tampilkan pratinjau PDF
            return response()->file(storage_path('app/public/' . $path));
        } else {
            // Tampilkan link untuk mengunduh file
            return response()->download(storage_path('app/public/' . $path), $downloadFilename, $headers);
        }
    }
}
