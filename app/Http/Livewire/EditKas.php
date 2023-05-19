<?php

namespace App\Http\Livewire;

use App\Http\Controllers\PengeluaranController;
use App\Models\Coa;
use App\Models\Company;
use App\Models\Pengeluaran;
use App\Models\Project;
use App\Services\CekBudgetService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
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
    public $isInBudget;


    public function mount()
    {
        $this->kas = Pengeluaran::with('coa')->find($this->id_kas);
        $this->companyList = Company::get();
        $this->setValueAwal();
    }

    public function render()
    {
        $projectList = Project::where('project_company_id', $this->selectedCompany)->get();
        // $coaList = Coa::where('status', '!=', 0)->searchCoa($this->searchCoa)->orderBy('code')->get();
        $coaList = Coa::join('budget', function($q){
            $q->on('budget.kode_coa','=', 'coa.coa_id');
        })->searchCoa($this->searchCoa)->orderBy('code')->get()->unique('coa_id');
        if (!$this->selectedCoa) {
            ($coaList->first()->coa_id) ? $this->selectedCoa = $coaList->first()->coa_id : null;
        }
        return view('livewire.edit-kas', compact('projectList', 'coaList'));
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

    public function cekBudgetEditKas()
    {
        $cekBudget = new CekBudgetService;
        $budgetCOA = $cekBudget->getBudget($this->selectedCompany, $this->selectedCoa, $this->selectedDate);
        if (!$budgetCOA[0]['budgetbulan'] && !$budgetCOA[0]['budgettahun']) {
            session()->flash('tidak_ada_budget', 'Input kas gagal, tidak ada budget pada COA ini');
            return false;
        }
        $this->isInBudget = $cekBudget->isInBudget($budgetCOA[0]['budgetbulan'], $budgetCOA[0]['budgettahun'], $this->jumlah);
        return true;
    }

    public function cekSaldo()
    {
        $saldo = (new PengeluaranController)->hitung_saldo(Auth::user()->id);
        if ($this->jumlah > $saldo) {
            session()->flash('message_kas', 'Input kas gagal, saldo tidak cukup');
            return false;
        }
        return true;
    }

    public function statusBudgetCoa()
    {
        $coa = Coa::find($this->selectedCoa);
        if ((!$this->isInBudget) && ($coa->status_budget == 'budget')) {
            session()->flash('budget_kurang', 'Input kas gagal, budget pada COA ini tidak cukup. Ajukan penambahan budget.');
            return false;
        }
        return true;
    }

    public function updateKas()
    {
        $budget = $this->cekBudgetEditKas();
        $inSaldo = $this->cekSaldo();
        $inBudget = ($budget && !$this->isInBudget) ? 1 : null;
        $statusBudget = $this->statusBudgetCoa();
        $data_kas = array([
            'date' => $this->selectedDate,
            'deskripsi' => $this->deskripsi,
            'jumlah' => $this->jumlah,
            'coa' => $this->selectedCoa,
            'company' => $this->selectedCompany,
            'project' => $this->selectedProject,
            'pic' => $this->pic,
            'tujuan' => $this->tujuan,
            'in_budget' => $inBudget,
        ]);
        if ($budget && $inSaldo && $statusBudget) {
            (new PengeluaranController)->update($data_kas, $this->id_kas);
        }
    }
}
