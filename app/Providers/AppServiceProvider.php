<?php

namespace App\Providers;

use App\Models\IndukDokumen;
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
        View::composer('*', function ($view) {
            $user = Auth::user();

            if ($user) {
                $notifications = IndukDokumen::where('user_id', $user->id)
                    ->whereNotNull('command')
                    ->get();
            } else {
                $notifications = collect(); // Kosongkan koleksi jika tidak ada pengguna
            }

            $view->with('notifications', $notifications);
        });
    }
}
