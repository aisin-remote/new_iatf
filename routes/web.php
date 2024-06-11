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
        Route::get('/admin/dashboard-rule', [AdminController::class, 'dashboard_rule'])->name('admin.dashboard.rule');
        Route::get('/admin/dashboard-proses', [AdminController::class, 'dashboard_proses'])->name('admin-dashboard-proses');
        Route::get('/admin/dokumen/{jenis}/{tipe}', [DocruleController::class, 'index'])->name('rule.index');
        Route::put('/dokumen/upload/{jenis}/{tipe}', [DokumenController::class, 'upload'])->name('dokumen.upload');
        Route::get('/dokumen/download/{jenis}/{tipe}', [DokumenController::class, 'download'])->name('dokumen.download');
        Route::post('/admin/dokumen/tambah', [DocruleController::class, 'store'])->name('tambah.rule');
        Route::post('/admin/dokumen/{jenis}/{tipe}/edit/{id}', [DocruleController::class, 'update'])->name('edit.rule');
        Route::get('/admin/dokumen/{jenis}/{tipe}/download/{id}', [DocruleController::class, 'download'])->name('download.rule');
    });
    Route::group(['middleware' => ['role:guest']], function () {
        Route::get('/guest/dashboard-rule', [GuestController::class, 'dashboard_rule'])->name('guest.dashboard.rule');
    });
});
