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
use App\Models\BKK;
use App\Models\BKKHeader;
use App\Services\HitungSaldoService;
use App\Services\HitungTransaksiService;
use App\Services\HitungPengajuanService;
use App\Utils\PaginateCollection;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class AdminPribadiController extends Controller
{
    public $startDate;
    public $endDate;
    public $company;
    public $companySelected;

    public function __construct()
    {
        $this->startDate = Carbon::now()->startOfYear('d-m-Y');
        $this->endDate = Carbon::now()->endOfYear('d-m-Y');
        $this->company = null;
    }

    public function getTotalPerUser(Collection $userList, int $company = null, string $startDate = null, string $endDate = null): Collection
    {
        $saldoServices = new HitungSaldoService;
        $transaksiServices = new HitungTransaksiService;
        $pengajuanServices = new HitungPengajuanService;
        $userList->map(function ($item) use ($company, $startDate, $endDate, $saldoServices, $transaksiServices, $pengajuanServices) {
            $item->total_pengajuan = $pengajuanServices->hitung_pengajuan($item->id);
            $item->total_pengeluaran = $transaksiServices->hitung_belum_klaim($item->id, $startDate, $endDate, $company);
            $item->total_diklaim = $transaksiServices->hitung_klaim($item->id, $startDate, $endDate, $company);
            $item->sisa_saldo = $saldoServices->hitung_saldo_user($item->id, $company);
        });

        return $userList;
    }

    public function getGrandTotalTransaksi(string $startDate = null, string $endDate = null, int $company = null): array
    {
        $saldoServices = new HitungSaldoService;
        $transaksiServices = new HitungTransaksiService;
        $pengajuanServices = new HitungPengajuanService;
        $Saldo = $pengajuanServices->hitung_pengajuan();
        $totalKeluar = $transaksiServices->hitung_belum_klaim(null, $startDate, $endDate, $company);
        $totalKlaim = $transaksiServices->hitung_klaim(null, $startDate, $endDate, $company);
        $sisa = $saldoServices->hitung_saldo_all_user($company);

        return [$Saldo, $totalKeluar, $totalKlaim, $sisa];
    }

    public function laporan_keluar(Request $request)
    {
        $title = "Laporan Kas Kecil";
        $company = Company::isPribadi()->get();
        $laporan = TRUE;
        $startDate = $request->startDate ? $request->startDate : $this->startDate;
        $endDate = $request->endDate ? $request->endDate : $this->endDate;
        $selectedStatus = ($request->status) ? Status::find($request->status) : null;
        $selectedCompany = ($request->company) ? Company::find($request->company) : null;
        $selectedUser = ($request->user) ? User::find($request->user) : null;
        $status = Status::whereIn('id', [4, 6, 7, 8, 10])->get();
        $userList = DB::table('user')->join('pettycash_pengajuan', 'user.id', '=', 'pettycash_pengajuan.user_id')->select('user.*')->get()->unique('id');
        $userList = $this->getTotalPerUser($userList, $request->company, null, null);
        [$Saldo, $totalKeluar, $totalKlaim, $sisa] = $this->getGrandTotalTransaksi(null, null, $request->company);
        // $dataKas = DB::table('pettycash_pengeluaran')->select('coa',DB::raw('sum(jumlah) as total'))->groupBy('coa')->get();
        $dataKas = Pengeluaran::with('Project','pengajuan', 'Status', 'COA', 'Pembebanan', 'unit', 'User')->bukanPengembalianSaldo()->orderBy('status', 'asc')->isPribadi()
            ->searchByDateRange($startDate, $endDate)
            ->searchByCompany($request->company)
            ->searchByStatus($request->status)
            ->searchByUser((Auth::user()->kk_access==2) ? Auth::user()->id : $request->user)
            ->searchByProject($request->project)
            ->get();
        (new PengeluaranController)->set_tanggal($startDate, $endDate);
        return view('pribadi/kas', compact('title', 'startDate', 'endDate', 'company', 'userList', 'dataKas', 'Saldo', 'totalKeluar', 'totalKlaim', 'sisa', 'laporan', 'status', 'selectedStatus', 'selectedCompany', 'selectedUser'));
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
        $company = Company::isPribadi()->get();
        $laporan = FALSE;
        $startDate = $request->startDate ? $request->startDate : $this->startDate;
        $endDate = $request->endDate ? $request->endDate : $this->endDate;
        $selectedStatus = ($request->status) ? Status::find($request->status) : Status::find(4);
        $selectedCompany = ($request->company) ? Company::find($request->company) : null;
        $selectedUser = ($request->user) ? User::find($request->user) : null;
        $status = Status::whereIn('id', [4, 6, 7, 8, 10])->get();
        $userList = DB::table('user')->join('pettycash_pengajuan', 'user.id', '=', 'pettycash_pengajuan.user_id')->select('user.*')->get()->unique('id');
        $userList = $this->getTotalPerUser($userList, $request->company, null, null);
        [$Saldo, $totalKeluar, $totalKlaim, $sisa] = $this->getGrandTotalTransaksi(null, null, $request->company);
        $dataKas = Pengeluaran::with('Project','pengajuan', 'Status', 'COA', 'Pembebanan', 'unit', 'User')->bukanPengembalianSaldo()->isPribadi()
            ->searchByDateRange($startDate, $endDate)->orderBy('status', 'asc')
            ->where(function ($query) use ($selectedStatus) {
                ($selectedStatus) ? $query->searchByStatus($selectedStatus->id) : $query->statusProgress();
            })
            ->searchByCompany($request->company)
            ->searchByUser((Auth::user()->kk_access==2) ? Auth::user()->id : $request->user)
            ->searchByProject($request->project)
            ->get();
        (new PengeluaranController)->set_tanggal($startDate, $endDate);
        return view('pribadi/kas', compact('title', 'startDate', 'endDate', 'company', 'userList', 'dataKas', 'Saldo', 'totalKeluar', 'totalKlaim', 'sisa', 'laporan', 'status', 'selectedStatus', 'selectedCompany', 'selectedUser'));
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

    public function create_bkk()
    {
        return view('pribadi/create_bkk');
    }

    public function list_bkk(Request $request)
    {
        $companyList = DB::table('project_company')->join('pettycash_pengeluaran', 'project_company.project_company_id', '=', 'pettycash_pengeluaran.pembebanan')
            ->select('project_company.*')->whereIn('project_company_id',[28,29,30])->get()
            ->unique('project_company_id');
        $dataBkk = BKKHeader::where('status', 1)->where('project_id', '!=', null)->orderByDesc('created_at')->isPribadi()->get();
        $title = "List BKK";
        $selectedCompany = ($request->company) ?? $this->company;
        $startDate = ($request->startDate) ? $request->startDate  : $this->startDate;
        $endDate = ($request->endDate) ? $request->endDate : $this->endDate;
        $barcode = $request->barcode;
        $dataBkk = BKKHeader::where('status',1)->where('project_id', '!=', null)->searchByBarcode($barcode)->whereBetween('tanggal', [$startDate, $endDate])->isPribadi()->orderByDesc('created_at')->get();
        if ($selectedCompany) {
            $dataBkk = $dataBkk->filter(function($item) use ($selectedCompany) {
                return $item->project->project_company_id == $selectedCompany->project_company_id;
            });
        }
        return view('pribadi/bkk', compact('title', 'companyList', 'dataBkk', 'selectedCompany', 'startDate', 'endDate', 'barcode'));
    }

    public function detail_bkk($id)
    {
        $title = "BKK Detail";
        $bkkHeader = BKKHeader::find($id);
        $bkkDetail = BKK::where('bkk_header_id', $bkkHeader->id)->get();
        $totalPayment = $bkkDetail->sum('payment');
        $totalDpp = $bkkDetail->sum('dpp');
        $totalPpn = $bkkDetail->sum('ppn');
        $totalPph = $bkkDetail->sum('pph');
        return view('pribadi/bkk_detail', compact('title', 'bkkHeader', 'bkkDetail', 'totalPayment', 'totalDpp', 'totalPph', 'totalPpn'));
    }

    public function create_kas() {
        return view('pribadi/form_kas');
    }

    public function edit_kas(Request $request, $id)
    {
        $id_kas = $id;

        return view('pribadi.form-edit', compact('id_kas'));
    }
}
