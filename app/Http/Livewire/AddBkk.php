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
    public $selectedCompany;
    public $selectedProject;
    public $selectedRekening;
    public $selectedCoaId;
    public $selectedPartner;
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

    protected $listeners = ['getSelectedCoa' => 'getCoa'];

    public function mount()
    {
        $this->companyList = Company::get();
        $this->partnerList = Partner::get();
        $this->startDate = Carbon::now()->startOfMonth()->format('Y-m-d');
        $this->endDate = Carbon::now()->endOfMonth()->format('Y-m-d');
    }

    public function render()
    {
        $projectList = Project::where('project_company_id', $this->selectedCompany)->get();
        $rekeningList = Rekening::where('company_id', $this->selectedCompany)->get();
        $coaList = Coa::join('budget', function($q){
            $q->on('budget.kode_coa','=', 'coa.coa_id');
        })->searchCoa($this->searchCoa)->orderBy('code')->get()->unique('coa_id');
        if (!$this->selectedCoaExist && $coaList->first() && !$this->selectedCoaId) {
            $this->selectedCoaId = $coaList->first()->coa_id;
        }
        if (Auth::user()->kk_access==1) {
            $kas = Pengeluaran::with('COA', 'project')->where('status', 8)->bukanPengembalianSaldo()->searchByCoa($this->selectedCoaId)
            ->searchByDateRange($this->startDate, $this->endDate)
            ->searchByCompany($this->selectedCompany)
            // ->searchByProject($this->selectedProject)
            ->paginate(10);
        } elseif(Auth::user()->kk_access==2) {
            $kas = Pengeluaran::with('COA', 'project')->where('status', 8)->where('user_id',Auth::user()->id)->bukanPengembalianSaldo()->searchByCoa($this->selectedCoaId)
            ->searchByDateRange($this->startDate, $this->endDate)
            ->searchByCompany($this->selectedCompany)
            // ->searchByProject($this->selectedProject)
            ->paginate(10);
        }
        
        $this->selectedCoaExist = false;
        return view('livewire.add-bkk', compact('kas','projectList','rekeningList','coaList'));
    }

    public function getCoa($coaId=null)
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
        foreach($this->selectedKas as $item) {
            $coa = Coa::find($item->coa);
            if (($coa->status_budget=='budget' && $item->in_budget==1)) {
                session()->flash('message_overbudget', 'COA overbudget. Ajukan penambahan budget.');
                $this->selectedKas = $this->selectedKas->filter(function($kas) use ($item){
                    if ($kas->id != $item->getKey()){
                        return $kas;
                    };
                });
            }
        }
        
        $this->totalKas = $this->selectedKas->sum('jumlah');
        $this->selectedKas = $this->selectedKas->sortBy('coa')->groupBy('coa')->toBase();
        $statusCoa = $this->cekJumlahCoa();
        if ($statusCoa == false) {
            session()->flash('message_coa', 'Gagal menambahkan transaksi, COA yang dipilih lebih dari 5');
        }
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
                $isInBudget=false;
            }
            
            if(!$isInBudget) {
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
        $this->totalKasCoa = $this->selectedKas->map(function ($group) {
            return [
                'id' => $group->first()['id'],
                'total_kas' => $group->sum('jumlah'),
                'jumlah_data' => $group->count(),
            ];
        });
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
                $bkk_collection->push([
                    'ppn' => 0,
                    'pph' => 0,
                    'coa_id' => $item[0]['coa']['coa_id'],
                    'pekerjaan' => "Pengeluaran " . $item[0]['coa']['name'],
                    'status_jurnal' => 0,
                    'status' => 0,
                    'otorisasi' => 0,
                    'payment' => collect($item)->sum('jumlah'),
                    'dpp' => collect($item)->sum('jumlah'),
                    'action' => "create",
                    'action_by' => Auth::user()->id,
                    'action_date' => Carbon::now()->format('Y-m-d'),
                    'layer_cashflow_id' => 0,
                    'using_budget' => "DEFAULT",
                ]);
            });
            //save
            $bkk = CreateBKKService::createBKK($bkk_header_data, $bkk_collection->toArray());
            collect($bkk["bkk_detail"])->map(function ($item) {
                Pengeluaran::where('coa', $item->coa_id)->whereIn('id', $this->selectedKasId)->update(['id_bkk' => $item->id, 'bkk_header_id' => $item->bkk_header_id]);
            });
            if ($bkk) {
                session()->flash('message_save', 'BKK berhasil dibuat');
            } else {
                session()->flash('message_not_save', 'BKK gagal dibuat');
            }
        // } else {
            // session()->flash('message_not_save', 'BKK gagal dibuat');
        // }
        $this->render();
    }
}
