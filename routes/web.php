<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\PengajuanController;
use App\Http\Controllers\PengeluaranController;
use App\Http\Controllers\SumberController;
use App\Http\Controllers\PembebananController;
use App\Http\Controllers\BankController;
use App\Http\Controllers\BKKController;
use App\Http\Controllers\BKKHeaderController;


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
    Route::get('/home', [PengajuanController::class, 'index'])->name('home');
    Route::match(['GET', 'POST'], '/laporan', [PengajuanController::class, 'laporan']);
    Route::post('/simpan_pengajuan', [PengajuanController::class, 'save']);
    Route::get('/home', [PengajuanController::class, 'index']);
    Route::get('/pengajuan', [PengajuanController::class, 'create'])->name('pengajuan');
    Route::get('/welcome', [PengajuanController::class, 'welcome']);
    Route::post('/getCompany', [PengajuanController::class, 'getCompany']);
    Route::get('/project/{id}', [PengajuanController::class, 'project']);
    Route::get('/pengembalian_saldo_pengajuan/{id}', [PengajuanController::class, 'pengembalian_saldo']);

    Route::get('/detail_pengajuan/{id}', [PengeluaranController::class, 'index']);
    Route::match(['GET', 'POST'], '/kas_keluar', [PengeluaranController::class, 'index'])->name('kas_keluar');
    Route::get('/hapus_kas_keluar/{id}', [PengeluaranController::class, 'delete']);
    Route::get('/edit_kas_keluar/{id}', [PengeluaranController::class, 'edit']);
    Route::put('/kas_update/{id}', [PengeluaranController::class, 'update']);
    Route::get('/kas', [PengeluaranController::class, 'create']);
    Route::post('/simpan_kas', [PengeluaranController::class, 'save']);
    Route::post('/kas_selesai', [PengeluaranController::class, 'done']);
    Route::match(['GET', 'POST'],'/laporan_kas_keluar', [PengeluaranController::class, 'laporan']);
    Route::get('/pengembalian_saldo/{id}', [PengeluaranController::class, 'pengembalian_saldo']);
    // Route::post('/set_bkk_checkbox', [PengeluaranController::class, 'set_bkk']);
    //Dropdown Project
    Route::post('fetch_project', [PengeluaranController::class, 'fetchProject']);

//Halaman admin
    Route::match(['GET', 'POST'], '/home_admin', [AdminController::class, 'index'])->name('home_admin');
    Route::match(['GET', 'POST'], '/home_admin/{id}', [AdminController::class, 'index']);
    Route::match(['GET', 'POST'], '/index_filter_keluar/{filter}/{id}', [AdminController::class, 'index_filter_keluar'])->name('index_filter_keluar2'); //1 = filter user, 2 = filter company
    Route::match(['GET', 'POST'], '/index_filter_keluar', [AdminController::class, 'index_filter_keluar'])->name('index_filter_keluar');
    Route::get('/acc/{id}', [AdminController::class, 'acc']);
    Route::put('/setujui/{id}', [AdminController::class, 'setujui']);
    Route::get('/tolak/{id}', [AdminController::class, 'tolak']);
    Route::match(['GET', 'POST'],'/done', [AdminController::class, 'done']);
    Route::get('/kas_divisi/{laporan}/{id}', [AdminController::class, 'kas_divisi']);
    Route::get('/detail_divisi/{id}', [AdminController::class, 'detail_divisi']);
    Route::match(['GET', 'POST'], '/admin_laporan', [AdminController::class, 'laporan'])->name('admin_laporan');
    Route::match(['GET', 'POST'], '/admin_laporan_kas_keluar', [AdminController::class, 'laporan_keluar'])->name('admin_laporan_kas_keluar');
    Route::match(['GET', 'POST'], '/sendDataLaporan', [AdminController::class, 'sendDataLaporan']);
    Route::match(['GET', 'POST'], '/admin_kas_keluar', [AdminController::class, 'kas_keluar'])->name('admin_kas_keluar');
    Route::get('/edit_done/{id}', [AdminController::class, 'edit_done']);
    Route::get('/edit_admin/{id}', [AdminController::class, 'edit']);
    Route::put('/simpan_done/{id}', [AdminController::class, 'simpan_done']);
    Route::put('/update/{id}', [AdminController::class, 'update']);
    Route::get('/batal_done/{id}', [AdminController::class, 'batal_done']);
    Route::get('/hapus_admin/{pengajuan}/{id}', [AdminController::class, 'hapus']);
    Route::get('/klaim', [AdminController::class, 'klaim']);
    Route::get('/kas_company/{id}', [AdminController::class, 'kas_company']);
    Route::get('/done_pengajuan/{id}', [AdminController::class, 'done_pengajuan']);
    // Route::get('/set_bkk/{id}', [AdminController::class, 'set_bkk']);
    Route::post('/set_bkk_checkbox', [AdminController::class, 'set_bkk']);
    Route::post('/set_uang_kembali', [AdminController::class, 'set_tanggal_kembali']);
    Route::get('/konfirm_kembali/{id}', [AdminController::class, 'konfirm_kembali']);

    Route::post('/input_sumber', [SumberController::class, 'save']);
    Route::post('/input_pembebanan', [PembebananController::class, 'save']);

    //Download
    Route::match(['GET', 'POST'],'/pengeluaran.export', [PengeluaranController::class, 'export'])->name('pengeluaran.export');
    Route::get('/export_pengajuan/{id}', [PengajuanController::class, 'export_pdf']);


    //Halaman Bank
    Route::match(['GET', 'POST'],'/home_bank', [BankController::class, 'index'])->name('home_bank');
    Route::match(['GET', 'POST'],'/bank_laporan', [BankController::class, 'laporan'])->name('bank_laporan');
    Route::match(['GET', 'POST'],'/bank_laporan_kas_keluar', [BankController::class, 'laporan_keluar'])->name('bank_laporan_kas_keluar');
    Route::get('/hapus_bank/{id}', [BankController::class, 'hapus']);
    Route::get('/edit_bank/{id}', [BankController::class, 'edit']);
    Route::get('/acc_bank/{id}', [BankController::class, 'acc']);
    Route::put('/setujui_bank/{id}', [BankController::class, 'setujui']);
    Route::get('/tolak_bank/{id}', [BankController::class, 'tolak']);
    Route::put('/update_bank/{id}', [BankController::class, 'update']);
    Route::get('/bank_kas_divisi/{id}', [BankController::class, 'kas_divisi']);

    //BKK
    Route::get('/print_bkk/{id}', [BKKController::class, 'print']);
    Route::match(['GET', 'POST'], '/list_bkk', [BKKHeaderController::class, 'index'])->name('list_bkk');
    Route::get('/create_bkk', [BKKController::class, 'create'])->name('create_bkk');
    Route::post('/save_bkk', [BKKController::class, 'save']);
    Route::get('/bkk_detail/{id}',[BKKController::class, 'index']);
});

Route::get('/login', [AuthController::class, 'login'])->name('login')->middleware('guest');
Route::post('/login', [AuthController::class, 'authentic'])->middleware('guest');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');






