<?php

namespace App\Http\Controllers;

use App\Exports\IndukDokumenExport;
use App\Models\Departemen;
use App\Models\Dokumen;
use App\Models\IndukDokumen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class HomeController extends Controller
{
    public function dashboard_rule(Request $request)
    {
        $user = auth()->user();
        $departemen_user = $user->departemen->nama_departemen;
        $allDepartemen = Departemen::all();

        // Tampilkan form filter tipe dokumen
        $tipeDokumen = Dokumen::where('jenis_dokumen', 'rule')->get();

        // Filter berdasarkan departemen
        $departemenFilter = $request->input('departemen');
        if ($departemenFilter) {
            $departemen_user = $departemenFilter;
        }

        // Jumlah item per halaman
        $perPage = $request->input('per_page', 10); // Default to 10 items per page

        // Query dasar untuk data dokumen
        $query = IndukDokumen::query();

        // Hanya jika bukan admin, filter berdasarkan dokumen yang diunggah oleh departemen user
        if (!$user->hasRole('admin')) {
            $query->whereHas('user', function ($query) use ($departemen_user) {
                $query->whereHas('departemen', function ($query) use ($departemen_user) {
                    $query->where('nama_departemen', $departemen_user);
                });
            })->where('statusdoc', 'active');
        } else {
            // Jika admin, tidak perlu filter statusdoc
            $query->where('status', 'Approve by MS');
        }

        // Ambil data dokumen sesuai dengan query yang sudah difilter dengan pagination
        $dokumenall = $query->paginate($perPage);

        // Format tanggal upload
        $dokumenall->getCollection()->transform(function ($doc) {
            $doc->tgl_upload = \Carbon\Carbon::parse($doc->tgl_upload)->format('d-m-Y');
            return $doc;
        });

        // Query untuk menghitung berdasarkan tipe dokumen
        $countByType = IndukDokumen::query()
            ->when(!$user->hasRole('admin'), function ($query) use ($departemen_user) {
                $query->whereHas('user', function ($query) use ($departemen_user) {
                    $query->whereHas('departemen', function ($query) use ($departemen_user) {
                        $query->where('nama_departemen', $departemen_user);
                    });
                });
            })
            ->join('dokumen', 'induk_dokumen.dokumen_id', '=', 'dokumen.id')
            ->select('dokumen.tipe_dokumen', DB::raw('count(*) as count'))
            ->groupBy('dokumen.tipe_dokumen')
            ->get();

        // Query untuk menghitung berdasarkan status dan tipe dokumen
        $countByStatusAndType = IndukDokumen::query()
            ->when(!$user->hasRole('admin'), function ($query) use ($departemen_user) {
                $query->whereHas('user', function ($query) use ($departemen_user) {
                    $query->whereHas('departemen', function ($query) use ($departemen_user) {
                        $query->where('nama_departemen', $departemen_user);
                    });
                });
            })
            ->join('dokumen', 'induk_dokumen.dokumen_id', '=', 'dokumen.id')
            ->select('dokumen.tipe_dokumen', 'induk_dokumen.status', DB::raw('count(*) as count'))
            ->groupBy('dokumen.tipe_dokumen', 'induk_dokumen.status')
            ->get();

        // Menghitung jumlah berdasarkan status tertentu
        $waitingCheckCount = $countByStatusAndType->where('status', 'Waiting check by MS')->sum('count');
        $finishCheckCount = $countByStatusAndType->where('status', 'Finish check by MS')->sum('count');

        return view('pages-rule.dashboard', compact(
            'countByType',
            'waitingCheckCount',
            'finishCheckCount',
            'countByStatusAndType',
            'dokumenall',
            'allDepartemen',
            'tipeDokumen',
            'perPage'
        ));
    }

    public function getNotifications()
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login'); // Redirect jika pengguna belum login
        }

        // Ambil notifikasi dari tabel IndukDokumen
        if ($user->role === 'admin') {
            // Jika user adalah admin, ambil semua notifikasi yang memiliki file_draft diisi
            $notifications = IndukDokumen::whereNotNull('file')
                ->whereNotNull('command')
                ->get();
        } else {
            // Jika user bukan admin, ambil notifikasi berdasarkan user_id dan file diisi
            $notifications = IndukDokumen::where('user_id', $user->id)
                ->whereNotNull('file')
                ->whereNotNull('command')
                ->get();
        }

        return view('partials.notifications', compact('notifications'));
    }
    public function downloadExcel(Request $request)
    {
        $user = auth()->user();
        $departemen_user = $user->departemen->nama_departemen;

        // Query dasar untuk data dokumen yang akan diunduh
        $query = IndukDokumen::where('status', 'approved');

        // Filter berdasarkan status dokumen
        if (!$user->hasRole('admin')) {
            $query->where('statusdoc', 'active');
        }

        // Hanya jika bukan admin, filter berdasarkan departemen user
        if (!$user->hasRole('admin')) {
            $query->whereHas('user', function ($query) use ($departemen_user) {
                $query->whereHas('departemen', function ($query) use ($departemen_user) {
                    $query->where('nama_departemen', $departemen_user);
                });
            });
        }

        // Ambil semua data dokumen sesuai dengan query yang sudah difilter
        $dokumen = $query->get();

        // Ambil kolom yang dipilih oleh pengguna
        $columns = $request->input('columns', ['id', 'nama_dokumen', 'status', 'statusdoc', 'user_id']); // Default columns if none selected

        // Generate nama file untuk download
        $fileName = 'dokumen_' . date('Ymd_His') . '.xlsx';

        // Export data ke file Excel dan langsung download
        return Excel::download(new IndukDokumenExport($dokumen, $columns), $fileName);
    }
    public function filterDocuments(Request $request)
    {
        $query = IndukDokumen::query();

        // Filter berdasarkan Tanggal Upload
        if ($request->filled('date_from') && $request->filled('date_to')) {
            $query->whereBetween('tgl_upload', [$request->date_from, $request->date_to]);
        }

        // Filter berdasarkan Tipe Dokumen
        if ($request->filled('tipe_dokumen_id')) {
            $query->where('tipe_dokumen_id', $request->tipe_dokumen_id);
        }

        // Filter berdasarkan Departemen (Hanya untuk admin)
        if (auth()->user()->hasRole('admin') && $request->filled('departemen_id')) {
            $query->whereHas('user.departemen', function ($q) use ($request) {
                $q->where('nama_departemen', $request->departemen_id);
            });
        }

        // Filter berdasarkan Status Dokumen
        if ($request->filled('statusdoc')) {
            $query->where('statusdoc', $request->statusdoc);
        }

        $dokumenall = $query->paginate($request->get('per_page', 10));

        return view('dashboard', compact('dokumenall'))
            ->with('tipeDokumen', TipeDokumen::all())
            ->with('allDepartemen', Departemen::all())
            ->with('countByType', $this->getDocumentCounts());
    }
}
