<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Http\Controllers\AdminController;
use App\Models\Company;
use App\Models\Pengeluaran;
use Illuminate\Support\Facades\DB;

class FilterCompany extends Component
{
    public $company;
    public $companyList;

    public function render()
    {
        return view('livewire.filter-company');
    }

    public function __construct()
    {
        $this->companyList = Company::join('pettycash_pengeluaran', 'project_company.project_company_id', '=', 'pettycash_pengeluaran.pembebanan')->get();
    }
}
