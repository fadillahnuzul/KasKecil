<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RekeningController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\PengajuanController;
use App\Http\Controllers\PengeluaranController;
use App\Http\Controllers\SumberController;
use App\Http\Controllers\PembebananController;
use App\Http\Controllers\BankController;


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
    Route::get('/welcome', [PengajuanController::class, 'welcome']);
    Route::post('/getCompany', [PengajuanController::class, 'getCompany']);
    Route::get('/project/{id}', [PengajuanController::class, 'project']);

    Route::get('/detail_pengajuan/{id}', [PengeluaranController::class, 'index']);
    Route::get('/hapus_kas_keluar/{id}', [PengeluaranController::class, 'delete']);
    Route::get('/edit_kas_keluar/{id}', [PengeluaranController::class, 'edit']);
    Route::put('/kas_update/{id}', [PengeluaranController::class, 'update']);
    Route::get('/kas', [PengeluaranController::class, 'create']);
    Route::post('/simpan_kas', [PengeluaranController::class, 'save']);
    Route::post('/kas_selesai', [PengeluaranController::class, 'done']);
    Route::get('/laporan_kas_keluar', [PengeluaranController::class, 'laporan']);
    Route::get('/kas_company/{id}/{id_comp}', [PengeluaranController::class, 'kas_company']);

//Halaman admin
    Route::get('/home_admin', [AdminController::class, 'index']);
    Route::get('/acc/{id}', [AdminController::class, 'acc']);
    Route::put('/setujui/{id}', [AdminController::class, 'setujui']);
    Route::get('/tolak/{id}', [AdminController::class, 'tolak']);
    Route::get('/done/{id}', [AdminController::class, 'done']);
    Route::get('/kas_divisi/{id}', [AdminController::class, 'kas_divisi']);
    Route::get('/detail_divisi/{id}', [AdminController::class, 'detail_divisi']);
    Route::get('/admin_laporan', [AdminController::class, 'laporan']);
    Route::get('/admin_laporan_kas_keluar', [AdminController::class, 'laporan_keluar']);
    Route::get('/admin_kas_kategori/{id}', [AdminController::class, 'kategori']);
    Route::get('/edit_done/{id}', [AdminController::class, 'edit_done']);
    Route::get('/edit_admin/{id}', [AdminController::class, 'edit']);
    Route::put('/simpan_done/{id}', [AdminController::class, 'simpan_done']);
    Route::put('/update/{id}', [AdminController::class, 'update']);
    Route::get('/batal_done/{id}', [AdminController::class, 'batal_done']);
    Route::get('/hapus_admin/{pengajuan}/{id}', [AdminController::class, 'hapus']);
    Route::get('/klaim', [AdminController::class, 'klaim']);
    Route::get('/kas_company/{id}', [AdminController::class, 'kas_company']);

    //Filter tanggal
    Route::post('/filter_pengajuan/{id}', [PengajuanController::class, 'filter']);
    Route::post('/filter_pengeluaran', [PengeluaranController::class, 'filter']);


    Route::post('/input_sumber', [SumberController::class, 'save']);
    Route::post('/input_pembebanan', [PembebananController::class, 'save']);

    //Download
    Route::get('/pengajuan.export', [PengajuanController::class, 'export'])->name('pengajuan.export');
    Route::get('/pengeluaran.export', [PengeluaranController::class, 'export'])->name('pengeluaran.export');
    Route::post('/download.pdf', [PengajuanController::class, 'export_pdf'])->name('download.pdf');

    //Halaman Bank
    Route::get('/home_bank', [BankController::class, 'index']);
    Route::get('/bank_laporan', [BankController::class, 'laporan']);
    Route::get('/bank_laporan_kas_keluar', [BankController::class, 'laporan_keluar']);
    Route::get('/hapus_bank/{id}', [BankController::class, 'hapus']);
    Route::get('/edit_bank/{id}', [BankController::class, 'edit']);
    Route::get('/acc_bank/{id}', [BankController::class, 'acc']);
    Route::put('/setujui_bank/{id}', [BankController::class, 'setujui']);
    Route::get('/tolak_bank/{id}', [BankController::class, 'tolak']);
    Route::put('/update_bank/{id}', [BankController::class, 'update']);

    Route::get('/test', [PengeluaranController::class, 'coba_export']);
});

Route::get('/login', [AuthController::class, 'login'])->name('login')->middleware('guest');
Route::post('/login', [AuthController::class, 'authentic'])->middleware('guest');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

Route::get('/rekening', [RekeningController::class, 'index'])->middleware('auth');






