<?php

namespace App\Http\Controllers;

use App\Models\IndukDokumen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function dashboard_rule()
    {
        $dokumenall = IndukDokumen::all();
        // Mengambil count berdasarkan tipe dari tabel induk_dokumen
        $countByType = DB::table('induk_dokumen')
            ->join('dokumen', 'induk_dokumen.dokumen_id', '=', 'dokumen.id')
            ->select('dokumen.tipe_dokumen', DB::raw('count(*) as count'))
            ->groupBy('dokumen.tipe_dokumen')
            ->get();

        // Menghitung jumlah dokumen yang sedang menunggu (waiting) dan yang disetujui (approve)
        $countByStatusAndType = DB::table('induk_dokumen')
            ->join('dokumen', 'induk_dokumen.dokumen_id', '=', 'dokumen.id')
            ->select('dokumen.tipe_dokumen', 'induk_dokumen.status', DB::raw('count(*) as count'))
            ->groupBy('dokumen.tipe_dokumen', 'induk_dokumen.status')
            ->get();
        // Prepare data for the pie chart
        $waitingCount = $countByStatusAndType->where('status', 'waiting')->first()->count ?? 0;
        $approveCount = $countByStatusAndType->where('status', 'approve')->first()->count ?? 0;

        // Mengirimkan data tersebut ke view
        return view('pages-rule.dashboard-admin', compact('countByType', 'waitingCount', 'approveCount','countByStatusAndType','dokumenall'));
    }
}
