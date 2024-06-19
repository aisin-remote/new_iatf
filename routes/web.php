<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DocruleController;
use App\Http\Controllers\DokumenController;
use App\Http\Controllers\GuestController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\RegisterController;
use Illuminate\Support\Facades\Route;
use League\CommonMark\Node\Block\Document;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// login register 
Route::get('/', function () {
    return redirect('/login');
});
Route::get('/login', [AuthController::class, 'index'])->name('login');
Route::post('/login-proses', [AuthController::class, 'login_proses'])->name('login.proses');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/register', [AuthController::class, 'register_form'])->name('register');
Route::post('/register-proses', [AuthController::class, 'register_proses'])->name('register.proses');

Route::middleware(['auth'])->group(function () {
    Route::group(['middleware' => ['role:admin']], function () {
        Route::get('/admin/validate-dokumen/{jenis}/{tipe}', [DocruleController::class, 'validate_index'])->name('rule.validate');
        Route::post('/dokumen/approve/{id}', [DocruleController::class, 'approveDocument'])
            ->name('dokumen.approve');
        Route::post('/dokumen/rejected/{id}', [DocruleController::class, 'RejectedDocument'])
            ->name('dokumen.rejected');
        Route::get('/template-dokumen/add', [DokumenController::class, 'store'])
            ->name('template.add');
        Route::post('/template-dokumen/edit/{id}', [DokumenController::class, 'edit'])->name('template.edit');
    });
    Route::group(['middleware' => ['role:guest']], function () {
        Route::get('/dokumen/{jenis}/{tipe}', [DocruleController::class, 'index'])->name('rule.index');
        Route::post('/dokumen/draft', [DocruleController::class, 'store'])->name('tambah.rule');
        Route::post('/dokumen/{jenis}/{tipe}/edit/{id}', [DocruleController::class, 'update'])->name('edit.rule');
        Route::post('/dokumen/final/{id}', [DocruleController::class, 'final_upload'])->name('final.rule');
    });

    // Template Dokumen
    Route::get('/template-dokumen', [DokumenController::class, 'index'])
        ->middleware(['auth', 'role:admin|guest'])
        ->name('template.index');
    Route::get('/template-dokumen/download/{id}', [DokumenController::class, 'download'])
        ->middleware(['auth', 'role:admin|guest'])
        ->name('template.download');


    // Dashboard rule
    Route::get('/dashboard-rule', [HomeController::class, 'dashboard_rule'])
        ->middleware(['auth', 'role:admin|guest'])
        ->name('dashboard.rule');
    Route::get('/notifications', [HomeController::class, 'getNotifications'])
        ->middleware(['auth', 'role:admin|guest'])
        ->name('notifications');

    // Document Rule
    Route::get('/dokumen/{jenis}/{tipe}/download/{id}', [DocruleController::class, 'download'])
        ->name('download.rule');

    // Document Share
    Route::get('/document-shared', [DocruleController::class, 'share_document'])
        ->middleware(['auth', 'role:admin|guest'])
        ->name('document.share');
});
