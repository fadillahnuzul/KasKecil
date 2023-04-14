<?php

namespace App\Http\Livewire;

use App\Models\Company;
use App\Models\Project;
use App\Models\Status;
use Carbon\Carbon;
use Livewire\Component;

class FilterKas extends Component
{
    public $isLaporan;
    public $startDate;
    public $endDate;
    public $statusList;
    public $companyList;
    public $selectedStatus;
    public $selectedCompany;
    public $selectedProject;

    public function mount()
    {
        $this->startDate = Carbon::now()->startOfMonth()->format('Y-m-d');
        $this->endDate = Carbon::now()->endOfMonth()->format('Y-m-d');
        $this->statusList = Status::get();
        $this->companyList = Company::get();
    }

    public function render()
    {
        $projectList = Project::where('project_company_id',$this->selectedCompany)->get();
        return view('livewire.filter-kas',['projectList'=>$projectList]);
    }

    public function getFilter()
    {
        # code...
    }
}
