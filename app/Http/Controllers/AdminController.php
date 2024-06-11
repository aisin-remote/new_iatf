<?php

namespace App\Http\Controllers;

use App\Models\IndukDokumen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function dashboard_rule()
    {
        // Mengambil count berdasarkan tipe dari tabel induk_dokumen
        $countByType = DB::table('induk_dokumen')
            ->join('dokumen', 'induk_dokumen.dokumen_id', '=', 'dokumen.id')
            ->select('dokumen.tipe_dokumen', DB::raw('count(*) as count'))
            ->groupBy('dokumen.tipe_dokumen')
            ->get();

        // Mengirimkan data tersebut ke view
        return view('pages-rule.dashboard-admin', compact('countByType'));
    }
}
