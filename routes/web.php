<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DokumenController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;
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
    return view('welcome');
});
Route::get('/login', [AuthController::class, 'index'])->name('login');
Route::post('/login-proses', [AuthController::class, 'login_proses'])->name('login-proses');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/home', [AdminController::class, 'dashboard'])->name('admindashboard');
Route::get('/user', [UserController::class, 'index'])->name('user');
Route::get('/create-user', [UserController::class, 'create'])->name('user-create');
Route::post('/create-proses', [UserController::class, 'store'])->name('user-store');
Route::get('/edit-user/{id}', [UserController::class, 'edit'])->name('user-edit');
Route::put('/update-user/{id}', [UserController::class, 'update'])->name('user-update');
Route::delete('/delete/{id}', [UserController::class, 'delete'])->name('user-delete');
Route::get('/upload-dokumen', [DokumenController::class, 'index'])->name('upload-dokumen');
Route::get('/validate-dokumen', [DokumenController::class, 'valid'])->name('validate-dokumen');

// Route::group(['prefix' => 'admin', 'middleware' => ['auth'], 'as' => 'admin.'], function () {
    
// });
