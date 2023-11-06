<?php

namespace App\Http\Controllers;

use App\Http\Controllers\AdminController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Pengajuan;
use App\Models\Pengeluaran;
use App\Models\Status;
use App\Models\Pembebanan;
use App\Models\Divisi;
use App\Models\Saldo;
use App\Models\Coa;
use App\Models\Company;
use App\Models\Project;
use App\Exports\KasKecilExport;
use Alert;
use Carbon\Carbon;
use App\Services\CekBudgetService;
use Illuminate\Support\Facades\DB;

class PengeluaranController extends Controller
{
    public $startDate;
    public $endDate;
    public $company;
    public $companySelected;

    public function __construct()
    {
        $this->startDate = Carbon::now()->month(9)->startOfMonth();
        $this->endDate = Carbon::now()->endOfYear('d-m-Y');
        $this->company = NULL;
        $this->companySelected = null;
        session(['startDate' => $this->startDate]);
        session(['endDate' => $this->endDate]);
        session(['company' => $this->company]);
    }

    public function hitung_saldo($id = null)
    {
        $id = ($id) ? $id : Auth::user()->id;
        $saldo = (new AdminController)->hitung_pengajuan($id);
        $kas = (new AdminController)->hitung_belum_klaim($id);
        $saldo = $saldo - $kas;
        return $saldo;
    }

    public function fetchProject(Request $request)
    {
        $project = Project::where('project_company_id', $request->project_company_id)->get();
        return response()->json($project);
    }

    public function index(Request $request, $id = null)
    {
        $companySelected = $this->companySelected;
        $pengajuan = ($id) ? Pengajuan::find($id) : null;
        $company = Company::get();
        $button_kas = TRUE;
        $title = "Kas Keluar";
        $startDate = ($request->startDate) ? $request->startDate : $this->startDate;
        $endDate = ($request->endDate) ? $request->endDate : $this->endDate;
        $selectedStatus = ($request->status) ? Status::find($request->status) : Status::find(4);
        $selectedCompany = ($request->company) ? Company::find($request->company) : null;
        $project_company_id = ($selectedCompany) ? $selectedCompany->project_company_id : null;
        $status = Status::whereIn('id', [4, 6, 7, 8])->get();
        $dataKas = Pengeluaran::with('Pembebanan', 'Status', 'COA')->searchByUser(Auth::user()->id)->orderBy('status', 'asc')
            ->where(function ($query) use ($selectedStatus) {
                ($selectedStatus) ? $query->searchByStatus($selectedStatus->id) : $query->statusProgress();
            })
            ->searchByDateRange($startDate, $endDate)
            ->searchByCompany($request->company)
            ->searchByProject($request->project)
            ->get();
        session(['key' => $id]);
        $saldo = $this->hitung_saldo(Auth::user()->id);
        $totalPengeluaran = (new AdminController)->hitung_belum_klaim(Auth::user()->id, $startDate, $endDate, $project_company_id);
        session(['key' => $id]);

        return view('detail_pengajuan', compact('dataKas', 'title', 'button_kas', 'startDate', 'endDate', 'saldo', 'totalPengeluaran', 'pengajuan', 'company', 'companySelected', 'status', 'selectedStatus', 'selectedCompany'));
    }

    public function laporan(Request $request)
    {
        $companySelected = $this->companySelected;
        session(['company' => $this->company]);
        $startDate = ($request->startDate) ? $request->startDate : $this->startDate;
        $endDate = ($request->endDate) ? $request->endDate : $this->endDate;
        $button_kas = FALSE;
        $selectedStatus = ($request->status) ? Status::find($request->status) : null;
        $selectedCompany = ($request->company) ? Company::find($request->company) : null;
        $status = Status::whereIn('id', [4, 6, 7, 8])->get();
        if (Auth::user()->kk_access == 1) {
            $dataKas = Pengeluaran::with('pengajuan', 'Status', 'Pembebanan')->statusKlaimAndSetBKK()
                ->searchByDateRange($startDate, $endDate)
                ->searchByCompany($request->company)
                ->searchByStatus($request->status)
                ->searchByProject($request->project)
                ->get();
        } else {
            $dataKas = Pengeluaran::with('pengajuan', 'Status', 'Pembebanan')->where('user_id', Auth::user()->id)->where('status', '!=', 6)->bukanPengembalianSaldo()
                ->searchByDateRange($startDate, $endDate)
                ->searchByCompany($request->company)
                ->searchByStatus($request->status)
                ->searchByProject($request->project)
                ->get();
        }
        $title = "Laporan Pengeluaran Kas Kecil";
        $saldo = $this->hitung_saldo(Auth::user()->id);
        $company = Company::get();

        return view('detail_pengajuan', compact('dataKas', 'title', 'button_kas', 'startDate', 'endDate', 'saldo', 'company', 'companySelected', 'status', 'selectedStatus', 'selectedCompany'));
    }

    public function set_tanggal($startDate, $endDate)
    {
        session(['startDate' => $startDate]);
        session(['endDate' => $endDate]);
    }

    public function create(Request $request)
    {
        return view('form_kas');
    }

    public function save($data)
    {
        $kas = Pengeluaran::updateOrCreate([
            'tanggal' => $data[0]['date'],
            'deskripsi' => $data[0]['deskripsi'],
            'jumlah' => $data[0]['jumlah'],
            'divisi_id' => Auth::user()->level,
            'status' => '4',
            'coa' => $data[0]['coa'],
            'pic' => $data[0]['pic'],
            'pembebanan' => $data[0]['company'],
            'project_id' => $data[0]['project'],
            'tujuan' => $data[0]['tujuan'],
            'user_id' => Auth::user()->id,
            'in_budget' => $data[0]['in_budget'],
        ]);

        return redirect('kas_keluar');
    }

    public function edit(Request $request, $id)
    {
        $id_kas = $id;

        return view('form-edit', compact('id_kas'));
    }

    public function update($data, $id)
    {
        $kas = Pengeluaran::find($id)->update([
            'tanggal' => $data[0]['date'],
            'deskripsi' => $data[0]['deskripsi'],
            'jumlah' => $data[0]['jumlah'],
            'divisi_id' => Auth::user()->level,
            'status' => '4',
            'coa' => $data[0]['coa'],
            'pic' => $data[0]['pic'],
            'pembebanan' => $data[0]['company'],
            'project_id' => $data[0]['project'],
            'tujuan' => $data[0]['tujuan'],
            'in_budget' => $data[0]['in_budget'],
        ]);
        
        return redirect('kas_keluar');
    }

    public function delete($id)
    {
        Pengeluaran::with('Divisi', 'User')->find($id)->update(['status' => '6']);
        return back();
    }

    public function done(Request $request)
    {
        if (Auth::user()->kk_access == 1) { //Jika user admin langsung klaim
            Pengeluaran::whereIn('id', $request->ids)->update(['status' => '7', 'tanggal_respon' => Carbon::now()]);
        } else if (Auth::user()->kk_access == 2) { //Jika user biasa tunggu klaim admin
            Pengeluaran::whereIn('id', $request->ids)->update(['status' => '4', 'tanggal_respon' => Carbon::now()]);
        }
        Pengajuan::find($request->session()->get('key'))->update(['status' => '4']);

        return response()->json(['message' => "Kas berhasil diklaim"]);
    }

    // public function filter(Request $request)
    // {
    //     $companySelected = $this->companySelected;
    //     $button_kas = FALSE;
    //     $this->startDate = $request->startDate; session(['startDate' => $request->startDate]);
    //     $this->endDate = $request->endDate; session(['endDate' => $request->endDate]);
    //     if (Auth::user()->kk_access == 1) {
    //         $dataKas = Pengeluaran::with('pengajuan', 'Status', 'COA','Pembebanan')->statusKlaimAndSetBKK()->searchByDateRange($this->startDate,$this->endDate)
    //                 ->where('deskripsi','!=','PENGEMBALIAN SALDO PENGAJUAN')->orderBy('status','asc')->get();
    //     } elseif (Auth::user()->kk_access == 2) {
    //         $dataKas = Pengeluaran::with('pengajuan', 'Status', 'COA')->where('user_id', Auth::user()->id)->statusKlaimAndSetBKK()->searchByDateRange($this->startDate,$this->endDate)->orderBy('status','asc')->get();
    //     }
    //     $company = Company::get();
    //     $title = "Laporan Pengeluaran Kas Kecil";
    //     $Saldo = $this->hitung_saldo(Auth::user()->id);

    //     if (Auth::user()->kk_access == 1) {
    //         $startDate = $this->startDate; $endDate = $this->endDate;
    //         $totalKeluar = 0; $totalSetBKK = 0; $totalBelumSetBKK = 0;
    //         foreach($dataKas as $value) {
    //             $totalKeluar = $totalKeluar + $value->jumlah;
    //             if($value->status == 7) {
    //                 $totalBelumSetBKK = $totalBelumSetBKK + $value->jumlah;
    //             } elseif ($value->status == 8) {
    //                 $totalSetBKK = $totalSetBKK + $value->jumlah;
    //             }
    //         }
    //         return view('/admin/kas', compact('title', 'startDate', 'endDate', 'company','dataKas','Saldo','totalKeluar','totalSetBKK','totalBelumSetBKK','companySelected'));
    //     } elseif (Auth::user()->kk_access == 2) {
    //         return view('detail_pengajuan', ['dataKas' => $dataKas], ['title' => $title, 'button_kas' => $button_kas, 'startDate' => $this->startDate, 'endDate' => $this->endDate, 'saldo' => $Saldo, 'company'=>$company]);
    //     }
    // }

    // public function export(Request $request)
    // {
    //     $startDate = ($request->startDate) ? $request->startDate : $request->session()->get('startDate');
    //     $endDate = ($request->endDate) ? $request->endDate : $request->session()->get('endDate');
    //     $company = $request->session()->get('company');

    //     return (new KasKecilExport($startDate,$endDate,$company))->download("Laporan_Kas_Kecil" . $startDate . $endDate . ".xlsx");
    // }

    // public function pengembalian_saldo(Request $request, $id) {
    //     $kas = new Pengeluaran;
    //     $kas->tanggal = $request->tanggal;
    //     $kas->deskripsi = "PENGEMBALIAN SALDO PENGAJUAN";
    //     // $kas->pemasukan = $id;
    //     $kas->user_id = Auth::user()->id;
    //     $kas->status = "4";
    //     $kas->divisi_id = Auth::user()->level;
    //     $saldo = $this->hitung_saldo(Auth::user()->id);
    //     //Kas admin
    //     if ($kas->jumlah > $saldo) {
    //         Alert::error('Input kas gagal', 'Maaf, saldo tidak cukup');
    //         return back();
    //     } else {
    //         $kas->save();
    //     }
    //     return redirect('home');
    // }
}
