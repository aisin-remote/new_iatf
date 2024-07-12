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

            // Ambil dokumen yang sesuai berdasarkan peran pengguna
            if ($user->hasRole('admin')) {
                $documents = IndukDokumen::with(['user.departemen'])
                    ->where(function ($query) {
                        $query->whereNotIn('status', ['final approved'])
                            ->orWhereNotIn('statusdoc', ['active', 'obsolate']);
                    })
                    ->orderByDesc('created_at')
                    ->paginate(10); // Gunakan paginate untuk membuat objek LengthAwarePaginator
            } else {
                $documents = IndukDokumen::where(function ($query) use ($user) {
                    $query->where(function ($q) use ($user) {
                        $q->where('user_id', $user->id)
                            ->whereNotIn('status', ['final approved']);
                    })->orWhere(function ($q) use ($user) {
                        $q->whereHas('user', function ($q2) use ($user) {
                            $q2->where('departemen_id', $user->departemen_id);
                        })->whereNotIn('statusdoc', ['active', 'obsolate']);
                    });
                })
                    ->orderByDesc('created_at')
                    ->paginate(10); // Gunakan paginate untuk membuat objek LengthAwarePaginator
            }

            $view->with('documents', $documents);
        });
    }
}
