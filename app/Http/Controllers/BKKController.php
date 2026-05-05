<?php

namespace App\Http\Controllers;

use App\Models\BKK;
use App\Models\BKKHeader;
use App\Models\Pengeluaran;
use App\Models\Project;
use App\Services\PrintBkk;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use App\Services\TerbilangNominal;
use App\Exports\BkkTransaksiExport;
use App\Exports\JurnalBkkExport;
use App\Models\Company;
use App\Models\Pengajuan;
use App\Services\HitungSaldoService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class BKKController extends Controller
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

    public function index(Request $request, $id): View
    {
        $title = "BKK Detail";
        $bkkHeader = BKKHeader::find($id);
        $bkkDetail = BKK::where('bkk_header_id', $bkkHeader->id)->get();
        $totalPayment = $bkkDetail->sum('payment');
        $totalDpp = $bkkDetail->sum('dpp');
        $totalPpn = $bkkDetail->sum('ppn');
        $totalPph = $bkkDetail->sum('pph');
        $startDate = ($request->startDate) ? $request->startDate  : $this->startDate;
        $endDate = ($request->endDate) ? $request->endDate : $this->endDate;
        return view('admin/bkk_detail', compact('title', 'bkkHeader', 'bkkDetail', 'totalPayment', 'totalDpp', 'totalPph', 'totalPpn', 'startDate', 'endDate'));
    }

    public function create()
    {
        $title = "Create BKK";
        return view('admin/create_bkk', compact('title'));
    }

    public function store($bkk_collection)
    {
        $bkk_detail_array = [];
        foreach ($bkk_collection as $bkk_data) {
            $bkk_detail_data = BKK::create($bkk_data);
            array_push($bkk_detail_array, $bkk_detail_data);
        }
        return $bkk_detail_array;
    }

    public function print($id)
    {
        $bkkHeader = BKKHeader::find($id);
        $bkkDetail = BKK::where('bkk_header_id', $id)->get();
        $tipe = "spi";
        (new PrintBkk)->printBkk($bkkHeader, $bkkDetail, $tipe);
    }

    public function printDetailTransaksiBkk($bkkId)
    {
        $bkk = BKKHeader::with('bank')->find($bkkId);
        $kasBkk = Pengeluaran::with('coa', 'User', 'Divisi')->where('bkk_header_id', $bkkId)->get()->groupBy('coa_id');
        return Excel::download(
            new BkkTransaksiExport($kasBkk, $bkk),
            'bkk-' . $bkk->id . '-pettycash.xlsx'
        );
    }

    public function exportReport(Request $request)
    {
        $company = $request->company;
        // $saldoAwal = (new HitungSaldoService)->hitung_saldo_all_user($company);
        $saldoAwal = 10000000;
        $companyName = Company::find($company)->name;
        $startDate = $request->start_date;
        $endDate = $request->end_date;


        $bkk = BKK::query()
            ->selectRaw('bkk_header.created_at as tanggal, bkk_header.id as bkk_header_id, bkk_header.name as no_bukti, CONCAT(bkk_header.partner," ", pekerjaan) as keterangan, 1 as jenis, payment as nominal')
            ->join('bkk_header', 'bkk_header.id', '=', 'bkk.bkk_header_id')
            ->join('project', 'project.project_id', '=', 'bkk_header.project_id')
            ->join('project_company', 'project_company.project_company_id', '=', 'project.project_company_id')
            ->whereBetween('bkk_header.created_at', [$startDate, $endDate])
            ->where('project_company.project_company_id', $company)
            ->where('bkk_header.partner', 'like', '%Pettycash%')->get();

        $bank = DB::table('bank')
            ->where('company_id', $company)
            ->pluck('bank_id');
        $dataMutasi = collect();
        foreach ($bank as $rekening) {
            $mutasiQuery = DB::table('rr_rekonsiliasi_rekening')
            ->selectRaw('rr_mutasi_rekening_id_' . $rekening . '.tanggal_transfer as tanggal, id_rekening, bkk_header.id as bkk_header_id, bkk_header.name as no_bukti, CONCAT("Penggantian ", bkk_header.partner) as keterangan, 0 as jenis, rr_rekonsiliasi_rekening.nominal as nominal')
            ->join('__abdael_property_spi.bkk_header', 'bkk_header.id', '=', 'rr_rekonsiliasi_rekening.id_bkk_spi')
            ->join('rr_mutasi_rekening_id_' . $rekening, function ($join) use ($rekening) {
                    $join->on('rr_rekonsiliasi_rekening.id_mutasi', '=', 'rr_mutasi_rekening_id_' . $rekening . '.id')
                        ->where('rr_rekonsiliasi_rekening.id_rekening', '=', $rekening);
            })
            ->whereBetween('rr_mutasi_rekening_id_' . $rekening . '.tanggal_transfer', [$startDate, $endDate])
            ->where(function ($query) use ($bkk) {
                $query->where('bkk_header.partner', 'like', '%Pettycash%')->orWhereIn('rr_rekonsiliasi_rekening.id_bkk_spi', $bkk->pluck('bkk_header_id')->unique()->toArray());
            })
            ->get();
            
            $dataMutasi->push($mutasiQuery->flatten());
        }
        $dataMutasi = $dataMutasi->flatten();
        $data = $bkk->concat($dataMutasi)->sortBy('tanggal');

        return Excel::download(new JurnalBkkExport($data, $companyName, $startDate, $endDate, $saldoAwal), 'Mutasi Kas Kecil ' . Carbon::parse($startDate)->format('d-m-Y') . ' to ' . Carbon::parse($endDate)->format('d-m-Y') . '.xlsx');
    }
}
