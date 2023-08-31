<?php

namespace App\Http\Livewire;

use App\Models\BKKHeader;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class ListBkk extends Component
{
    public $selectedCompany;
    public $startDate;
    public $endDate;
    public $company;

    public function mount()
    {
        $this->startDate = Carbon::now()->startOfMonth()->format('Y-m-d');
        $this->endDate = Carbon::now()->endOfMonth()->format('Y-m-d');
    }

    public function render()
    {
        $companyList = DB::table('project_company')->join('pettycash_pengeluaran', 'project_company.project_company_id', '=', 'pettycash_pengeluaran.pembebanan')->select('project_company.*')->get()->unique('project_company_id');
        $dataBkk = BKKHeader::where('status',1)->where('project_id','!=',null)->get();
        return view('livewire.list-bkk', compact('companyList','dataBkk'));
    }

    public function getBkkDetail($id) {
        $this->emit('DetailBkk.showBkkDetail', $id);
    }

}
