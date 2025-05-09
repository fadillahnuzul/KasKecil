<?php

namespace App\Http\Livewire;

use App\Models\Coa;
use App\Models\Company;
use Livewire\Component;
use App\Models\Pengeluaran;
use App\Models\Project;
use App\Models\BKK;
use App\Models\BKKHeader;
use App\Models\Rekening;
use App\Models\Partner;
use Carbon\Carbon;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Alert;
use App\Http\Controllers\BKKController;
use App\Http\Controllers\BKKHeaderController;
use App\Models\Unit;
use App\Services\CekBudgetService;
use App\Services\CreateBKKService;

class AddBkk extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $startDate;
    public $endDate;
    public $companyList;
    public $partnerList;
    public $unitList;
    public $selectedCompany;
    public $selectedProject;
    public $selectedRekening;
    public $selectedCoaId;
    public $selectedPartner;
    public $selectedUnit;
    public $manualTypePartner;
    public $tanggalBkk;
    public $selectedKasId = [];
    public $selectedAll = FALSE;
    public $firstId;
    public $selectedKas;
    public $totalKas;
    public $totalKasCoa;
    public $searchCoa;
    public $selectedCoaExist;
    public $bkk;

    protected $listeners = ['getSelectedCoa' => 'getCoa', 'refresh' => '$refresh'];

    public function mount()
    {
        $this->companyList = Company::notPribadi()->get();
        $this->partnerList = Partner::get();
        $this->unitList = Unit::where('status', 'enable')->get()->sortBy('name');
        $this->startDate = Carbon::now()->startOfMonth()->format('Y-m-d');
        $this->endDate = Carbon::now()->endOfMonth()->format('Y-m-d');
    }

    public function render()
    {
        $projectList = Project::where('project_company_id', $this->selectedCompany)->notPribadi()->get();
        $rekeningList = Rekening::where('company_id', $this->selectedCompany)->get();
        $coaList = Coa::join('budget', function ($q) {
            $q->on('budget.kode_coa', '=', 'coa.coa_id');
        })->searchCoa($this->searchCoa)->orderBy('code')
            ->get()->unique('coa_id');
        $coaList = $coaList->push(Coa::where('code', 'like', '2.120.000')->first());
        if (!$this->selectedCoaExist && $coaList->first() && !$this->selectedCoaId) {
            $this->selectedCoaId = $coaList->first()->coa_id;
        }
        if (Auth::user()->kk_access == 1) {
            $kas = Pengeluaran::with('COA', 'project')->whereIn('status', [8, 10])->bukanPengembalianSaldo()->searchByCoa($this->selectedCoaId)
                ->searchByDateRange($this->startDate, $this->endDate)
                ->searchByCompany($this->selectedCompany)
                ->searchByUnit($this->selectedUnit)
                ->notPribadi()
                // ->searchByProject($this->selectedProject)
                ->paginate(10);
        } elseif (Auth::user()->kk_access == 2) {
            $kas = Pengeluaran::with('COA', 'project')->whereIn('status', [8, 10])->where('user_id', Auth::user()->id)->bukanPengembalianSaldo()->searchByCoa($this->selectedCoaId)
                ->searchByDateRange($this->startDate, $this->endDate)
                ->searchByCompany($this->selectedCompany)
                ->searchByUnit($this->selectedUnit)
                ->notPribadi()
                // ->searchByProject($this->selectedProject)
                ->paginate(10);
        }

        $this->selectedCoaExist = false;
        return view('livewire.add-bkk', compact('kas', 'projectList', 'rekeningList', 'coaList'));
    }

    public function getCoa($coaId = null)
    {
        if ($coaId) {
            $this->selectedCoaId = $coaId;
            $this->selectedCoaExist = true;
        }
    }

    public function getSelectedKas()
    {
        // $this->selectedCoaExist = false;
        $this->selectedKas = Pengeluaran::with('coa')->whereIn('id', $this->selectedKasId)->get();
        // foreach($this->selectedKas as $item) {
        //     $coa = Coa::find($item->coa);
        //     if (($coa->status_budget=='budget' && $item->in_budget==1)) {
        //         session()->flash('message_overbudget', 'COA overbudget. Ajukan penambahan budget.');
        //         $this->selectedKas = $this->selectedKas->filter(function($kas) use ($item){
        //             if ($kas->id != $item->getKey()){
        //                 return $kas;
        //             };
        //         });
        //     }
        // }

        $this->totalKas = $this->selectedKas->sum('jumlah');
        $this->selectedKas = $this->selectedKas->sortBy('coa')->groupBy(['coa', 'divisi_id'])->toBase();
        // $statusCoa = $this->cekJumlahCoa();
        // if ($statusCoa == false) {
        //     session()->flash('message_coa', 'Gagal menambahkan transaksi, COA yang dipilih lebih dari 5');
        // }
        $this->hitungKasCOA();
    }

    public function cekBudget()
    {
        foreach ($this->selectedKas as $key => $value) {
            $budget = new CekBudgetService;
            $budgetCOA = $budget->getBudget($this->selectedCompany, $key, $this->tanggalBkk);
            if ($budgetCOA) {
                $isInBudget = $budget->isInBudget($budgetCOA[0]['budgetbulan'], $budgetCOA[0]['budgettahun'], collect($value)->sum('jumlah'));
            } else {
                $isInBudget = false;
            }

            if (!$isInBudget) {
                session()->flash('message_budget', 'Gagal membuat BKK! overbudget atau tidak ada budget untuk COA yang dipilih');
            }
        }

        return $isInBudget;
    }

    public function deleteKas($delete_id)
    {
        if (($key = array_search($delete_id, $this->selectedKasId)) !== false) {
            unset($this->selectedKasId[$key]);
        }

        $this->getSelectedKas();
        $this->render();
    }

    public function cekJumlahCoa()
    {
        if ($this->selectedKas->count() <= 5) {
            return true;
        } else {
            $this->selectedKas = $this->selectedKas->slice(0, 5);
            return false;
        }
    }

    public function hitungKasCOA()
    {
        $this->totalKasCoa = [];
        foreach ($this->selectedKas as $kas) {
            array_push($this->totalKasCoa, $kas->map(function ($item) {
                return [
                    'id' => $item->first()['id'],
                    'total_kas' => $item->sum('jumlah'),
                    'jumlah_data' => $item->count(),
                ];
            }));
        }
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

    public function createBKK()
    {
        // $statusCoa = $this->cekJumlahCoa();
        // $budgetCOA = $this->cekBudget();
        // if ($statusCoa && $budgetCOA) {
        //data bkk header
        $bkk_header_data = [
            'bank_id' => $this->selectedRekening,
            'name' => null,
            'tanggal' => $this->tanggalBkk,
            'partner' => $this->manualTypePartner ?? $this->selectedPartner,
            'otorisasi' => 0,
            'project_id' => $this->selectedProject,
            'layer_cashflow_id' => 0,
            'created_by' => Auth::user()->id,
            'created_at' => Carbon::now()->toDateTimeString(),
            'status' => 1,
        ];

        //data BKK
        $bkk_collection = collect();
        $this->selectedKas->map(function ($item) use (&$bkk_collection) {
            foreach ($item as $dataBkk) {
                $dataBkk = array_values($dataBkk);
                $bkk_collection->push([
                    'ppn' => 0,
                    'pph' => 0,
                    'coa_id' => $dataBkk[0]['coa']['coa_id'],
                    'pekerjaan' => "Pengeluaran " . $dataBkk[0]['coa']['name'] . " Unit " . ucwords(strtolower($dataBkk[0]['unit']['name'])),
                    'status_jurnal' => 0,
                    'status' => 0,
                    'otorisasi' => 0,
                    'payment' => collect($dataBkk)->sum('jumlah'),
                    'dpp' => collect($dataBkk)->sum('jumlah'),
                    'action' => "create",
                    'action_by' => Auth::user()->id,
                    'action_date' => Carbon::now()->format('Y-m-d'),
                    'layer_cashflow_id' => 0,
                    'using_budget' => "DEFAULT",
                    'unit_initial' => $dataBkk[0]['unit']['initial'],
                    'unit_id' => $dataBkk[0]['unit']['id']
                ]);
            }
        });
        //save
        $this->bkk = CreateBKKService::createBKK($bkk_header_data, $bkk_collection->toArray());

        if ($this->bkk) {
            collect($this->bkk["bkk_detail"])->map(function ($item) {
                Pengeluaran::where('coa', $item->coa_id)->whereIn('id', $this->selectedKasId)->update(['id_bkk' => $item->id, 'bkk_header_id' => $item->bkk_header_id]);
            });
            session()->flash('message_save', 'BKK berhasil dibuat');
        } else {
            session()->flash('message_not_save', 'BKK gagal dibuat');
        }
        // } else {
        // session()->flash('message_not_save', 'BKK gagal dibuat');
        // }
        $this->resetData();
        $this->render();
    }

    public function printBkk(): void
    {
        (new BKKController)->print($this->bkk['bkk_detail'], $this->bkk['bkk_header'], $this->selectedProject);
    }

    public function resetData(): void
    {
        $this->reset('selectedKas');
        $this->reset('selectedKasId');
        $this->emit('refresh');
    }
}
