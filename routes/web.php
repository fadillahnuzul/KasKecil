<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\KasController;
use App\Http\Controllers\RekeningController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\PengajuanController;
use App\Http\Controllers\PengeluaranController;


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


Route::get('/rekening', [RekeningController::class, 'index'])->middleware('auth');

// Route::get('/kas', [KasController::class, 'create_keluar'])->middleware('auth');
// Route::get('/kas-edit/{id}', [KasController::class, 'edit'])->middleware('auth');
// Route::put('/kas-update/{id}', [KasController::class, 'update'])->middleware('auth');
// Route::get('/kas-delete/{id}', [KasController::class, 'delete'])->middleware('auth', 'admin');
// Route::post('/simpan_kas', [KasController::class, 'save'])->middleware('auth');

Route::get('/home/admin', [AdminController::class, 'index'])->middleware('auth');
Route::get('/acc/{id}', [AdminController::class, 'acc'])->middleware('auth');
Route::put('/setujui/{id}', [AdminController::class, 'setujui'])->middleware('auth');
Route::get('/tolak/{id}', [AdminController::class, 'tolak'])->middleware('auth');
Route::get('/done/{id}', [AdminController::class, 'done'])->middleware('auth');
Route::get('/kas_divisi/{id}', [AdminController::class, 'kas_divisi'])->middleware('auth');

Route::get('/home', [PengajuanController::class, 'index'])->middleware('auth');
Route::post('/simpan_pengajuan', [PengajuanController::class, 'save'])->middleware('auth');
Route::get('/home', [PengajuanController::class, 'index'])->middleware('auth');
Route::get('/pengajuan', [PengajuanController::class, 'create'])->middleware('auth');

Route::get('/detail_pengajuan/{id}', [PengeluaranController::class, 'index'])->middleware('auth');
Route::get('/hapus_kas_keluar/{id}', [PengeluaranController::class, 'delete'])->middleware('auth');
Route::get('/edit_kas_keluar/{id}', [PengeluaranController::class, 'edit'])->middleware('auth');
Route::put('/kas_update/{id}', [PengeluaranController::class, 'update'])->middleware('auth');
Route::get('/kas', [PengeluaranController::class, 'create'])->middleware('auth');
Route::post('/simpan_kas', [PengeluaranController::class, 'save'])->middleware('auth');
Route::post('/kas_selesai', [PengeluaranController::class, 'done'])->middleware('auth');

