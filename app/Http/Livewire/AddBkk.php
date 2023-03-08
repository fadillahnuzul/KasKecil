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
use Carbon\Carbon;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AddBkk extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $startDate;
    public $endDate;
    public $coaList;
    public $companyList;
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

    public function mount()
    {
        $this->companyList = Company::get();
        $this->coaList = Coa::where('status', '!=', 0)->get();
    }

    public function render()
    {
        $partnerList = DB::table('partner')->get();
        $projectList = Project::where('project_company_id', $this->selectedCompany)->get();
        $rekeningList = Rekening::where('company_id', $this->selectedCompany)->get();
        $kas = Pengeluaran::with('COA')->where('status', 7)->bukanPengembalianSaldo()->searchByDateRange($this->startDate, $this->endDate)->searchByCoa($this->selectedCoaId)->paginate(10);

        return view('livewire.add-bkk', ['kas' => $kas, 'projectList' => $projectList, 'rekeningList' => $rekeningList, 'partnerList' => $partnerList]);
    }

    public function getSelectedKas()
    {
        $this->selectedKas = Pengeluaran::whereIn('id', $this->selectedKasId)->get();
        $this->hitungTotalKas($this->selectedKas);
    }

    public function hitungTotalKas($kas)
    {
        foreach ($kas as $row) {
            $this->totalKas = $this->totalKas + $row->jumlah;
        }
    }

    public function createBKK()
    {
        $bkk_header = new BKKHeader;

        //save bkk header
        $bkk_header->bank_id = $this->selectedRekening;
        $bkk_header->name = null;
        $bkk_header->tanggal = $this->tanggalBkk;
        $bkk_header->partner = $this->selectedPartner;
        $bkk_header->otorisasi = 0;
        $bkk_header->project_id = $this->selectedProject;
        $bkk_header->layer_cashflow_id = 0;
        $bkk_header->created_by = Auth::user()->id;
        $bkk_header->created_at = Carbon::now()->toDateTimeString();
        $bkk_header->save();
        //save bkk
        foreach ($this->selectedKasId as $kas) {
            $bkk = new BKK;
            $coa = Coa::find($kas);
            $bkk->bkk_header_id = $bkk_header->id;
            $bkk->ppn = 0;
            $bkk->pph = 0;
            $bkk->coa_id = $kas;
            $bkk->pekerjaan = "Pengeluaran ".$coa->name;
            $bkk->status_jurnal = 0;
            $bkk->status = 0;
            $bkk->otorisasi = 0;
            $bkk->payment = $this->totalKas;
            $bkk->dpp = $this->totalKas;
            $bkk->action = "create";
            $bkk->action_by = Auth::user()->id;
            $bkk->action_date = Carbon::now()->format('Y-m-d');
            $bkk->layer_cashflow_id = 0;
            $bkk->using_budget = "DEFAULT";
            $bkk->save();
            dd($bkk);
        }
    }
}
