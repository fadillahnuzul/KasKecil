<?php

namespace App\Http\Livewire\Pribadi;

use App\Http\Controllers\PengeluaranController;
use App\Models\Coa;
use App\Models\Company;
use App\Models\Project;
use App\Models\Unit;
use App\Services\CekBudgetService;
use App\Services\HitungSaldoService;
use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class CreateKas extends Component
{
    public $companyList;
    public $unitList;
    public $selectedCompany;
    public $selectedProject;
    public $selectedDate;
    public $selectedUnit;
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
        $this->companyList = Company::isPribadi()->get();
        $this->unitList = Unit::where('status', 'enable')->get()->sortBy('name');
    }

    public function render()
    {
        $projectList = Project::where('project_company_id', $this->selectedCompany)->isPribadi()->get();
        // $coaList = DB::table('coa')->select('coa.*')->join('budget', 'coa.coa_id', '=', 'budget.kode_coa')->get();
        // $coaList = Coa::where('status', '!=', 0)->searchCoa($this->searchCoa)->orderBy('code')->get();
        $coaList = Coa::join('budget', function($q){
            $q->on('budget.kode_coa','=', 'coa.coa_id');
        })->searchCoa($this->searchCoa)->orderBy('code')->get()->unique('coa_id');
        if (!$this->selectedCoaExist && $coaList->first() && $this->searchCoa) {
            $this->selectedCoa = $coaList->first()->coa_id;
        }
        if (!$this->selectedUnit) {
            $this->selectedUnit = Auth::user()->level;
        }

        // $this->selectedCoaExist = false;
        return view('livewire.pribadi.create-kas', compact('projectList', 'coaList'));
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
        if (!$budgetCOA[0]['budgetbulan'] && !$budgetCOA[0]['budgettahun']) {
            // session()->flash('tidak_ada_budget', 'Input kas gagal, tidak ada budget pada COA ini');
            return false;
        }
        $this->isInBudget = $cekBudget->isInBudget($budgetCOA[0]['budgetbulan'], $budgetCOA[0]['budgettahun'], $this->jumlah);
        return true;
    }

    public function cekSaldo()
    {
        $saldo = (new HitungSaldoService)->hitung_saldo_user(Auth::user()->id);
        if ($this->jumlah > $saldo) {
            session()->flash('message_kas', 'Input kas gagal, saldo tidak cukup');
            return false;
        }
        return true;
    }

    public function createPettyCash()
    {
        $this->jumlah = preg_replace("/[^0-9]/", "", $this->jumlah);
        // $budget = $this->cekBudgetCreateKas();
        // $inSaldo = $this->cekSaldo();
        // $inBudget = (!$budget && !$this->isInBudget) ? 1 : null;
        $data_kas = array([
            'date' => $this->selectedDate,
            'deskripsi' => $this->deskripsi,
            'jumlah' => $this->jumlah,
            'coa' => $this->selectedCoa,
            'company' => $this->selectedCompany,
            'project' => $this->selectedProject,
            'pic' => $this->pic,
            'tujuan' => $this->tujuan,
            'in_budget' => 1,
            // 'in_budget' => 0,
            'unit_id' => null,
        ]);
        // if ($inSaldo) {
        //     (new PengeluaranController)->save($data_kas);
        // }
        (new PengeluaranController)->save($data_kas, true);
    }
}