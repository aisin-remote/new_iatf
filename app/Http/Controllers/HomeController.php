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
        $userDepartemenId = $user->departemen_id; // Ambil ID departemen pengguna

        $allDepartemen = Departemen::all();
        $user = auth()->user();
        // Jumlah item per halaman
        $perPage = $request->input('per_page', 10);

        // Query dasar untuk data dokumen
        $query = IndukDokumen::query()
            ->join('dokumen', 'induk_dokumen.dokumen_id', '=', 'dokumen.id')
            ->where('induk_dokumen.departemen_id', $selectedDepartemenId);

        // Filter berdasarkan status dokumen jika bukan admin
        if (!$user->hasRole('admin')) {
            $query->whereIn('induk_dokumen.statusdoc', ['active', 'obsolete', 'not yet active']);
        }

        // Filter berdasarkan departemen pengguna jika bukan admin
        if (!$user->hasRole('admin')) {
            $query->where('induk_dokumen.departemen_id', $userDepartemenId);
        }

        // Filter berdasarkan tanggal upload
        if ($request->filled('date_from') && $request->filled('date_to')) {
            $query->whereBetween('induk_dokumen.tgl_upload', [$request->date_from, $request->date_to]);
        }

        // Filter berdasarkan tipe dokumen
        if ($request->filled('tipe_dokumen_id')) {
            $query->where('dokumen.tipe_dokumen', $request->tipe_dokumen_id);
        }

        // Filter berdasarkan status dokumen
        if ($request->filled('statusdoc')) {
            $query->where('induk_dokumen.statusdoc', $request->statusdoc);
        }

        // Ambil data dokumen sesuai dengan query yang sudah difilter dengan pagination
        $dokumenall = $query->paginate($perPage);

        // Format tanggal upload
        $dokumenall->getCollection()->transform(function ($doc) {
            $doc->tgl_upload = \Carbon\Carbon::parse($doc->tgl_upload)->format('d-m-Y');
            return $doc;
        });

        // Query untuk menghitung dokumen berdasarkan tipe
        $countByType = IndukDokumen::query()
            ->join('dokumen', 'induk_dokumen.dokumen_id', '=', 'dokumen.id')
            ->select('dokumen.tipe_dokumen', DB::raw('count(*) as count'))
            ->where('induk_dokumen.departemen_id', $selectedDepartemenId)
            ->groupBy('dokumen.tipe_dokumen')
            ->get();

        // Query untuk menghitung dokumen berdasarkan status dan tipe
        $countByStatusAndType = IndukDokumen::query()
            ->join('dokumen', 'induk_dokumen.dokumen_id', '=', 'dokumen.id')
            ->select('dokumen.tipe_dokumen', 'induk_dokumen.status', DB::raw('count(*) as count'))
            ->where('induk_dokumen.departemen_id', $selectedDepartemenId)
            ->groupBy('dokumen.tipe_dokumen', 'induk_dokumen.status')
            ->get();

        // Menghitung jumlah dokumen berdasarkan status tertentu
        $waitingCheckCount = $countByStatusAndType->where('status', 'Waiting check by MS')->sum('count');
        $finishCheckCount = $countByStatusAndType->where('status', 'Finish check by MS')->sum('count');
        $activeCount = $countByStatusAndType->where('status', 'Approve by MS')->sum('count');
        $obsolateCount = $countByStatusAndType->where('status', 'Obsolete by MS')->sum('count');

        return view('pages-rule.dashboard', compact(
            'countByType',
            'waitingCheckCount',
            'finishCheckCount',
            'activeCount',
            'obsolateCount',
            'countByStatusAndType',
            'dokumenall',
            'tipeDokumen',
            'perPage',
            'activeDepartemen',
            'allDepartemen'
        ));
    }

    public function getNotifications()
    {
        $user = auth()->user();

        if (!$user || !$user->departments) {
            // Tangani jika tidak ada data departemen
            return redirect()->route('error.page'); // Ganti dengan rute error atau tampilkan pesan
        }

        $departemen_user = $user->departments->pluck('nama_departemen')->first();

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
        $query = IndukDokumen::query();

        // Filter berdasarkan status dokumen jika bukan admin
        if (!$user->hasRole('admin')) {
            $query->whereIn('statusdoc', ['active', 'obsolete', 'not yet active']);
        }

        // Filter berdasarkan departemen user jika bukan admin
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

    // Fungsi untuk menghitung jumlah dokumen berdasarkan tipe
    private function getDocumentCounts()
    {
        return IndukDokumen::selectRaw('count(*) as count, dokumen.tipe_dokumen_id')
            ->join('dokumen', 'induk_dokumen.dokumen_id', '=', 'dokumen.id')
            ->groupBy('dokumen.tipe_dokumen_id')
            ->get();
    }
    public function switchDepartemen(Request $request)
    {
        $user = auth()->user();
        $departemenId = $request->input('department_id');

        // Validasi ID departemen
        $departemen = Departemen::find($departemenId);
        // dd($departemen);

        if ($departemen) {
            // Update session dengan departemen yang dipilih
            session(['active_departemen_name' => $departemen->nama_departemen]);

            // Redirect ke dashboard atau halaman lain
            return redirect()->route('dashboard.rule');
        }


        // Jika departemen tidak ditemukan atau tidak dimiliki pengguna
        return redirect()->back()->withErrors(['department_id' => 'Invalid department.']);
    }
}
