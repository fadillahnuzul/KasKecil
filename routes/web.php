<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\KasController;
use App\Http\Controllers\RekeningController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\PengajuanController;
use App\Http\Controllers\PengeluaranController;
use App\Http\Controllers\SumberController;
use App\Http\Controllers\PembebananController;


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
Route::middleware(['auth'])->group(function () {
    Route::get('/', [PengajuanController::class, 'index']);
    Route::get('/home', [PengajuanController::class, 'index']);
    Route::get('/laporan', [PengajuanController::class, 'laporan']);
    Route::post('/simpan_pengajuan', [PengajuanController::class, 'save']);
    Route::get('/home', [PengajuanController::class, 'index']);
    Route::get('/pengajuan', [PengajuanController::class, 'create']);

    Route::get('/detail_pengajuan/{id}', [PengeluaranController::class, 'index']);
    Route::get('/hapus_kas_keluar/{id}', [PengeluaranController::class, 'delete']);
    Route::get('/edit_kas_keluar/{id}', [PengeluaranController::class, 'edit']);
    Route::put('/kas_update/{id}', [PengeluaranController::class, 'update']);
    Route::get('/kas', [PengeluaranController::class, 'create']);
    Route::post('/simpan_kas', [PengeluaranController::class, 'save']);
    Route::post('/kas_selesai', [PengeluaranController::class, 'done']);
    Route::get('/laporan_kas_keluar', [PengeluaranController::class, 'laporan']);
});
// Route::get('/', [KasController::class, 'index'])->middleware('auth');

Route::get('/login', [AuthController::class, 'login'])->name('login')->middleware('guest');
Route::post('/login', [AuthController::class, 'authentic'])->middleware('guest');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');


Route::get('/rekening', [RekeningController::class, 'index'])->middleware('auth');

// Route::get('/kas', [KasController::class, 'create_keluar'])->middleware('auth');
// Route::get('/kas-edit/{id}', [KasController::class, 'edit'])->middleware('auth');
// Route::put('/kas-update/{id}', [KasController::class, 'update'])->middleware('auth');
// Route::get('/kas-delete/{id}', [KasController::class, 'delete'])->middleware('auth', 'admin');
// Route::post('/simpan_kas', [KasController::class, 'save'])->middleware('auth');

//Halaman admin
Route::get('/home_admin', [AdminController::class, 'index'])->middleware('auth');
Route::get('/acc/{id}', [AdminController::class, 'acc'])->middleware('auth');
Route::put('/setujui/{id}', [AdminController::class, 'setujui'])->middleware('auth');
Route::get('/tolak/{id}', [AdminController::class, 'tolak'])->middleware('auth');
Route::get('/done/{id}', [AdminController::class, 'done'])->middleware('auth');
Route::get('/kas_divisi/{id}', [AdminController::class, 'kas_divisi'])->middleware('auth');
Route::get('/detail_divisi/{id}', [AdminController::class, 'detail_divisi'])->middleware('auth');
Route::get('/admin_laporan', [AdminController::class, 'laporan'])->middleware('auth');
Route::get('/admin_laporan_kas_keluar', [AdminController::class, 'laporan_keluar'])->middleware('auth');
Route::get('/admin_kas_kategori/{id}', [AdminController::class, 'kategori'])->middleware('auth');
Route::get('/edit_done/{id}', [AdminController::class, 'edit_done'])->middleware('auth');
Route::get('/edit_admin/{id}', [AdminController::class, 'edit'])->middleware('auth');
Route::put('/simpan_done/{id}', [AdminController::class, 'simpan_done'])->middleware('auth');
Route::put('/update/{id}', [AdminController::class, 'update'])->middleware('auth');
Route::get('/batal_done/{id}', [AdminController::class, 'batal_done'])->middleware('auth');
Route::get('/hapus_admin/{pengajuan}/{id}', [AdminController::class, 'hapus'])->middleware('auth');

//Filter tanggal
Route::post('/filter_pengajuan/{id}', [PengajuanController::class, 'filter']);
Route::post('/filter_pengeluaran', [PengeluaranController::class, 'filter']);


Route::post('/input_sumber', [SumberController::class, 'save']);
Route::post('/input_pembebanan', [PembebananController::class, 'save']);

//Download
Route::get('/pengajuan.export', [PengajuanController::class, 'export'])->name('pengajuan.export')->middleware('auth');
Route::get('/pengeluaran.export', [PengeluaranController::class, 'export'])->name('pengeluaran.export')->middleware('auth');
Route::post('/download.pdf', [PengajuanController::class, 'export_pdf'])->name('download.pdf')->middleware('auth');



