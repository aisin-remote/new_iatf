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

        // tampilan form filter tipe dokumen
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
            $query->where('status', 'final approved');
        }

        // Ambil data dokumen sesuai dengan query yang sudah difilter dengan pagination
        $dokumenall = $query->paginate($perPage);

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
        $waitingApproveCount = $countByStatusAndType->where('status', 'waiting approval')->sum('count');
        $draftApproveCount = $countByStatusAndType->where('status', 'draft approved')->sum('count');
        $waitingFinalCount = $countByStatusAndType->where('status', 'waiting final approval')->sum('count');
        $finalApprovedCount = $countByStatusAndType->where('status', 'final approved')->sum('count');

        return view('pages-rule.dashboard', compact(
            'countByType',
            'waitingApproveCount',
            'draftApproveCount',
            'countByStatusAndType',
            'dokumenall',
            'waitingFinalCount',
            'finalApprovedCount',
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
            $notifications = IndukDokumen::whereNotNull('file_draft')
                ->whereNotNull('command')
                ->get();
        } else {
            // Jika user bukan admin, ambil notifikasi berdasarkan user_id dan file_draft diisi
            $notifications = IndukDokumen::where('user_id', $user->id)
                ->whereNotNull('file_draft')
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
        $query = IndukDokumen::where('status', 'final approved');

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
        // Ambil data dari request
        $date_from = $request->input('date_from');
        $date_to = $request->input('date_to');
        $tipe_dokumen_id = $request->input('tipe_dokumen_id');
        $departemen_id = $request->input('departemen_id');
        $statusdoc = $request->input('statusdoc');

        // Query berdasarkan filter yang diterima
        $query = IndukDokumen::query();

        // Filter berdasarkan range tanggal upload jika ada
        if ($date_from && $date_to) {
            $query->whereBetween('tgl_upload', [$date_from, $date_to]);
        }

        // Filter berdasarkan tipe dokumen jika dipilih
        if ($tipe_dokumen_id) {
            $query->where('tipe_dokumen_id', $tipe_dokumen_id);
        }

        // Filter berdasarkan departemen jika dipilih
        if ($departemen_id) {
            $query->whereHas('user.departemen', function ($query) use ($departemen_id) {
                $query->where('nama_departemen', $departemen_id);
            });
        }

        // Filter berdasarkan status dokumen jika dipilih
        if ($statusdoc) {
            $query->where('statusdoc', $statusdoc);
        }

        // Eksekusi query untuk mendapatkan hasil filter
        $filteredDocuments = $query->get();

        // Anda bisa mengembalikan response dalam bentuk JSON atau redirect ke halaman dengan data filter
        return response()->json($filteredDocuments);
    }
}
