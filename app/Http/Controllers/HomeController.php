<?php

namespace App\Http\Controllers;

use App\Models\IndukDokumen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function dashboard_rule()
    {
        $user = auth()->user();

        if ($user->hasRole('admin')) {
            // Admin melihat semua data
            $dokumenall = IndukDokumen::all();
            $countByType = DB::table('induk_dokumen')
                ->join('dokumen', 'induk_dokumen.dokumen_id', '=', 'dokumen.id')
                ->select('dokumen.tipe_dokumen', DB::raw('count(*) as count'))
                ->groupBy('dokumen.tipe_dokumen')
                ->get();

            $countByStatusAndType = DB::table('induk_dokumen')
                ->join('dokumen', 'induk_dokumen.dokumen_id', '=', 'dokumen.id')
                ->select('dokumen.tipe_dokumen', 'induk_dokumen.status', DB::raw('count(*) as count'))
                ->groupBy('dokumen.tipe_dokumen', 'induk_dokumen.status')
                ->get();
        } else {
            // Pengguna biasa melihat data berdasarkan departemen mereka
            $departemen_user = $user->departemen->nama_departemen;

            $dokumenall = IndukDokumen::whereHas('user', function ($query) use ($departemen_user) {
                $query->whereHas('departemen', function ($query) use ($departemen_user) {
                    $query->where('nama_departemen', $departemen_user);
                });
            })->get();

            $countByType = DB::table('induk_dokumen')
                ->join('dokumen', 'induk_dokumen.dokumen_id', '=', 'dokumen.id')
                ->join('users', 'induk_dokumen.user_id', '=', 'users.id')
                ->join('departemen', 'users.departemen_id', '=', 'departemen.id')
                ->where('departemen.nama_departemen', $departemen_user)
                ->select('dokumen.tipe_dokumen', DB::raw('count(*) as count'))
                ->groupBy('dokumen.tipe_dokumen')
                ->get();

            $countByStatusAndType = DB::table('induk_dokumen')
                ->join('dokumen', 'induk_dokumen.dokumen_id', '=', 'dokumen.id')
                ->join('users', 'induk_dokumen.user_id', '=', 'users.id')
                ->join('departemen', 'users.departemen_id', '=', 'departemen.id')
                ->where('departemen.nama_departemen', $departemen_user)
                ->select('dokumen.tipe_dokumen', 'induk_dokumen.status', DB::raw('count(*) as count'))
                ->groupBy('dokumen.tipe_dokumen', 'induk_dokumen.status')
                ->get();
        }

        $waitingCount = $countByStatusAndType->where('status', 'waiting')->sum('count');
        $approveCount = $countByStatusAndType->where('status', 'approved')->sum('count');
        $rejectCount = $countByStatusAndType->where('status', 'rejected')->sum('count');

        return view('pages-rule.dashboard', compact('countByType', 'waitingCount', 'approveCount', 'countByStatusAndType', 'dokumenall', 'rejectCount'));
    }
}
