<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\KasController;
use App\Http\Controllers\RekeningController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::get('/', [KasController::class, 'index'])->middleware('auth');

Route::get('/login', [AuthController::class, 'login'])->name('login')->middleware('guest');
Route::post('/login', [AuthController::class, 'authentic'])->middleware('guest');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

Route::get('/home', [KasController::class, 'index'])->middleware('auth');
Route::get('/rekening', [RekeningController::class, 'index'])->middleware('auth');
Route::get('/pengajuan', [KasController::class, 'create_masuk'])->middleware('auth');
Route::get('/kas', [KasController::class, 'create_keluar'])->middleware('auth');
Route::get('/kas-edit/{id}', [KasController::class, 'edit'])->middleware('auth');
Route::put('/kas-update/{id}', [KasController::class, 'update'])->middleware('auth');
Route::get('/kas-delete/{id}', [KasController::class, 'delete'])->middleware('auth', 'admin');
Route::post('/simpan_kas', [KasController::class, 'save'])->middleware('auth');

Route::get('/home/admin', [AdminController::class, 'index'])->middleware('auth');
