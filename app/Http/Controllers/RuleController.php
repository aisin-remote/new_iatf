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

    public function final_doc($jenis, $tipe)
    {
        $user = Auth::user(); // Mendapatkan user yang sedang login

        $kodeProses = RuleCode::all();
        $alldepartmens = Departemen::all();
        $departemens = Departemen::all();

        $uniqueDepartemens = $departemens->unique('code');

        // Jika user adalah admin, mengambil semua dokumen final approved
        if ($user->hasRole('admin')) {
            $dokumenfinal = IndukDokumen::whereIn('status', ['Finish check by MS', 'Approve by MS', 'Obsolete by MS'])
                ->whereHas('dokumen', function ($query) use ($jenis, $tipe) {
                    $query->where('jenis_dokumen', $jenis) // Filter berdasarkan jenis_dokumen
                        ->where('tipe_dokumen', $tipe); // Filter berdasarkan tipe_dokumen
                })
                ->orderByDesc('updated_at')
                ->get();
        } else {
            // Jika user bukan admin, mengambil dokumen final approved yang terkait dengan departemen user
            $dokumenfinal = IndukDokumen::whereIn('status', ['Approve by MS', 'Obsolete by MS'])
                ->whereHas('dokumen', function ($query) use ($jenis, $tipe) {
                    $query->where('jenis_dokumen', $jenis) // Filter berdasarkan jenis_dokumen
                        ->where('tipe_dokumen', $tipe); // Filter berdasarkan tipe_dokumen
                })
                ->whereIn('statusdoc', ['active', 'obsolete']) // Tambahkan kondisi untuk statusdoc
                ->whereNotNull('file_pdf') // Pastikan ini sesuai dengan nama kolom Anda
                ->where(function ($query) use ($user) {
                    // Filter berdasarkan departemen user_id dan departemen_id
                    $query->whereHas('user', function ($query) use ($user) {
                        $query->where('departemen_id', $user->departemen_id);
                    })
                        ->orWhere('departemen_id', $user->departemen_id);
                })
                ->orderByDesc('updated_at')
                ->get();
        }

        return view('pages-rule.dokumen-final', compact('dokumenfinal', 'kodeProses', 'alldepartmens', 'uniqueDepartemens', 'jenis', 'tipe'));
    }
    public function share_document($jenis, $tipe)
    {
        $user = auth()->user();
        $departments = Departemen::all();
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

        return view('pages-rule.document-shared', compact('sharedDocuments', 'jenis', 'tipe', 'departments'));
    }
}
