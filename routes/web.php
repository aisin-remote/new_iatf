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
        Route::post('/admin/dokumen-prosedur', [DocruleController::class, 'store'])->name('admin-tambah-dokumen-prosedur');
        Route::get('/admin/dokumen-prosedur/download/{id}', [DocruleController::class, 'download'])->name('admin-download-dokumen');
        Route::put('/admin/dokumen-prosedur/update/{id}', [DocruleController::class, 'update'])->name('admin-update-dokumen');
        Route::delete('/admin/dokumen-prosedur/delete/{id}', [DocruleController::class, 'destroy'])->name('admin-delete-dokumen');
        Route::get('/admin/template-dokumen/{tipe_dokumen}', [AdminController::class, 'index'])->name('admin-template-dokumen');
        Route::post('/admin/template-dokumen/add', [DocruleController::class, 'store'])->name('admin-add-template');
        Route::put('/admin/template-dokumen/update/{id}', [AdminController::class, 'update'])->name('admin-update-template');
        Route::get('/admin/template-dokumen/download/{id}', [AdminController::class, 'download'])->name('admin-download-template');
    });
    Route::group(['middleware' => ['role:guest']], function () {     
        Route::get('/guest/dashboard-rule', [GuestController::class, 'dashboard_rule'])->name('guest.dashboard.rule');
    });
});
