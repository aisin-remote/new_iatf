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
            ->whereHas('indukDokumen', function ($query) {})
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
        $request->validate([
            'file' => 'required|mimes:doc,docx,xls,xlsx|max:10240',
        ], [
            'file.mimes' => 'Only Word and Excel files are allowed.',
        ]);
        // Simpan file
        $file = $request->file('file');
        $filename = $file->getClientOriginalName();
        $path = 'draft-rule/' . $filename;
        $file->storeAs('draft-rule', $filename, 'public'); // Simpan di direktori 'draft-rule' dalam storage 'public'

        // Ambil informasi user
        $userId = auth()->id();
        $user = auth()->user();
        $departemen_user_code = $user->departemen->code;
        $departemen_id = $user->departemen->id;
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
            return redirect()->back()->with('error', 'Invalid type and document type.');
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
        $dokumen->departemen_id = $departemen_id;
        $dokumen->status = 'Waiting check by MS';
        $dokumen->comment = 'Document "' . $dokumen->nama_dokumen . '" has been uploaded.';
        $dokumen->save();

        // Jika ada departemen yang dipilih, kaitkan dokumen dengan departemen tersebut
        if ($request->has('kode_departemen')) {
            $departemenCodes = $request->input('kode_departemen');
            $departemens = Departemen::whereIn('code', $departemenCodes)->get();
            $dokumen->departments()->sync($departemens->pluck('id')); // Menggunakan sync() untuk update relasi
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
            $query->whereIn('induk_dokumen.status', ['Finish check by MS', 'Approve by MS', 'Obsolete by MS']);
        } else {
            $query->whereIn('induk_dokumen.status', ['Approve by MS', 'Obsolete by MS'])
                ->whereIn('induk_dokumen.statusdoc', ['active', 'obsolete']) // Tambahkan kondisi untuk statusdoc
                ->whereNotNull('induk_dokumen.file_pdf') // Pastikan ini sesuai dengan nama kolom Anda
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
}
