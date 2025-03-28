<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\PengeluaranController;
use Illuminate\Support\Fascades\Session;
use App\Models\Kas;
use App\Models\Rekening;
use App\Models\Divisi;
use App\Models\Pengajuan;
use App\Models\Pengeluaran;
use App\Models\Sumber;
use App\Models\Saldo;
use App\Models\Status;
use App\Models\Company;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Alert;
use App\Services\HitungSaldoService;
use App\Services\HitungTransaksiService;
use App\Services\HitungPengajuanService;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public $startDate;
    public $endDate;
    public $company;
    public $companySelected;
    public $userList;

    public function __construct()
    {
        $this->startDate = Carbon::now()->startOfYear('d-m-Y');
        $this->endDate = Carbon::now()->endOfYear('d-m-Y');
        $this->company = Company::notPribadi()->get();
        $this->userList = DB::table('user')->join('pettycash_pengajuan', 'user.id', '=', 'pettycash_pengajuan.user_id')->select('user.*')->get()->unique('id');
    }

    public function index(Request $request, $id = null)
    {
        // $saldoAwal = $request->session()->get('saldo_awal');
        $companySelected = $this->companySelected;
        $startDate = ($request->startDate) ? $request->startDate  : $this->startDate;
        $endDate = ($request->endDate) ? $request->endDate : $this->endDate;
        $company = $request->id ? $request->id : null;
        $filter_keluar = FALSE;
        $laporan = FALSE;

        $dataKas = Pengajuan::with('Sumber', 'User', 'Status')->isNotDone()->searchByUser($id)->get();
        $Saldo = (new HitungSaldoService)->hitung_saldo_all_user($company);
        $divisi = Divisi::get();
        $title = "Admin Kas Kecil";
        $userList = DB::table('user')->join('pettycash_pengajuan', 'user.id', '=', 'pettycash_pengajuan.user_id')->select('user.*')->get()->unique('id');
        $companyList = DB::table('project_company')->join('pettycash_pengeluaran', 'project_company.project_company_id', '=', 'pettycash_pengeluaran.pembebanan')->select('project_company.*')->get()->unique('project_company_id');

        // Perhitungan sisa dan total belanja pada card
        $pengajuanServices = new HitungPengajuanService;
        // $transaksiServices = new HitungTransaksiService;
        $total_pengajuan = $pengajuanServices->hitung_pengajuan();
        $total_pengajuan_admin = $pengajuanServices->hitung_pengajuan_admin();
        $total_pengajuan_user = $pengajuanServices->hitung_pengajuan_all_user();
        // if ($id && $startDate && $endDate && $startDate != $this->startDate && $endDate != $this->endDate) {
        // } else if ($id) {
        //     $total_pengajuan = $pengajuanServices->hitung_pengajuan($id);
        //     $total_pengeluaran = $transaksiServices->hitung_belum_klaim($id);
        //     $total_diklaim = $transaksiServices->hitung_klaim($id);
        // } else if ($startDate && $endDate && $startDate != $this->startDate && $endDate != $this->endDate) {
        //     $total_pengajuan = $pengajuanServices->hitung_pengajuan(null, $startDate, $endDate);
        //     $total_pengeluaran = $transaksiServices->hitung_belum_klaim(null, $startDate, $endDate);
        //     $total_diklaim = $transaksiServices->hitung_klaim(null, $startDate, $endDate);
        // } else {
        //     $total_pengajuan = $pengajuanServices->hitung_pengajuan();
        //     $total_pengeluaran = $transaksiServices->hitung_belum_klaim();
        //     $total_diklaim = $transaksiServices->hitung_klaim();
        // }

        $pengajuan_admin = Pengajuan::with('Status')->where('divisi_id', 1)->where('status', 2)->orWhere('status', '4')->get();
        $admin = $pengajuan_admin->last();
        // session(['total_pengajuan' => $total_pengajuan]);
        // session(['total_pengeluaran' => $total_pengeluaran]);
        // session(['total_diklaim' => $total_diklaim]);
        return view('admin/main', compact('dataKas', 'admin', 'Saldo', 'divisi', 'title', 'laporan', 'startDate', 'endDate', 'total_pengajuan', 'total_pengajuan_admin', 'total_pengajuan_user', 'filter_keluar', 'userList', 'companyList', 'companySelected'));
    }

    public function index_filter_keluar(Request $request, $filter = null, $id = null)
    {
        // filter : (1 = user, 2 = company)
        $startDate = ($request->startDate) ? $request->startDate  : $this->startDate;
        $endDate = ($request->endDate) ? $request->endDate : $this->endDate;
        $company = ($request->id) ? $request->id : null;
        $companySelected = $this->companySelected;
        $Saldo = (new HitungSaldoService)->hitung_saldo_all_user($company);
        $divisi = Divisi::get();
        $filter_keluar = TRUE;
        $title = "Admin Kas Kecil";
        $laporan = FALSE;
        $userList = DB::table('user')->join('pettycash_pengajuan', 'user.id', '=', 'pettycash_pengajuan.user_id')->select('user.*')->get()->unique('id');
        $companyList = DB::table('project_company')->join('pettycash_pengeluaran', 'project_company.project_company_id', '=', 'pettycash_pengeluaran.pembebanan')->select('project_company.*')->get()->unique('project_company_id');
        $user = ($id) ? User::find($id) : null;
        $dataKas = Pengajuan::with('Sumber', 'User', 'Status')->where(function($query) use ($filter){
            ($filter==1) ? $query->isNotDone() : $query->isDone();
        })->searchByUser($id)->searchByDateRange($startDate, $endDate)->get();
        // $dataKas = Pengeluaran::with('pengajuan', 'Status', 'COA', 'Pembebanan')
        //     ->where(function ($query) use ($filter, $id, $user) {
        //         ($filter == 1) ? $query->searchByUser($id) : $query->searchByCompany($id);
        //     })
        //     ->SearchByDateRange($startDate, $endDate)->get();

        $pengajuanServices = new HitungPengajuanService;
        $transaksiServices = new HitungTransaksiService;
        // if ($filter == 1) {
        //     $total_pengajuan = $pengajuanServices->hitung_pengajuan($id);
        //     $total_pengeluaran = $transaksiServices->hitung_belum_klaim($id);
        //     $total_diklaim = $transaksiServices->hitung_klaim($id);
        // } else if ($filter == 2) {
        //     $total_pengajuan = $pengajuanServices->hitung_pengajuan();
        //     $total_pengeluaran = $transaksiServices->hitung_belum_klaim(null, null, null, $id);
        //     $total_diklaim = $transaksiServices->hitung_klaim(null, null, null, $id);
        // } else {
        //     $total_pengajuan = $pengajuanServices->hitung_pengajuan();
        //     $total_pengeluaran = $transaksiServices->hitung_belum_klaim();
        //     $total_diklaim = $transaksiServices->hitung_klaim();
        // }
        $total_pengajuan = $pengajuanServices->hitung_pengajuan();
        $total_pengajuan_admin = $pengajuanServices->hitung_pengajuan_admin();
        $total_pengajuan_user = $pengajuanServices->hitung_pengajuan_all_user();
        if($id) {
            $total_pengajuan_user = $pengajuanServices->hitung_pengajuan($id);
        }

        return view('admin/main', compact('dataKas', 'Saldo', 'divisi', 'title', 'laporan', 'startDate', 'endDate', 'total_pengajuan', 'total_pengajuan_admin', 'total_pengajuan_user', 'filter_keluar', 'userList', 'companyList', 'companySelected'));
    }

    public function laporan(Request $request)
    {
        $startDate = $request->startDate ? $request->startDate : $this->startDate;
        $endDate = $request->endDate ? $request->endDate : $this->endDate;
        $laporan = TRUE;
        $filter_keluar = FALSE;
        $dataKas = Pengajuan::with('Sumber', 'User', 'Status')->isDone()->searchByDateRange($startDate, $endDate)->get();
        $divisi = Divisi::get();
        $userList = DB::table('user')->join('pettycash_pengajuan', 'user.id', '=', 'pettycash_pengajuan.user_id')->select('user.*')->get()->unique('id');
        $companyList = DB::table('project_company')->join('pettycash_pengeluaran', 'project_company.project_company_id', '=', 'pettycash_pengeluaran.pembebanan')->select('project_company.*')->get()->unique('project_company_id');
        $companySelected = $this->companySelected;

        // Perhitungan sisa dan total belanja pada card
        $transaksiServices = new HitungTransaksiService;
        $pengajuanServices = new HitungPengajuanService;
        $total_pengajuan = $pengajuanServices->hitung_pengajuan(null, $startDate, $endDate);
        $total_pengajuan_admin = $pengajuanServices->hitung_pengajuan_admin();
        $total_pengajuan_user = $pengajuanServices->hitung_pengajuan_all_user();
        $title = "Laporan Pengajuan";
        // $pengajuan_admin = Pengajuan::with('Status')->where('divisi_id', 1)->where('status', 2)->orWhere('status', 4)->get();
        // $admin = $pengajuan_admin->last();

        return view('admin/main', compact('dataKas', 'divisi', 'title', 'laporan', 'total_pengajuan', 'total_pengajuan_admin', 'total_pengajuan_user', 'filter_keluar', 'startDate', 'endDate', 'companyList', 'userList', 'companySelected'));
    }

    public function getTotalPerUser(Collection $userList, int $company=null, string $startDate=null, string $endDate=null) : Collection
    {
        $saldoServices = new HitungSaldoService;
        $transaksiServices = new HitungTransaksiService;
        $pengajuanServices = new HitungPengajuanService;
        $userList->map(function ($item) use ($company, $startDate, $endDate, $saldoServices, $transaksiServices, $pengajuanServices) {
            $item->total_pengajuan = $pengajuanServices->hitung_pengajuan($item->id, null, null, null, $company);
            $item->total_pengeluaran = $transaksiServices->hitung_belum_klaim($item->id, $startDate, $endDate, $company);
            $item->total_diklaim = $transaksiServices->hitung_klaim($item->id, $startDate, $endDate, $company);
            $item->sisa_saldo = $item->total_pengajuan - ($item->total_pengeluaran + $item->total_diklaim);
        });

        return $userList;
    }

    public function getGrandTotalTransaksi(string $startDate=null, string $endDate=null, int $company=null) : array {
        $saldoServices = new HitungSaldoService;
        $transaksiServices = new HitungTransaksiService;
        $pengajuanServices = new HitungPengajuanService;
        $Saldo = $pengajuanServices->hitung_pengajuan_admin($company) + $pengajuanServices->hitung_pengajuan_all_user($company);
        $totalKeluar = $transaksiServices->hitung_belum_klaim(null, $startDate, $endDate, $company);
        $totalKlaim = $transaksiServices->hitung_klaim(null, $startDate, $endDate, $company);
        $sisa = $Saldo - ($totalKeluar + $totalKlaim);

        return [$Saldo, $totalKeluar, $totalKlaim, $sisa];
    }

    public function laporan_keluar(Request $request)
    {
        $title = "Laporan Kas Kecil";
        $company = $this->company;
        $laporan = TRUE;
        $startDate = $request->startDate ? $request->startDate : $this->startDate;
        $endDate = $request->endDate ? $request->endDate : $this->endDate;
        $selectedStatus = ($request->status) ?? null;
        $selectedCompany = ($request->company) ? Company::with('project')->find($request->company) : null;
        $selectedUser = ($request->user) ?? null;
        $status = Status::whereIn('id', [4, 6, 7, 8, 10])->get();
        $userList = $this->userList;
        $userList = $this->getTotalPerUser($userList,$request->company, null, null);
        [$Saldo, $totalKeluar, $totalKlaim, $sisa] = $this->getGrandTotalTransaksi(null, null, $request->company);
        // $dataKas = DB::table('pettycash_pengeluaran')->select('coa',DB::raw('sum(jumlah) as total'))->groupBy('coa')->get();
        $dataKas = Pengeluaran::with('Project','pengajuan', 'Status', 'COA', 'Pembebanan', 'unit', 'User')->bukanPengembalianSaldo()->orderBy('status', 'asc')->notPribadi()
            ->searchByDateRange($startDate, $endDate)
            ->searchByCompany($request->company)
            ->searchByStatus($request->status)
            ->searchByUser($request->user)
            ->searchByProject($request->project)
            ->get();
        (new PengeluaranController)->set_tanggal($startDate, $endDate);
        return view('/admin/kas', compact('title', 'startDate', 'endDate', 'company', 'userList', 'dataKas', 'Saldo', 'totalKeluar', 'totalKlaim', 'sisa', 'laporan', 'status', 'selectedStatus', 'selectedCompany', 'selectedUser'));
    }

    public function sendDataKas($startDate = null, $endDate = null)
    {
        $dataKas = Pengeluaran::with('User', 'Status', 'Coa', 'Pembebanan')->statusKlaimAndSetBKK()
            ->searchByDateRange($startDate, $endDate)
            ->bukanPengembalianSaldo()->orderBy('status', 'asc')->get();
        return response()->json(['data' => $dataKas]);
    }

    public function kas_keluar(Request $request)
    {
        $title = "Pengeluaran Kas Kecil";
        $company = $this->company;
        $laporan = FALSE;
        $startDate = $request->startDate ? $request->startDate : $this->startDate;
        $endDate = $request->endDate ? $request->endDate : $this->endDate;
        $selectedStatus = ($request->status) ?? 4;
        $selectedCompany = ($request->company) ? Company::find($request->company) : null;
        $selectedUser = ($request->user) ?? null;
        $status = Status::whereIn('id', [4, 6, 7, 8, 10])->get();
        $userList = $this->userList;
        $userList = $this->getTotalPerUser($userList,$request->company, null, null);
        [$Saldo, $totalKeluar, $totalKlaim, $sisa] = $this->getGrandTotalTransaksi(null, null, $request->company);
        $dataKas = Pengeluaran::with('Project','pengajuan', 'Status', 'COA', 'Pembebanan', 'unit', 'User')->bukanPengembalianSaldo()->notPribadi()
            ->searchByDateRange($startDate, $endDate)->orderBy('status', 'asc')
            ->where(function ($query) use ($selectedStatus) {
                ($selectedStatus) ? $query->searchByStatus($selectedStatus) : $query->statusProgress();
            })
            ->searchByCompany($request->company)
            ->searchByUser($request->user)
            ->searchByProject($request->project)
            ->get();
        (new PengeluaranController)->set_tanggal($startDate, $endDate);
        return view('/admin/kas', compact('title', 'startDate', 'endDate', 'company', 'userList', 'dataKas', 'Saldo', 'totalKeluar', 'totalKlaim', 'sisa', 'laporan', 'status', 'selectedStatus', 'selectedCompany', 'selectedUser'));
    }


    public function acc(Request $request, $id)
    {
        $edit = FALSE;
        $sumber = Sumber::select('id', 'sumber_dana')->get();
        $pengajuan = Pengajuan::with('sumber', 'Divisi')->findOrFail($id);

        return view('admin/form-edit', ['pengajuan' => $pengajuan], ['sumber' => $sumber, 'edit' => $edit]);
    }

    public function edit(Request $request, $id)
    {
        $edit = TRUE;
        $sumber = Sumber::select('id', 'sumber_dana')->get();
        $pengajuan = Pengajuan::with('sumber', 'Divisi')->findOrFail($id);

        return view('admin/form-edit', ['pengajuan' => $pengajuan], ['sumber' => $sumber, 'edit' => $edit]);
    }

    public function update(Request $request, $id)
    {
        $pengajuan = Pengajuan::with('Divisi')->findOrFail($id);
        $saldo_admin = (new HitungSaldoService)->hitung_saldo_user(Auth::user()->id);

        $pengajuan->tanggal = $request->tanggal;
        $pengajuan->deskripsi = $request->deskripsi;
        $pengajuan->tanggal = $request->tanggal;
        $pengajuan->sumber = $request->sumber;
        $pengajuan->jumlah = preg_replace("/[^0-9]/", "", $request->jumlah);
        //pengajuan admin
        if ($pengajuan->User->kk_access == '2') {
            if ($pengajuan->jumlah > $saldo_admin) {
                Alert::error('Approve gagal', 'Maaf, saldo admin tidak cukup');
                return back();
            }
        }
        $pengajuan->save();

        return redirect('home_admin');
    }

    public function setujui(Request $request, $id)
    {
        $pengajuan = Pengajuan::with('User')->findOrFail($id);
        $saldo_admin = (new HitungSaldoService)->hitung_saldo_user(Auth::user()->id);

        $pengajuan->tanggal = $request->tanggal;
        $pengajuan->deskripsi = $request->deskripsi;
        $pengajuan->tanggal = $request->tanggal;
        $pengajuan->sumber = $request->sumber;
        $pengajuan->status = "2";
        $pengajuan->jumlah = preg_replace("/[^0-9]/", "", $request->jumlah);

        //PENGAJUAN USER
        if ($pengajuan->User->kk_access == 2) {
            $pengajuan->jumlah = preg_replace("/[^0-9]/", "", $request->jumlah);
            if ($pengajuan->jumlah > $saldo_admin) {
                Alert::error('Approve gagal', 'Maaf, saldo admin tidak cukup');
                return back();
            }
        }
        $pengajuan->save();

        return redirect('home_admin');
    }

    public function tolak(Request $request, $id)
    {
        $pengajuan = Pengajuan::findOrFail($id);
        $pengajuan->sumber = NULL;
        $pengajuan->status = "3";

        $pengajuan->save();

        return redirect('home_admin');
    }

    public function done(Request $request)
    {
        Pengeluaran::with('User', 'pengajuan')->whereIn('id', $request->ids)->update(['status' => '7']);

        return response()->json(['message' => "Kas berhasil diklaim"]);
    }


    public function hapus($pengajuan, $id)
    {
        //$pengajuan = 1 : pengajuan, $pengajuan = 2 : kas
        //HAPUS PENGAJUAN
        if ($pengajuan == 1) {
            $delete = Pengajuan::with('Divisi', 'User')->findOrFail($id);
            //HAPUS PENGELUARAN
        } else if ($pengajuan == 2) {
            $delete = Pengeluaran::with('Divisi', 'User')->findOrFail($id);
        }
        $delete->status = 6;
        $delete->save();

        return back();
    }

    public function kas_divisi(Request $request, $laporan, $id)
    {
        $dataKas = Pengajuan::with('Sumber', 'User', 'Status')->where('divisi_id', $id)->get();
        $divisi = Divisi::get();
        session(['key' => $id]);
        if ($laporan == 1) {
            $laporan = FALSE;
        } else {
            $laporan = TRUE;
        }
        $title = "Admin Kas Kecil";
        $startDate = $this->startDate;
        $endDate = $this->endDate;
        $userList = DB::table('user')->join('pettycash_pengajuan', 'user.id', '=', 'pettycash_pengajuan.user_id')->select('user.*')->get()->unique('id');
        $companyList = DB::table('project_company')->join('pettycash_pengeluaran', 'project_company.project_company_id', '=', 'pettycash_pengeluaran.pembebanan')->select('project_company.*')->get()->unique('project_company_id');
        $filter_keluar = FALSE;
        // $pengajuan_admin = Pengajuan::with('Status')->where('divisi_id', 1)->where('status', 2)->orWhere('status', '4')->get();
        // $admin = $pengajuan_admin->last();
        $Saldo = (new HitungSaldoService)->hitung_saldo_user();
        
        $transaksiServices = new HitungTransaksiService;
        $total_pengajuan = (new HitungPengajuanService)->hitung_pengajuan(null, null, null, $id);
        $total_pengeluaran = $transaksiServices->hitung_belum_klaim(null, null, null, null, $id);
        $total_diklaim = $transaksiServices->hitung_klaim(null, null, null, null, $id);

        return view('admin/main', compact('dataKas', 'divisi', 'title', 'laporan', 'startDate', 'endDate', 'Saldo', 'total_pengajuan', 'total_pengeluaran', 'total_diklaim', 'userList', 'companyList', 'filter_keluar'));
    }

    // public function detail_divisi(Request $request, $id)
    // {
    //     $companySelected = $this->companySelected;
    //     $idPengajuan = ($id) ?? $request->session()->get('key');
    //     $pengajuan = Pengajuan::find($idPengajuan);
    //     $Saldo = (new PengeluaranController)->hitung_saldo();
    //     $totalDiklaim = 0;
    //     $totalPengeluaran = 0;
    //     $startDate = $this->startDate;
    //     $endDate = $this->endDate;
    //     $dataKas = Pengeluaran::with('pengajuan', 'Status', 'Pembebanan', 'COA')->where('pemasukan', '=', $id)->orderBy('status', 'asc')->get();
    //     $dataBelumKlaim = Pengeluaran::with('pengajuan', 'Status', 'Pembebanan', 'COA')->where('pemasukan', '=', $id)->statusProgress()->orderBy('status', 'asc')->get();
    //     foreach ($dataBelumKlaim as $k) {
    //         if ($k->deskripsi != "PENGEMBALIAN SALDO PENGAJUAN") {
    //             $totalPengeluaran = $totalPengeluaran + $k->jumlah;
    //         }
    //     }
    //     session(['key' => $id]);
    //     $kasTotal = Pengeluaran::with('pengajuan', 'Status')->where('pemasukan', '=', $id)->whereIn('status', [7, 8])->where('deskripsi', '!=', "PENGEMBALIAN SALDO PENGAJUAN")->get();
    //     foreach ($kasTotal as $k) {
    //         $totalDiklaim = $totalDiklaim + $k->jumlah;
    //     }
    //     $title = "Pengeluaran Kas Kecil";
    //     $laporan = FALSE;
    //     $company = Company::get();
    //     $totalKeluar = 0;
    //     $totalSetBKK = 0;
    //     $totalBelumSetBKK = 0;
    //     foreach ($dataKas as $value) {
    //         $totalKeluar = $totalKeluar + $value->jumlah;
    //         if ($value->status == 7 && $value->deskripsi != "PENGEMBALIAN SALDO PENGAJUAN") {
    //             $totalBelumSetBKK = $totalBelumSetBKK + $value->jumlah;
    //         } elseif ($value->status == 8 && $value->deskripsi != "PENGEMBALIAN SALDO PENGAJUAN") {
    //             $totalSetBKK = $totalSetBKK + $value->jumlah;
    //         }
    //     }
    //     return view('admin/kas', compact('dataKas', 'pengajuan', 'totalDiklaim', 'totalPengeluaran', 'company','laporan','title','startDate', 'endDate','Saldo','totalKeluar','totalSetBKK','totalBelumSetBKK','companySelected'));
    // }

    // public function kas_company(Request $request, $id)
    // {
    //     $companySelected = $this->companySelected;
    //     $idPengajuan = $request->session()->get('key');
    //     $pengajuan = Pengajuan::find($idPengajuan);
    //     $Saldo = (new PengeluaranController)->hitung_saldo();
    //     $totalDiklaim = 0;
    //     $totalPengeluaran = 0;
    //     $title = "Pengeluaran Kas Kecil";
    //     $laporan = FALSE;
    //     $startDate = $this->startDate;
    //     $endDate = $this->endDate;
    //     $dataKas = Pengeluaran::with('pengajuan', 'Status', 'Pembebanan', 'COA')->where('pemasukan', '=', $idPengajuan)->where('status', '!=', 6)->where('pembebanan', $id)->orderBy('status', 'asc')->get();
    //     $belumDiklaim = Pengeluaran::with('pengajuan', 'Status', 'Pembebanan', 'COA')->where('pemasukan', '=', $idPengajuan)->whereNotIn('status', [3, 6, 7, 8])->where('pembebanan', $id)->get();
    //     foreach ($belumDiklaim as $k) {
    //         $totalPengeluaran = $totalPengeluaran + $k->jumlah;
    //     }
    //     $kasTotal = Pengeluaran::with('pengajuan', 'Status')->where('pemasukan', '=', $idPengajuan)->whereIn('status', [7, 8])->where('pembebanan', $id)->get();
    //     foreach ($kasTotal as $k) {
    //         $totalDiklaim = $totalDiklaim + $k->jumlah;
    //     }
    //     $company = Company::get();
    //     $totalKeluar = 0;
    //     $totalSetBKK = 0;
    //     $totalBelumSetBKK = 0;
    //     foreach ($dataKas as $value) {
    //         $totalKeluar = $totalKeluar + $value->jumlah;
    //         if ($value->status == 7 && $value->deskripsi != "PENGEMBALIAN SALDO PENGAJUAN") {
    //             $totalBelumSetBKK = $totalBelumSetBKK + $value->jumlah;
    //         } elseif ($value->status == 8 && $value->deskripsi != "PENGEMBALIAN SALDO PENGAJUAN") {
    //             $totalSetBKK = $totalSetBKK + $value->jumlah;
    //         }
    //     }
    //     return view('admin/kas', compact('dataKas', 'pengajuan', 'totalDiklaim', 'totalPengeluaran', 'company','title','laporan','startDate', 'endDate','Saldo','totalKeluar','totalSetBKK','totalBelumSetBKK','companySelected'));
    // }

    public function edit_done($id)
    {
        $edit = FALSE;
        $pengeluaran = Pengeluaran::with('pengajuan')->findOrFail($id);

        return view('admin/form-edit-done', ['pengeluaran' => $pengeluaran, 'edit' => $edit]);
    }

    public function simpan_done(Request $request, $id)
    {
        $pengeluaran = Pengeluaran::with('pengajuan')->findOrFail($id);

        $pengeluaran->tanggal = $request->tanggal;
        $pengeluaran->deskripsi = $request->deskripsi;
        $pengeluaran->jumlah = preg_replace("/[^0-9]/", "", $request->jumlah);
        $pengeluaran->tanggal_respon = $request->tanggal_respon;

        $pengeluaran->save();

        return redirect('home_admin');
    }

    public function batal_done($id)
    {
        Pengeluaran::with('pengajuan')->find($id)->update(['status' => '4', 'tanggal_respon' => null]);

        return back();
    }

    public function set_bkk(Request $request)
    {
        $pengeluaran = Pengeluaran::with('User')->whereIn('id', $request->ids)->update(['status' => '8', 'tanggal_set_bkk' => Carbon::now()]);

        return response()->json(['message' => "Kas Diset BKK"]);
    }

    public function done_pengajuan($id)
    {
        Pengajuan::find($id)->update(['status' => '5']);

        return back();
    }

    public function konfirm_kembali($id)
    {
        $pengajuan = Pengajuan::find($id)->update(['status' => 10]);
        return back();
    }

    public function set_tanggal_kembali(Request $request)
    {
        $pengeluaran = Pengeluaran::with('User')->whereIn('id', $request->ids)->update(['status' => '10', 'tanggal_uang_kembali' => Carbon::now()]);

        return response()->json(['message' => "Kas telah dikembalikan"]);
    }
}
