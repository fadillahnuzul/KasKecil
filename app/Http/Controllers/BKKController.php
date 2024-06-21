<?php

namespace App\Http\Controllers;

use App\Models\BKK;
use App\Models\BKKHeader;
use App\Models\Project;
use App\Services\PrintBkk;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use App\Services\TerbilangNominal;
use Carbon\Carbon;

class BKKController extends Controller
{
    public $startDate;
    public $endDate;
    public $company;
    public $companySelected;

    public function __construct()
    {
        $this->startDate = Carbon::now()->month(3)->startOfMonth();
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
        (new PrintBkk)->printBkk( $bkkHeader, $bkkDetail, $tipe);
    }
}
