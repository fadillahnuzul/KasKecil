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
    public $selectedCompany;
    public $selectedProject;
    public $selectedDate;
    public $deskripsi;
    public $jumlah;
    public $selectedCoa;
    public $pic;
    public $tujuan;
    public $searchCoa;
    public $selectedCoaExist;

    protected $listeners = ['getSelectedCoaInput' => 'getCoa'];

    public function mount()
    {
        $this->companyList = Company::get();
    }

    public function render()
    {
        $projectList = Project::where('project_company_id',$this->selectedCompany)->get();
        $coaList = Coa::where('status', '!=', 0)->searchCoa($this->searchCoa)->orderBy('code')->get();
        if (!$this->selectedCoaExist && $this->searchCoa && $coaList) {
            $this->selectedCoa = $coaList->first()->coa_id;
        }
        return view('livewire.create-kas', compact('projectList','coaList'));
    }

    public function getCoa($coaId=null)
    {
        if ($coaId) {
            $this->selectedCoa = $coaId;
            $this->selectedCoaExist = true;
        } 
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
