<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DocruleController;
use App\Http\Controllers\DokumenController;
use App\Http\Controllers\GuestController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

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
        Route::put('/dokumen/upload/{jenis}/{tipe}', [DokumenController::class, 'upload'])->name('dokumen.upload');
        Route::get('/admin/validate-dokumen/{jenis}/{tipe}', [DocruleController::class, 'validate_index'])->name('rule.validate');
        Route::post('/dokumen/approve/{id}', [DocruleController::class, 'approveDocument'])
            ->name('dokumen.approve');
        Route::post('/dokumen/rejected/{id}', [DocruleController::class, 'RejectedDocument'])
            ->name('dokumen.rejected');
    });
    Route::group(['middleware' => ['role:guest']], function () {
        Route::get('/guest/dokumen/{jenis}/{tipe}', [DocruleController::class, 'index'])->name('rule.index');
        Route::post('/guest/dokumen/tambah', [DocruleController::class, 'store'])->name('tambah.rule');
        Route::post('/guest/dokumen/{jenis}/{tipe}/edit/{id}', [DocruleController::class, 'update'])->name('edit.rule');
    });
    Route::get('/dokumen/{jenis}/{tipe}/download/{id}', [DocruleController::class, 'download'])
    ->middleware(['auth', 'role:admin|guest'])
    ->name('download.rule');
    Route::get('/dokumen/download/{jenis}/{tipe}', [DokumenController::class, 'download'])
    ->middleware(['auth', 'role:admin|guest'])
    ->name('dokumen.download');
    Route::get('/admin/dashboard-rule', [HomeController::class, 'dashboard_rule'])
    ->middleware(['auth', 'role:admin|guest'])
    ->name('dashboard.rule');

});
