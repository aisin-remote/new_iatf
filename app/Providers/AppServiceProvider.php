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
                // Ambil dokumen yang diunggah oleh mereka
                $userUploadedDocuments = IndukDokumen::where('user_id', $user->id);

                // Ambil dokumen yang didistribusikan ke departemen mereka
                $distributedDocuments = IndukDokumen::select('induk_dokumen.*')
                    ->join('document_departement', 'induk_dokumen.id', 'document_departement.induk_dokumen_id')
                    ->where('document_departement.departemen_id', $user->departemen_id)
                    ->where('induk_dokumen.statusdoc', 'active');

                // Gabungkan dua query tersebut menggunakan union
                $documents = $userUploadedDocuments->union($distributedDocuments)
                    ->paginate(10);
            }

            $view->with('documents', $documents);
        });
    }
}
