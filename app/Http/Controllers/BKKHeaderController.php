<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BKKHeader;
use App\Models\Company;
use App\Utils\PaginateCollection;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;

class BKKHeaderController extends Controller
{
    public $startDate;
    public $endDate;
    public $company;
    public $selectedCompany;

    public function __construct()
    {
        $this->startDate = Carbon::now()->startOfYear('d-m-Y');
        $this->endDate = Carbon::now()->endOfYear('d-m-Y');
        $this->selectedCompany = null;
    }

    public function index(Request $request): View
    {
        $companyList = DB::table('project_company')->join('pettycash_pengeluaran', 'project_company.project_company_id', '=', 'pettycash_pengeluaran.pembebanan')->select('project_company.*')->whereNotIn('project_id', [111, 112])->get()->unique('project_company_id');
        $title = "List BKK";
        $selectedCompany = Company::find(($request->company)) ?? $this->selectedCompany;
        $selectedCompanyId = $selectedCompany ? $selectedCompany->project_company_id : null;
        $startDate = ($request->startDate) ? $request->startDate  : $this->startDate;
        $endDate = ($request->endDate) ? $request->endDate : $this->endDate;
        $barcode = $request->barcode;
        $dataBkk = BKKHeader::where('status', 1)->where('project_id', '!=', null)->searchByBarcode($barcode)->whereBetween('tanggal', [$startDate, $endDate])->notPribadi()->orderByDesc('created_at')->get();
        if ($selectedCompany) {
            $dataBkk = $dataBkk->filter(function ($item) use ($selectedCompanyId) {
                return $item->project->project_company_id == $selectedCompanyId;
            });
        }
        // dd($this->selectedCompany);
        return view('admin/bkk', compact('title', 'companyList', 'dataBkk', 'selectedCompany', 'selectedCompanyId', 'startDate', 'endDate', 'barcode'));
    }
    public function store($bkk_header_data)
    {
        $bkk_header = BKKHeader::create($bkk_header_data);
        return $bkk_header;
    }
}
