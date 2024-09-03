<?php

use App\Http\Controllers\AuditController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\RuleController;
use App\Http\Controllers\DocumentRuleController;
use App\Http\Controllers\MasterDataAuditController;
use App\Http\Controllers\MasterDataRuleController;
use App\Http\Controllers\ValidateRuleController;
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


// Dashboard rule
Route::get('/rule', [HomeController::class, 'dashboard_rule'])
    ->middleware(['auth', 'role:admin|guest'])
    ->name('dashboard.rule');
Route::get('/rule/notification', [HomeController::class, 'getNotifications'])
    ->middleware(['auth', 'role:admin|guest'])
    ->name('notifications');
Route::get('/rule/download-excel', [HomeController::class, 'downloadExcel'])
    ->middleware(['auth', 'role:admin|guest'])
    ->name('download.excel');
Route::get('/rule/filter-documents', [HomeController::class, 'filterDocuments'])
    ->middleware(['auth', 'role:admin|guest'])
    ->name('filter.documents');

//master data rule
Route::get('/rule/master-data/departemen', [MasterDataRuleController::class, 'index_departemen'])
    ->middleware(['auth', 'role:admin'])
    ->name('masterdata.departemen');
Route::get('/rule/master-data/process-code', [MasterDataRuleController::class, 'index_prosescode'])
    ->middleware(['auth', 'role:admin'])
    ->name('masterdata.kodeproses');
Route::get('/rule/master-data/role', [MasterDataRuleController::class, 'index_role'])
    ->middleware(['auth', 'role:admin'])
    ->name('masterdata.role');
Route::post('/rule/master-data/departemen/add', [MasterDataRuleController::class, 'store_departemen'])
    ->middleware(['auth', 'role:admin'])
    ->name('add.departemen');
Route::post('/rule/master-data/process-code/add', [MasterDataRuleController::class, 'store_kodeproses'])
    ->middleware(['auth', 'role:admin'])
    ->name('add.kodeproses');
Route::post('/rule/master-data/role/add', [MasterDataRuleController::class, 'store_role'])
    ->middleware(['auth', 'role:admin'])
    ->name('add.role');
Route::post('/rule/master-data/departemen/update/{id}', [MasterDataRuleController::class, 'update_departemen'])
    ->middleware(['auth', 'role:admin'])
    ->name('update.departemen');
Route::post('/rule/master-data/process-code/update/{id}', [MasterDataRuleController::class, 'update_kodeproses'])
    ->middleware(['auth', 'role:admin'])
    ->name('update.kodeproses');
Route::delete('/rule/master-data/departemen/delete/{id}', [MasterDataRuleController::class, 'delete_departemen'])
    ->middleware(['auth', 'role:admin'])
    ->name('delete.departemen');
Route::delete('/rule/master-data/process-code/delete/{id}', [MasterDataRuleController::class, 'delete_kodeproses'])
    ->middleware(['auth', 'role:admin'])
    ->name('delete.kodeproses');
Route::delete('/rule/master-data/role/delete/{id}', [MasterDataRuleController::class, 'delete_role'])
    ->middleware(['auth', 'role:admin'])
    ->name('delete.role');

// Template Dokumen
Route::get('/rule/template-documents', [DocumentRuleController::class, 'index'])
    ->middleware(['auth', 'role:admin|guest'])
    ->name('template.index');
Route::post('/rule/template-documents/add', [DocumentRuleController::class, 'store'])
    ->middleware(['auth', 'role:admin'])
    ->name('template.add');
Route::post('/rule/template-documents/edit/{id}', [DocumentRuleController::class, 'edit'])
    ->middleware(['auth', 'role:admin'])
    ->name('template.edit');
Route::get('/rule/template-documents/preview/{id}', [DocumentRuleController::class, 'preview'])
    ->middleware(['auth', 'role:admin|guest'])
    ->name('template.preview');
Route::get('/rule/template-documents/download/{id}', [DocumentRuleController::class, 'download'])
    ->middleware(['auth', 'role:admin|guest'])
    ->name('template.download');
Route::delete('/rule/template-documents/delete/{id}', [DocumentRuleController::class, 'destroy'])
    ->middleware(['auth', 'role:admin'])
    ->name('template.delete');


// Document Draft Rule
Route::get('/rule/documents/{jenis}/{tipe}', [RuleController::class, 'index'])
    ->middleware(['auth', 'role:admin|guest'])
    ->name('rule.index');
Route::post('/rule/documents/draftRule', [RuleController::class, 'store'])
    ->middleware(['auth', 'role:guest'])
    ->name('tambah.rule');
Route::get('/rule/download/rule/{id}', [RuleController::class, 'download'])
    ->middleware(['auth', 'role:admin|guest'])
    ->name('download.rule');

// Validate Rule
Route::get('/rule/validate-draft/{jenis}/{tipe}', [ValidateRuleController::class, 'validate_index'])
    ->middleware(['auth', 'role:admin'])
    ->name('rule.validate');
Route::post('/rule/validate/approve/{id}', [ValidateRuleController::class, 'approveDocument'])
    ->middleware(['auth', 'role:admin'])
    ->name('dokumen.approve');
Route::post('/rule/documents/validate/{id}/activate', [ValidateRuleController::class, 'activateDocument'])
    ->middleware(['auth', 'role:admin'])
    ->name('activate.document');
Route::post('/rule/documents/validate/{id}/obsolete', [ValidateRuleController::class, 'obsoleteDocument'])
    ->middleware(['auth', 'role:admin'])
    ->name('obsolete.document');

// Document Final Rule
Route::get('/rule/documents/final/{jenis}/{tipe}', [RuleController::class, 'final_doc'])
    ->middleware(['auth', 'role:admin|guest'])
    ->name('documents.final');
Route::post('/rule/documents/final/upload/{id}', [ValidateRuleController::class, 'uploadFinal'])
    ->middleware(['auth', 'role:guest'])
    ->name('upload.final');
Route::get('/rule/documents/final/preview-final/{id}', [ValidateRuleController::class, 'previewFinal'])
    ->middleware(['auth', 'role:admin|guest'])
    ->name('documents.previewFinal');
Route::get('/rule/documents/final/preview-active/{id}', [ValidateRuleController::class, 'previewActive'])
    ->middleware(['auth', 'role:admin|guest'])
    ->name('documents.previewActive');
Route::get('/rule/documents/final/preview-obsolete/{id}', [ValidateRuleController::class, 'previewObsolete'])
    ->middleware(['auth', 'role:admin|guest'])
    ->name('documents.previewObsolete');
Route::post('/rule/documents/final/upload-oldDocument', [ValidateRuleController::class, 'upload_old_doc'])
    ->middleware(['auth', 'role:admin'])
    ->name('add.oldDoc');

// Document Share
Route::get('/rule/documents/share/{jenis}/{tipe}', [RuleController::class, 'share_document'])
    ->middleware(['auth', 'role:admin|guest'])
    ->name('document.share');
Route::get('/documents/preview/{id}', [RuleController::class, 'preview'])->name('documents.preview');


// Audit Control Master
Route::get('/audit/master-data/audit', [MasterDataAuditController::class, 'master_audit'])
    ->middleware(['auth', 'role:admin'])
    ->name('masterdata.audit');
Route::post('/audit/master-data/audit/add', [MasterDataAuditController::class, 'store_audit'])
    ->middleware(['auth', 'role:admin'])
    ->name('add.audit');
Route::post('/audit/master-data/audit/update/{id}', [MasterDataAuditController::class, 'update_audit'])
    ->middleware(['auth', 'role:admin'])
    ->name('update.audit');
Route::delete('/audit/master-data/audit/delete/{id}', [MasterDataAuditController::class, 'delete_audit'])
    ->middleware(['auth', 'role:admin'])
    ->name('delete.audit');
Route::get('/audit/master-data/itemaudit', [MasterDataAuditController::class, 'master_itemAudit'])
    ->middleware(['auth', 'role:admin'])
    ->name('masterdata.itemAudit');
Route::post('/audit/master-data/itemaudit/add', [MasterDataAuditController::class, 'store_itemAudit'])
    ->middleware(['auth', 'role:admin'])
    ->name('add.itemAudit');
Route::post('/audit/master-data/itemaudit/update/{id}', [MasterDataAuditController::class, 'update_itemaudit'])
    ->middleware(['auth', 'role:admin'])
    ->name('update.itemAudit');
Route::delete('/audit/master-data/itemaudit/delete/{id}', [MasterDataAuditController::class, 'delete_itemAudit'])
    ->middleware(['auth', 'role:admin'])
    ->name('delete.itemAudit');
Route::get('/audit/master-data/auditcontrol', [MasterDataAuditController::class, 'master_auditcontrol'])
    ->middleware(['auth', 'role:admin'])
    ->name('masterdata.auditControl');
Route::post('/audit/master-data/auditcontrol/add', [MasterDataAuditController::class, 'store_auditControl'])
    ->middleware(['auth', 'role:admin'])
    ->name('add.auditControl');


//Audit Control 
Route::get('/audit', [HomeController::class, 'dashboard_audit'])
    ->middleware(['auth', 'role:admin|guest'])
    ->name('dashboard.audit');
Route::get('/audit/auditcontrol', [AuditController::class, 'index_auditControl'])
    ->middleware(['auth', 'role:guest|admin'])
    ->name('index.auditControl');
