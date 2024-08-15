<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DokumenController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\masterDataController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ReminderAuditController;
use App\Http\Controllers\RuleController;
use App\Http\Controllers\ValidateRuleController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

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
Route::get('/comingsoon', function () {
    return view('comingsoon');
})->name('comingsoon');
Route::get('/switch-departemen/{id}', [AuthController::class, 'switchDepartemen'])->name('switch.departemen');
Route::post('/switchdepartemens', [HomeController::class, 'switchDepartemen'])->name('home.switch.departemen');


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
Route::get('/filter-documents', [HomeController::class, 'filterDocuments'])
    ->middleware(['auth', 'role:admin|guest'])
    ->name('filter.documents');

//master data
Route::get('/master-data', [masterDataController::class, 'index'])
    ->middleware(['auth', 'role:admin'])
    ->name('masterdata');
Route::get('/master-data/departemen', [masterDataController::class, 'index_departemen'])
    ->middleware(['auth', 'role:admin'])
    ->name('masterdata.departemen');
Route::get('/master-data/process-code', [masterDataController::class, 'index_prosescode'])
    ->middleware(['auth', 'role:admin'])
    ->name('masterdata.kodeproses');
Route::get('/master-data/role', [masterDataController::class, 'index_role'])
    ->middleware(['auth', 'role:admin'])
    ->name('masterdata.role');
Route::post('/master-data/departemen/add', [masterDataController::class, 'store_departemen'])
    ->middleware(['auth', 'role:admin'])
    ->name('add.departemen');
Route::post('/master-data/process-code/add', [masterDataController::class, 'store_kodeproses'])
    ->middleware(['auth', 'role:admin'])
    ->name('add.kodeproses');
Route::post('/master-data/role/add', [masterDataController::class, 'store_role'])
    ->middleware(['auth', 'role:admin'])
    ->name('add.role');
Route::post('/master-data/departemen/update/{id}', [masterDataController::class, 'update_departemen'])
    ->middleware(['auth', 'role:admin'])
    ->name('update.departemen');
Route::post('/master-data/process-code/update/{id}', [masterDataController::class, 'update_kodeproses'])
    ->middleware(['auth', 'role:admin'])
    ->name('update.kodeproses');
Route::delete('/master-data/departemen/delete/{id}', [masterDataController::class, 'delete_departemen'])
    ->middleware(['auth', 'role:admin'])
    ->name('delete.departemen');
Route::delete('/master-data/process-code/delete/{id}', [masterDataController::class, 'delete_kodeproses'])
    ->middleware(['auth', 'role:admin'])
    ->name('delete.kodeproses');
Route::delete('/master-data/role/delete/{id}', [masterDataController::class, 'delete_role'])
    ->middleware(['auth', 'role:admin'])
    ->name('delete.role');
Route::get('/reminder', [ReminderAuditController::class, 'reminder'])
    ->middleware(['auth', 'role:admin'])
    ->name('reminder');


// Template Dokumen
Route::get('/template-documents', [DokumenController::class, 'index'])
    ->middleware(['auth', 'role:admin|guest'])
    ->name('template.index');
Route::post('/template-documents/add', [DokumenController::class, 'store'])
    ->middleware(['auth', 'role:admin'])
    ->name('template.add');
Route::post('/template-documents/edit/{id}', [DokumenController::class, 'edit'])
    ->middleware(['auth', 'role:admin'])
    ->name('template.edit');
Route::get('/template-documents/preview/{id}', [DokumenController::class, 'preview'])
    ->middleware(['auth', 'role:admin|guest'])
    ->name('template.preview');
Route::get('/template-documents/download/{id}', [DokumenController::class, 'download'])
    ->middleware(['auth', 'role:admin|guest'])
    ->name('template.download');

// Document Draft Rule
Route::get('/documents/{jenis}/{tipe}', [RuleController::class, 'index'])
    ->middleware(['auth', 'role:admin|guest'])
    ->name('rule.index');
Route::post('/documents/draftRule', [RuleController::class, 'store'])
    ->middleware(['auth', 'role:guest'])
    ->name('tambah.rule');
Route::get('/download/rule/{id}', [RuleController::class, 'download'])
    ->middleware(['auth', 'role:admin|guest'])
    ->name('download.rule');

// Validate Rule
Route::get('/validate-draft/{jenis}/{tipe}', [ValidateRuleController::class, 'validate_index'])
    ->middleware(['auth', 'role:admin'])
    ->name('rule.validate');
Route::post('/validate/approve/{id}', [ValidateRuleController::class, 'approveDocument'])
    ->middleware(['auth', 'role:admin'])
    ->name('dokumen.approve');
Route::post('/documents/validate/{id}/activate', [ValidateRuleController::class, 'activateDocument'])
    ->middleware(['auth', 'role:admin'])
    ->name('activate.document');
Route::post('/documents/validate/{id}/obsolete', [ValidateRuleController::class, 'obsoleteDocument'])
    ->middleware(['auth', 'role:admin'])
    ->name('obsolete.document');

// Document Final Rule
Route::get('/documents/final/{jenis}/{tipe}', [RuleController::class, 'final_doc'])
    ->middleware(['auth', 'role:admin|guest'])
    ->name('documents.final');
Route::post('documents/final/upload/{id}', [ValidateRuleController::class, 'uploadFinal'])
    ->middleware(['auth', 'role:admin'])
    ->name('upload.final');
Route::get('/documents/final/preview-final/{id}', [ValidateRuleController::class, 'previewFinal'])
    ->middleware(['auth', 'role:admin|guest'])
    ->name('documents.previewFinal');
Route::get('/documents/final/preview-active/{id}', [ValidateRuleController::class, 'previewActive'])
    ->middleware(['auth', 'role:admin|guest'])
    ->name('documents.previewActive');
Route::get('/documents/final/preview-obsolete/{id}', [ValidateRuleController::class, 'previewObsolete'])
    ->middleware(['auth', 'role:admin|guest'])
    ->name('documents.previewObsolete');
Route::post('/documents/final/upload-oldDocument', [ValidateRuleController::class, 'upload_old_doc'])
    ->middleware(['auth', 'role:admin'])
    ->name('add.oldDoc');


// Document Share
Route::get('/documents/share/{jenis}/{tipe}', [RuleController::class, 'share_document'])
    ->middleware(['auth', 'role:admin|guest'])
    ->name('document.share');

//notifications
// Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
