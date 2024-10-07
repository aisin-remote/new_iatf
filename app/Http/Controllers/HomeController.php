<?php

namespace App\Http\Controllers;

use App\Exports\IndukDokumenExport;
use App\Models\Audit;
use App\Models\AuditControl;
use App\Models\Departemen;
use App\Models\DocumentAuditControl;
use App\Models\Dokumen;
use App\Models\IndukDokumen;
use App\Models\ItemAudit;
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

        // Tampilkan form filter tipe dokumen
        $tipeDokumen = Dokumen::all();

        // Jumlah item per halaman
        $perPage = $request->input('per_page', 10);

        // Query dasar untuk data dokumen
        $query = IndukDokumen::query()
            ->join('dokumen', 'induk_dokumen.dokumen_id', '=', 'dokumen.id');

        // Filter berdasarkan status dokumen jika bukan admin
        if (!$user->hasRole('admin')) {
            $query->whereIn('statusdoc', ['active', 'obsolete', 'not yet active'])
                ->where('induk_dokumen.departemen_id', $userDepartemenId); // Filter berdasarkan departemen pengguna
        }

        // Terapkan filter dari query string
        if ($request->filled('date_from') && $request->filled('date_to')) {
            $query->whereBetween('tgl_upload', [$request->date_from, $request->date_to]);
        }

        if ($request->filled('tipe_dokumen_id')) {
            $query->where('dokumen.tipe_dokumen', $request->tipe_dokumen_id);
        }

        if ($request->filled('statusdoc')) {
            $query->where('statusdoc', $request->statusdoc);
        }

        // Ambil data dokumen sesuai dengan query yang sudah difilter dengan pagination
        $dokumenall = $query->paginate($perPage);

        // Format tanggal upload
        $dokumenall->getCollection()->transform(function ($doc) {
            $doc->tgl_upload = \Carbon\Carbon::parse($doc->tgl_upload)->format('d-m-Y');
            return $doc;
        });

        // Query untuk menghitung berdasarkan tipe dokumen
        $countByTypeQuery = IndukDokumen::query()
            ->join('dokumen', 'induk_dokumen.dokumen_id', '=', 'dokumen.id')
            ->select('dokumen.tipe_dokumen', DB::raw('count(*) as count'))
            ->groupBy('dokumen.tipe_dokumen');

        // Jika bukan admin, filter berdasarkan departemen pengguna
        if (!$user->hasRole('admin')) {
            $countByTypeQuery->where('induk_dokumen.departemen_id', $userDepartemenId);
        }
        $countByType = $countByTypeQuery->get();

        // Query untuk menghitung berdasarkan status dan tipe dokumen
        $countByStatusAndTypeQuery = IndukDokumen::query()
            ->join('dokumen', 'induk_dokumen.dokumen_id', '=', 'dokumen.id')
            ->select('dokumen.tipe_dokumen', 'induk_dokumen.status', DB::raw('count(*) as count'))
            ->groupBy('dokumen.tipe_dokumen', 'induk_dokumen.status');

        // Jika bukan admin, filter berdasarkan departemen pengguna
        if (!$user->hasRole('admin')) {
            $countByStatusAndTypeQuery->where('induk_dokumen.departemen_id', $userDepartemenId);
        }
        $countByStatusAndType = $countByStatusAndTypeQuery->get();

        // Menghitung jumlah berdasarkan status tertentu
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
            'allDepartemen',
            'tipeDokumen',
            'perPage'
        ));
    }
    public function dashboard_process()
    {
        return view('pages-process.dashboard');
    }
    public function dashboard_audit(Request $request)
    {
        $user = auth()->user();
        $isAdmin = $user->hasRole('admin');
        $selectedDepartmentId = $request->input('department_id'); // Mengambil ID departemen yang dipilih

        // Ambil semua departemen terkait jika bukan admin
        if ($isAdmin) {
            $auditControls = AuditControl::with(['audit', 'departemen'])->get(); // Ambil semua kontrol audit
        } else {
            $departemenIds = $user->departemens->pluck('id'); // Mengambil semua departemen yang terkait dengan user
            $auditControls = AuditControl::whereIn('departemen_id', $departemenIds)
                ->with(['audit', 'departemen'])
                ->get();
        }

        // Data untuk audit
        $auditData = [];

        // Kelompokkan berdasarkan audit_id dan departemen_id
        foreach ($auditControls as $control) {
            $auditId = $control->audit_id;
            $departemenId = $control->departemen_id;

            // Jika departemen ID yang dipilih ada, filter data berdasarkan itu
            if ($selectedDepartmentId && $selectedDepartmentId != $departemenId) {
                continue; // Lewati jika tidak sesuai dengan ID yang dipilih
            }

            // Jika audit tidak ada, buat entri baru
            if (!isset($auditData[$auditId][$departemenId])) {
                $auditData[$auditId][$departemenId] = [
                    'auditName' => $control->audit->nama ?? 'Unknown Audit',
                    'departemenName' => $control->departemen->nama_departemen ?? 'Unknown Department',
                    'completedTasks' => 0,
                    'notCompletedTasks' => 0,
                    'submittedTasks' => 0
                ];
            }

            // Hitung status tugas
            switch ($control->status) {
                case 'completed':
                    $auditData[$auditId][$departemenId]['completedTasks']++;
                    break;
                case 'uncomplete':
                    $auditData[$auditId][$departemenId]['notCompletedTasks']++;
                    break;
                case 'submitted':
                    $auditData[$auditId][$departemenId]['submittedTasks']++;
                    break;
            }
        }

        // Ambil semua departemen
        $departemens = Departemen::all();

        // Kirim data ke view, gunakan 'departemens' sebagai key
        return view('audit.dashboard', [
            'auditData' => $auditData,
            'isAdmin' => $isAdmin,
            'departemens' => $departemens,
            'selectedDepartmentId' => $selectedDepartmentId, // Kirim ID departemen yang dipilih ke view
        ]);
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
}
