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
Route::get('/', function () {
    return redirect('/login');
});
Route::get('/select-dashboard', [AuthController::class, 'select_dashboard'])->name('select.dashboard');
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
Route::post('/filter-documents', [HomeController::class, 'filterDocuments'])
    ->middleware(['auth', 'role:admin|guest'])
    ->name('filter.documents');

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
Route::get('/template-dokumen/preview/{id}', [DokumenController::class, 'preview'])
    ->middleware(['auth', 'role:admin|guest'])
    ->name('template.preview');
Route::get('/template-dokumen/download/{id}', [DokumenController::class, 'download'])
    ->middleware(['auth', 'role:admin|guest'])
    ->name('template.download');

// Document Draft Rule
Route::get('/dokumen/{jenis}/{tipe}', [RuleController::class, 'index'])
    ->middleware(['auth', 'role:admin|guest'])
    ->name('rule.index');
Route::post('/dokumen/draft', [RuleController::class, 'store'])
    ->middleware(['auth', 'role:guest'])
    ->name('tambah.rule');
Route::get('/dokumen/{jenis}/{tipe}/download/{id}', [RuleController::class, 'download'])
    ->middleware(['auth', 'role:admin|guest'])
    ->name('download.rule');

// Validate Rule
Route::get('/admin/validate-draft/{jenis}/{tipe}', [ValidateRuleController::class, 'validate_index'])
    ->middleware(['auth', 'role:admin'])
    ->name('rule.validate');
Route::post('/dokumen/validate/approve/{id}', [ValidateRuleController::class, 'approveDocument'])
    ->middleware(['auth', 'role:admin'])
    ->name('dokumen.approve');
Route::post('/dokumen/validate/rejected/{id}', [ValidateRuleController::class, 'RejectedDocument'])
    ->middleware(['auth', 'role:admin'])
    ->name('dokumen.rejected');
Route::post('/dokumen/{id}/activate', [ValidateRuleController::class, 'activateDocument'])->name('activate.document');
Route::post('/dokumen/{id}/obsolete', [ValidateRuleController::class, 'obsoleteDocument'])->name('obsolete.document');



// Document Final Rule
Route::post('upload-final/{id}', [ValidateRuleController::class, 'uploadFinal'])
    ->middleware(['auth', 'role:admin'])
    ->name('upload.final');
Route::get('/dokumen/final/{jenis}/{tipe}', [RuleController::class, 'final_doc'])
    ->middleware(['auth', 'role:admin|guest'])
    ->name('document.final');
Route::get('/document/{id}', [ValidateRuleController::class, 'previewsAndDownload'])
    ->name('document.previewsAndDownload');
Route::get('/documents/{id}/download-watermarked', [ValidateRuleController::class, 'downloadWatermarkedDocument'])->name('documents.downloadWatermarked');


// Document Share
Route::get('/document/share/{jenis}/{tipe}', [RuleController::class, 'share_document'])
    ->middleware(['auth', 'role:admin|guest'])
    ->name('document.share');
Route::get('/document/{id}/preview-and-download', [RuleController::class, 'previewAndDownload'])
    ->middleware(['auth', 'role:admin|guest'])
    ->name('previewAndDownload.share');

//notifications
Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
