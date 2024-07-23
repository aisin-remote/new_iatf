<?php

namespace App\Providers;

use App\Models\IndukDokumen;
use App\Models\User;
use App\Notifications\DocumentStatusChanged;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('partials.notifications', function ($view) {
            $user = Auth::user();
            $departemen_user = $user->departemen->nama_departemen;

            if ($user->hasRole('admin')) {
                $documents = IndukDokumen::with(['user.departemen'])
                    ->where(function ($query) {
                        $query->whereNotIn('status', ['approved'])
                            ->orWhereNotIn('statusdoc', ['active', 'obsolete']);
                    })
                    ->orderByDesc('created_at')
                    ->get();
            } else {
                $documents = IndukDokumen::where(function ($query) use ($user) {
                    $query->where(function ($q) use ($user) {
                        $q->where('user_id', $user->id)
                            ->whereNotIn('status', ['approved']);
                    })->orWhere(function ($q) use ($user) {
                        $q->whereHas('user', function ($q2) use ($user) {
                            $q2->where('departemen_id', $user->departemen_id);
                        })->whereNotIn('statusdoc', ['active', 'obsolete']);
                    });
                })
                    ->orderByDesc('created_at')
                    ->get();
            }

            $sharedDocuments = IndukDokumen::whereHas('departments', function ($query) use ($departemen_user) {
                $query->where('nama_departemen', $departemen_user);
            })
                ->where('statusdoc', 'active')
                ->whereNotNull('file')
                ->orderByDesc('created_at')
                ->get()
                ->each(function ($doc) {
                    $doc->is_shared = true;
                });

            $allDocuments = $documents->merge($sharedDocuments)->sortByDesc('created_at');

            $paginatedDocuments = new \Illuminate\Pagination\LengthAwarePaginator(
                $allDocuments->forPage(\Illuminate\Pagination\Paginator::resolveCurrentPage(), 10),
                $allDocuments->count(),
                10,
                \Illuminate\Pagination\Paginator::resolveCurrentPage(),
                ['path' => \Illuminate\Pagination\Paginator::resolveCurrentPath()]
            );

            $notificationCount = $allDocuments->count();

            $view->with('documents', $paginatedDocuments);
            $view->with('notificationCount', $notificationCount);
        });
    }
}
