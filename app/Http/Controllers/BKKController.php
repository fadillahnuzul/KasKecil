<?php

namespace App\Http\Controllers;
use App\Models\BKK;
use App\Models\BKKHeader;
use App\Models\Company;
use App\Models\Project;
use App\Models\Coa;
use App\Models\Rekening;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class BKKController extends Controller
{
    public function create()
    {
        $Company = Company::get();
        $Project = Project::get();
        $Coa = Coa::get();
        $Rekening = Rekening::get();
        $Partner = DB::table('partner')->get();
        $title = "Create BKK";
        return view('admin/create_bkk', compact('title','Company','Project','Coa','Rekening','Partner'));
    }

    public function save(Request $request)
    {
        $bkk_header = new BKKHeader;
        $bkk = new BKK;
        //save bkk header
        $bkk_header->bank_id = $request->rekening;
        $bkk_header->name = null;
        $bkk_header->tanggal = $request->tanggal;
        $bkk_header->partner = $request->partner;
        $bkk_header->otorisasi = 0;
        $bkk_header->project_id = $request->project;
        $bkk_header->layer_cashflow_id = 0;
        $bkk_header->created_by = Auth::user()->id;
        $bkk_header->created_at = Carbon::now()->toDateTimeString();
        $bkk_header->save();
        //save bkk
        $bkk->bkk_header_id = $bkk_header->id;
        $bkk->ppn = 0;
        $bkk->pph = 0;
        $bkk->coa_id = $request->coa;
        
    }
}
