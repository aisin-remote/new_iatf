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

            if ($user->hasRole('admin')) {
                // Jika user adalah admin, ambil semua data IndukDokumen
                $documents = IndukDokumen::with('user.departemen')
                    ->paginate(10);
            } else {
                // Jika user bukan admin, ambil data IndukDokumen sesuai dengan departemen user
                $documents = IndukDokumen::whereHas('user', function ($query) use ($user) {
                    $query->where('departemen_id', $user->departemen_id);
                })
                    ->paginate(10);
            }

            $view->with('documents', $documents);
        });
    }
}
