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
    public $coaList;
    public $companyList;
    public $partnerList;
    public $selectedCompany;
    public $selectedProject;
    public $selectedRekening;
    public $selectedCoaId;
    public $selectedPartner;
    public $tanggalBkk;
    public $selectedKasId = [];
    public $selectedAll = FALSE;
    public $firstId;
    public $selectedKas;
    public $totalKas;
    public $totalKasCoa;

    public function mount()
    {
        $this->companyList = Company::get();
        $this->coaList = Coa::where('status', '!=', 0)->get();
        $this->partnerList = Partner::get();
        $this->startDate = Carbon::now()->startOfMonth()->format('Y-m-d');
        $this->endDate = Carbon::now()->endOfMonth()->format('Y-m-d');
    }

    public function render()
    {
        $projectList = Project::where('project_company_id', $this->selectedCompany)->get();
        $rekeningList = Rekening::where('company_id', $this->selectedCompany)->get();

        $kas = Pengeluaran::with('COA', 'project')->where('status', 8)->bukanPengembalianSaldo()->searchByCoa($this->selectedCoaId)
            ->searchByDateRange($this->startDate, $this->endDate)
            ->searchByCompany($this->selectedCompany)
            // ->searchByProject($this->selectedProject)
            ->paginate(10);

        return view('livewire.add-bkk', ['kas' => $kas, 'projectList' => $projectList, 'rekeningList' => $rekeningList]);
    }

    public function getSelectedKas()
    {
        $this->selectedKas = Pengeluaran::with('coa')->whereIn('id', $this->selectedKasId)->get();
        foreach($this->selectedKas as $item) {
            if ($item->in_budget==1) {
                $this->selectedKas = $this->selectedKas->where('in_budget','!=',1);
                session()->flash('message_overbudget', 'Kas overbudget tidak ditambahkan');
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
            $isInBudget = $budget->isInBudget($budgetCOA[0]['budgetbulan'], $budgetCOA[0]['budgettahun'], collect($value)->sum('jumlah'));
            if ($isInBudget == false) {
                session()->flash('message_budget', 'Overbudget COA');
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

    public function createBKK()
    {
        $statusCoa = $this->cekJumlahCoa();
        $budgetCOA = $this->cekBudget();
        if ($statusCoa && $budgetCOA) {
            //data bkk header
            $bkk_header_data = [
                'bank_id' => $this->selectedRekening,
                'name' => null,
                'tanggal' => $this->tanggalBkk,
                'partner' => $this->selectedPartner,
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
                Pengeluaran::where('coa', $item->coa_id)->whereIn('id', $this->selectedKasId)->update(['id_bkk' => $item->id]);
            });
            session()->flash('message_save', 'BKK berhasil dibuat');
        } else {
            session()->flash('message_not_save', 'BKK gagal dibuat');
        }
        $this->render();
    }
}