<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DokumenController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\RuleController;
use App\Http\Controllers\ValidateRuleController;
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

Route::get('/select-dashboard', function () {
    return view('select-dashboard');
});
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
Route::get('download-excel', [HomeController::class, 'downloadExcel'])
    ->middleware(['auth', 'role:admin|guest'])
    ->name('download.excel');

// Template Dokumen
Route::get('/template-dokumen', [DokumenController::class, 'index'])
    ->middleware(['auth', 'role:admin|guest'])
    ->name('template.index');
Route::post('/template-dokumen/add', [DokumenController::class, 'store'])
    ->middleware(['auth', 'role:admin'])
    ->name('template.add');
Route::post('/template-dokumen/edit/{id}', [DokumenController::class, 'edit'])
    ->middleware(['auth', 'role:admin'])
    ->name('template.edit');
Route::get('/template-dokumen/preview-download/{id}', [DokumenController::class, 'previewAndDownload'])
    ->middleware(['auth', 'role:admin|guest'])
    ->name('template.preview-download');

// Document Draft Rule
Route::get('/dokumen/{jenis}/{tipe}', [RuleController::class, 'index'])
    ->middleware(['auth', 'role:admin|guest'])
    ->name('rule.index');
Route::post('/dokumen/draft', [RuleController::class, 'store'])
    ->middleware(['auth', 'role:guest'])
    ->name('tambah.rule');
Route::get('/dokumen/{jenis}/{tipe}/download/{id}', [RuleController::class, 'download_draft'])
    ->middleware(['auth', 'role:admin|guest'])
    ->name('download.draft');
Route::post('/dokumen/{jenis}/{tipe}/edit/{id}', [RuleController::class, 'update'])
    ->middleware(['auth', 'role:guest'])
    ->name('edit.rule');

// Validate Rule
Route::get('/admin/validate-draft/{jenis}/{tipe}', [ValidateRuleController::class, 'validate_index'])
    ->middleware(['auth', 'role:admin'])
    ->name('rule.validate');
Route::post('/dokumen/validate-draft/approve/{id}', [ValidateRuleController::class, 'approveDocument'])
    ->middleware(['auth', 'role:admin'])
    ->name('dokumen.approve');
Route::post('/dokumen/validate-draft/rejected/{id}', [ValidateRuleController::class, 'RejectedDocument'])
    ->middleware(['auth', 'role:admin'])
    ->name('dokumen.rejected');
Route::post('/dokumen/validate-final/approve/{id}', [ValidateRuleController::class, 'finalapproved'])
    ->middleware(['auth', 'role:admin'])
    ->name('final.approve');
Route::post('/dokumen/validate-final/rejected/{id}', [ValidateRuleController::class, 'finalrejected'])
    ->middleware(['auth', 'role:admin'])
    ->name('final.reject');
Route::get('/dokumen/update/{id}/{action}', [ValidateRuleController::class, 'updateStatusDoc'])
    ->middleware(['auth', 'role:admin'])
    ->name('dokumen.update');

// Document Final Rule
Route::post('upload-final/{id}', [RuleController::class, 'uploadFinal'])
    ->middleware(['auth', 'role:guest'])
    ->name('upload.final');
Route::get('/dokumen-final/download/{id}', [RuleController::class, 'DownloadDocFinal'])
    ->middleware(['auth', 'role:admin|guest'])
    ->name('download.doc.final');
Route::get('/dokumen/final', [RuleController::class, 'final_doc'])
    ->middleware(['auth', 'role:admin|guest'])
    ->name('document.final');
Route::get('/dokumen/final/preview-download/{id}', [RuleController::class, 'previewAndDownloadFinal'])
    ->middleware(['auth', 'role:admin|guest'])
    ->name('preview-download.final');



// Document Share
Route::get('/document/share', [RuleController::class, 'share_document'])
    ->middleware(['auth', 'role:admin|guest'])
    ->name('document.share');
Route::get('/document/share/preview-download/{id}', [RuleController::class, 'previewAndDownloadSharedDocument'])
    ->middleware(['auth', 'role:admin|guest'])
    ->name('preview-download.share');

//notifications
Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
Route::post('/mark-as-read/{id}', [NotificationController::class, 'markAsRead'])->name('notifications.markAsRead');
Route::get('/fetch-notifications', [NotificationController::class, 'fetchNotifications'])->name('notifications.fetch');

