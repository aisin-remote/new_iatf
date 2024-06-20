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

// Dashboard rule
Route::get('/dashboard-rule', [HomeController::class, 'dashboard_rule'])
    ->middleware(['auth', 'role:admin|guest'])
    ->name('dashboard.rule');
Route::get('/notifications', [HomeController::class, 'getNotifications'])
    ->middleware(['auth', 'role:admin|guest'])
    ->name('notifications');

// Template Dokumen
Route::get('/template-dokumen', [DokumenController::class, 'index'])
    ->middleware(['auth', 'role:admin|guest'])
    ->name('template.index');
Route::get('/template-dokumen/add', [DokumenController::class, 'store'])
    ->middleware(['auth', 'role:admin'])
    ->name('template.add');
Route::post('/template-dokumen/edit/{id}', [DokumenController::class, 'edit'])
    ->middleware(['auth', 'role:admin'])
    ->name('template.edit');
Route::get('/template-dokumen/download/{id}', [DokumenController::class, 'download'])
    ->middleware(['auth', 'role:admin|guest'])
    ->name('template.download');

// Document Draft Rule
Route::get('/dokumen/{jenis}/{tipe}', [DocruleController::class, 'index'])
    ->middleware(['auth', 'role:admin|guest'])
    ->name('rule.index');
Route::post('/dokumen/draft', [DocruleController::class, 'store'])
    ->middleware(['auth', 'role:guest'])
    ->name('tambah.rule');
Route::get('/dokumen/{jenis}/{tipe}/download/{id}', [DocruleController::class, 'download_draft'])
    ->middleware(['auth', 'role:admin|guest'])
    ->name('download.rule');
Route::post('/dokumen/{jenis}/{tipe}/edit/{id}', [DocruleController::class, 'update'])->name('edit.rule');

// Validate Draft Rule
Route::get('/admin/validate-draft/{jenis}/{tipe}', [DocruleController::class, 'validate_index'])->name('rule.validate');
Route::post('/dokumen/validate-draft/approve/{id}', [DocruleController::class, 'approveDocument'])
    ->name('dokumen.approve');
Route::post('/dokumen/validate-draft/rejected/{id}', [DocruleController::class, 'RejectedDocument'])
    ->name('dokumen.rejected');

// Document Final Rule
Route::get('/dokumen/final', [DocruleController::class, 'final_doc'])
    ->middleware(['auth', 'role:admin|guest'])
    ->name('document.final');
Route::post('/dokumen/final/{id}', [DocruleController::class, 'final_upload'])->name('final.rule');

// Validate Final Rule
Route::get('/admin/validate-final/{jenis}/{tipe}', [DocruleController::class, 'validate_final'])->name('final.validate');
Route::post('/dokumen/validate-final/approve/{id}', [DocruleController::class, 'finalapproved'])
    ->name('final.approve');
Route::post('/dokumen/validate-final/rejected/{id}', [DocruleController::class, 'finalrejected'])
    ->name('final.rejected');

// Document Share
Route::get('/document/share', [DocruleController::class, 'share_document'])
    ->middleware(['auth', 'role:admin|guest'])
    ->name('document.share');
Route::get('/document/share/download/{id}', [DocruleController::class, 'downloadSharedDocument'])
    ->middleware(['auth', 'role:admin|guest'])
    ->name('download.share');
