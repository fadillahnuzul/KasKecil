<?php

namespace App\Http\Livewire;

use App\Http\Controllers\PengeluaranController;
use App\Models\Coa;
use App\Models\Company;
use App\Models\Project;
use App\Services\CekBudgetService;
use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

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
    public $isInBudget;

    protected $listeners = ['getSelectedCoaInput' => 'getCoa'];

    public function mount()
    {
        $this->companyList = Company::get();
    }

    public function render()
    {
        $projectList = Project::where('project_company_id', $this->selectedCompany)->get();
        // $coaList = DB::table('coa')->select('coa.*')->join('budget', 'coa.coa_id', '=', 'budget.kode_coa')->get();
        // $coaList = Coa::where('status', '!=', 0)->searchCoa($this->searchCoa)->orderBy('code')->get();
        $coaList = Coa::join('budget', function($q){
            $q->on('budget.kode_coa','=', 'coa.coa_id');
        })->searchCoa($this->searchCoa)->orderBy('code')->get()->unique('coa_id');
        if (!$this->selectedCoaExist && $this->searchCoa && $coaList) {
            $this->selectedCoa = $coaList->first()->coa_id;
        }
        return view('livewire.create-kas', compact('projectList', 'coaList'));
    }

    public function getCoa($coaId = null)
    {
        if ($coaId) {
            $this->selectedCoa = $coaId;
            $this->selectedCoaExist = true;
        }
    }

    public function cekBudgetCreateKas()
    {
        $cekBudget = new CekBudgetService;
        $budgetCOA = $cekBudget->getBudget($this->selectedCompany, $this->selectedCoa, $this->selectedDate);
        if (!$budgetCOA) {
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
        if ((!$this->isInBudget)&&($coa->status_budget=='budget')) {
            session()->flash('budget_kurang', 'Input kas gagal, budget pada COA ini tidak cukup. Ajukan penambahan budget.');
            return false;
        }
        return true;
    }

    public function getCompanyProject()
    {
        $this->jumlah = preg_replace("/[^0-9]/", "", $this->jumlah);
        $budget = $this->cekBudgetCreateKas();
        $inSaldo = $this->cekSaldo();
        $statusBudget = $this->statusBudgetCoa();
        $inBudget = ($budget && !$this->isInBudget) ? 1 : null;
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
            (new PengeluaranController)->save($data_kas);
        }
    }
}
