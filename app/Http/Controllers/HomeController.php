<?php

namespace App\Http\Controllers;

use App\Models\IndukDokumen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function dashboard_rule()
    {
        $user = auth()->user();

        if ($user->hasRole('admin')) {
            $dokumenall = IndukDokumen::where('status', 'final approved')->get();
        } else {
            $departemen_user = $user->departemen->nama_departemen;

            $dokumenall = IndukDokumen::where('status', 'final approved')
                ->whereHas('user', function ($query) use ($departemen_user) {
                    $query->whereHas('departemen', function ($query) use ($departemen_user) {
                        $query->where('nama_departemen', $departemen_user);
                    });
                })->get();
        }

        if ($user->hasRole('admin')) {
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
            $departemen_user = $user->departemen->nama_departemen;

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
        $finalApprovedCount = $countByStatusAndType->where('status', 'final approved')->sum('count');

        return view('pages-rule.dashboard', compact(
            'countByType',
            'waitingCount',
            'approveCount',
            'countByStatusAndType',
            'dokumenall',
            'rejectCount',
            'finalApprovedCount'
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
}
