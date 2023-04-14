<?php

namespace App\Http\Livewire;

use App\Http\Controllers\PengeluaranController;
use App\Models\Coa;
use App\Models\Company;
use App\Models\Project;
use App\Services\CekBudgetService;
use Livewire\Component;

class CreateKas extends Component
{
    public $companyList;
    public $coaList;
    public $selectedCompany;
    public $selectedProject;
    public $selectedDate;
    public $deskripsi;
    public $jumlah;
    public $selectedCoa;
    public $pic;
    public $tujuan;

    public function mount()
    {
        $this->companyList = Company::get();
        $this->coaList = Coa::where('status', '!=', 0)->get();
    }

    public function render()
    {
        $projectList = Project::where('project_company_id',$this->selectedCompany)->get();
        return view('livewire.create-kas', ['projectList'=>$projectList]);
    }

    public function getCompanyProject()
    {
        $data_kas = array([
            'date' => $this->selectedDate,
            'deskripsi' => $this->deskripsi,
            'jumlah' => $this->jumlah,
            'coa' => $this->selectedCoa,
            'company' => $this->selectedCompany,
            'project' => $this->selectedProject,
            'pic' => $this->pic,
            'tujuan' => $this->tujuan,
        ]);
        (new PengeluaranController)->save($data_kas);
    }
}
