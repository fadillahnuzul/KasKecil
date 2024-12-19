<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Fascades\Session;
use App\Models\Kas;
use App\Models\Rekening;
use App\Models\Divisi;
use App\Models\Pengajuan;
use App\Models\Pengeluaran;
use App\Models\Sumber;
use App\Models\Saldo;
use Illuminate\Support\Facades\Auth;
use Alert;
use App\Models\Company;
use App\Models\Status;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class BankController extends Controller
{
    public $startDate;
    public $endDate;

    public function __construct() {
        $this->startDate = Carbon::now()->startOfYear();
        $this->endDate = Carbon::now()->endOfYear();
    }

    public function getSaldo($company = null)
    {
        $adminController = new AdminController;
        [$total_pengajuan, $total_pengeluaran, $diklaim] = $adminController->getGrandTotalTransaksi(null, null, $company);

        return [$total_pengajuan, $total_pengeluaran, $diklaim];
    }

    public function index(Request $request){
        $startDate = $request->startDate ? $request->startDate : $this->startDate;
        $endDate = $request->endDate ? $request->endDate : $this->endDate;
        $laporan = FALSE;
        $dataKas = Pengajuan::with('Sumber','User','Status')->where('status','!=',5)->searchByDateRange($startDate, $endDate)->get();
        $divisi = Divisi::get();
        $title = "Bank Kas Kecil";
        list($total_pengajuan, $total_pengeluaran, $diklaim) = $this->getSaldo($request->company);
        $pengajuan_admin = Pengajuan::with('Status')->where('divisi_id', 1)->where('status', 2)->orWhere('status', '4')->get();
        $admin = $pengajuan_admin->last();

        return view('bank/main', compact('dataKas','admin','divisi','title','laporan','startDate','endDate','total_pengajuan','total_pengeluaran','diklaim'));
    }

    public function laporan(Request $request){
        $startDate = $request->startDate ? $request->startDate : $this->startDate;
        $endDate = $request->endDate ? $request->endDate : $this->endDate;
        $laporan = TRUE;
        $dataKas = Pengajuan::with('Sumber','User','Status')->searchByDateRange($startDate, $endDate)->get();
        $divisi = Divisi::get();
        $title = "Daftar Pengajuan";
        list($total_pengajuan, $total_pengeluaran, $diklaim) = $this->getSaldo($request->company);
        $pengajuan_admin = Pengajuan::with('Status')->where('divisi_id', 1)->where('status', 2)->orWhere('status', '4')->get();
        $admin = $pengajuan_admin->last();

        return view('bank/main', compact('dataKas','admin','divisi','title','laporan','startDate','endDate','total_pengajuan','total_pengeluaran','diklaim'));
    }

    public function kas_divisi(Request $request, $id){
        $startDate = $request->startDate ? $request->startDate : $this->startDate;
        $endDate = $request->endDate ? $request->endDate : $this->endDate;
        $laporan = TRUE;
        $dataKas = Pengajuan::with('Sumber','User','Status')->where('divisi_id',$id)->searchByDateRange($startDate, $endDate)->get();
        $divisi = Divisi::get();
        $title = "Daftar Pengajuan";
        list($total_pengajuan, $total_pengeluaran, $diklaim) = $this->getSaldo($request->company);
        $pengajuan_admin = Pengajuan::with('Status')->where('divisi_id', 1)->where('status', 2)->orWhere('status', '4')->get();
        $admin = $pengajuan_admin->last();

        return view('bank/main', compact('dataKas','admin','divisi','title','laporan','startDate','endDate','total_pengajuan','total_pengeluaran','diklaim'));
    }

    public function acc(Request $request, $id)
    {
        $title = "Form Persetujuan";
        $edit = FALSE;
        $pengajuan = Pengajuan::with('sumber', 'Divisi')->findOrFail($id);

        return view('bank/form-edit', ['pengajuan' => $pengajuan], ['edit' => $edit,'title'=>$title]);
    }

    public function edit(Request $request, $id)
    {
        $title = "Form Edit";
        $edit = TRUE;
        $pengajuan = Pengajuan::with('sumber', 'Divisi')->findOrFail($id);

        return view('bank/form-edit', ['pengajuan' => $pengajuan], ['edit' => $edit, 'title'=>$title]);
    }

    public function setujui(Request $request, $id)
    {
        $pengajuan = Pengajuan::with('User')->findOrFail($id);
        $saldo_user = Saldo::with('User')->findOrFail($pengajuan->user_id);
        $jumlah = preg_replace("/[^0-9]/","",$request->jumlah);   
        
        //menyimpan data
        $pengajuan->tanggal = $request->tanggal;
        $pengajuan->deskripsi = $request->deskripsi;
        $pengajuan->tanggal = $request->tanggal;
        $pengajuan->status = "2";        
        $pengajuan->jumlah = $jumlah;
        $saldo_user->saldo = $saldo_user->saldo + $pengajuan->jumlah;

        $saldo_user->save();
        $pengajuan->save();

        return redirect('home_bank');
    }

    public function update(Request $request, $id)
    {
        $pengajuan = Pengajuan::with('Divisi')->findOrFail($id);
        $saldo = Saldo::findOrFail($pengajuan->user_id);

        $pengajuan->tanggal = $request->tanggal;
        $pengajuan->deskripsi = $request->deskripsi;
        $pengajuan->tanggal = $request->tanggal;
        $pengajuan->sumber = $request->sumber;
        $jumlah = preg_replace("/[^0-9]/","",$request->jumlah);     
        //update tabel saldo
        $saldo_awal = $saldo->saldo;
        $saldo->saldo = $saldo->saldo - $saldo_awal + $jumlah;
        //update tabel pengajuan
        $pengajuan->jumlah = $jumlah;

        $pengajuan->save();
        $saldo->save();

        return redirect('home_bank');
    }

    public function laporan_keluar(Request $request)
    {
        $startDate = $request->startDate ? $request->startDate : Carbon::now()->startOfMonth('d-m-Y');;
        $endDate = $request->endDate ? $request->endDate : Carbon::now()->endOfMonth('d-m-Y');;
        $selectedStatus = ($request->status) ? Status::find($request->status) : null;
        $selectedCompany = ($request->company) ? Company::find($request->company) : null;
        $selectedUser = ($request->user) ? User::find($request->user) : null;
        $company = Company::get();
        $status = Status::whereIn('id', [4, 6, 7, 8])->get();
        $userList = DB::table('user')->join('pettycash_pengajuan', 'user.id', '=', 'pettycash_pengajuan.user_id')->select('user.*')->get()->unique('id');
        $dataKas = Pengeluaran::with('pengajuan', 'Status')
            ->searchByDateRange($startDate, $endDate)
            ->searchByCompany($request->company)
            ->searchByStatus($request->status)
            ->searchByUser($request->user)
            ->get();
        $title = "Daftar Kas Keluar";
        list($total_pengajuan, $total_pengeluaran, $diklaim) = $this->getSaldo($request->company);

        return view ('/bank/laporan_kas', compact('title','startDate','endDate','dataKas','company','status','userList','selectedStatus','selectedCompany','selectedUser','total_pengeluaran','diklaim'));
    }

    public function tolak(Request $request, $id)
    {
        $pengajuan = Pengajuan::findOrFail($id);
        $pengajuan->sumber = NULL;
        $pengajuan->status = "3";

        $pengajuan->save();

        return redirect('home_bank');
    }

    public function hapus($id)
    {
        $delete = Pengajuan::with('Divisi','User')->findOrFail($id);
        $saldo = Saldo::findOrFail($delete->user_id);
        //JIKA STATUSNYA BELUM DIAPPROVE ATAU DECLINE
        if ($delete->status != 1 AND $delete->status != 3) {
            $saldo->saldo = $saldo->saldo - $delete->jumlah;
            }
        $delete->status = 6;
        $saldo->save();
        $delete->save();
        
        return back();
    }
}
