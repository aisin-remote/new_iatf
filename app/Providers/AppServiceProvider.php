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
                $documents = IndukDokumen::with(['user.departemen', 'distributions.departemen'])
                    ->paginate(10);
            } else {
                if ($user->hasRole('guest')) {
                    // Jika user adalah pengguna biasa, ambil data IndukDokumen yang diunggah oleh mereka
                    $documents = IndukDokumen::where('user_id', $user->id)
                        ->paginate(10);
                } else {
                    // Jika user bukan admin atau pengguna biasa, ambil data IndukDokumen yang didistribusikan ke departemen mereka
                    $documents = IndukDokumen::select('induk_dokumen.*')
                        ->join('document_departement', 'induk_dokumen.id', 'document_departement.induk_dokumen_id')
                        ->where('document_departement.departemen_id', $user->departemen_id)
                        ->where('induk_dokumen.statusdoc', 'active')
                        ->paginate(10);
                }
            }

            $view->with('documents', $documents);
        });
    }
}
