<?php

namespace App\Http\Livewire;

use App\Http\Controllers\PengeluaranController;
use App\Models\Coa;
use App\Models\Company;
use App\Models\Pengeluaran;
use App\Models\Project;
use Carbon\Carbon;
use Livewire\Component;

class EditKas extends Component
{
    public $id_kas;
    public $kas;
    public $searchCoa;
    public $companyList;
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
        $this->kas = Pengeluaran::with('coa')->find($this->id_kas);
        $this->companyList = Company::get();
        $this->setValueAwal();
    }
    
    public function render()
    {
        $projectList = Project::where('project_company_id',$this->selectedCompany)->get();
        $coaList = Coa::where('status', '!=', 0)->searchCoa($this->searchCoa)->orderBy('code')->get();
        if(!$this->selectedCoa) {
            ($coaList->first()->coa_id) ? $this->selectedCoa = $coaList->first()->coa_id : null;
        }
        return view('livewire.edit-kas',compact('projectList','coaList'));
    } 

    public function setValueAwal()
    {
        $this->selectedCompany = $this->kas->pembebanan;
        $this->selectedProject = $this->kas->project_id;
        $this->selectedCoa = $this->kas->coa;
        $this->selectedDate = Carbon::parse($this->kas->tanggal)->format('d-m-Y');
        $this->deskripsi = $this->kas->deskripsi;
        $this->jumlah = $this->kas->jumlah;
        $this->pic = $this->kas->pic;
        $this->tujuan = $this->kas->tujuan;
    }

    public function updateKas()
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
        (new PengeluaranController)->update($data_kas, $this->id_kas);
    }
}
