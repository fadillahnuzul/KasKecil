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

        $kas = Pengeluaran::with('COA')->where('status', 8)->bukanPengembalianSaldo()->searchByCoa($this->selectedCoaId)->searchByDateRange($this->startDate, $this->endDate)->paginate(10);

        return view('livewire.add-bkk', ['kas' => $kas, 'projectList' => $projectList, 'rekeningList' => $rekeningList]);
    }

    public function getSelectedKas()
    {
        $this->selectedKas = Pengeluaran::with('COA')->whereIn('id', $this->selectedKasId)->get();
        $this->totalKas = $this->selectedKas->sum('jumlah');
        $this->selectedKas = $this->selectedKas->sortBy('coa')->groupBy('coa')->toBase();
        $statusCoa = $this->cekCoa();
        if ($statusCoa == false) {
            session()->flash('message_coa', 'Gagal menambahkan transaksi, COA yang dipilih lebih dari 5');
        }
        $this->hitungKasCOA();
    }

    public function cekCoa()
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
        $statusCoa = $this->cekCoa();

        if ($statusCoa) {
            //save bkk header
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
            $bkk_header_id = app('App\Http\Controllers\BKKHeaderController')->store($bkk_header_data);

            //send data BKK
            $bkk_collection = collect();
            foreach ($this->selectedKas as $item => $list_kas) {
                foreach ($list_kas as $row) {
                    $listKasCoa = Pengeluaran::where('coa', $row['coa'])->whereIn('id', $this->selectedKasId)->get();
                    $coa = COA::find($row['coa']);
                    $bkk_collection->push(
                        BKK::make([
                            'bkk_header_id' => $bkk_header_id,
                            'ppn' => 0,
                            'pph' => 0,
                            'coa_id' => $coa->coa_id,
                            'pekerjaan' => "Pengeluaran " . $coa->name,
                            'status_jurnal' => 0,
                            'status' => 0,
                            'otorisasi' => 0,
                            'payment' => $listKasCoa->sum('jumlah'),
                            'dpp' => $listKasCoa->sum('jumlah'),
                            'action' => "create",
                            'action_by' => Auth::user()->id,
                            'action_date' => Carbon::now()->format('Y-m-d'),
                            'layer_cashflow_id' => 0,
                            'using_budget' => "DEFAULT",
                        ])
                    );
                    //input id bkk header dan id bkk
                    // Pengeluaran::where('coa', $coa->coa_id)->whereIn('id', $this->selectedKasId)->update(['id_bkk' => $bkk->id]);
                }
            }
            //store
            $id_bkk = (new BKKController)->store($bkk_collection);
            Pengeluaran::where('coa', $coa->coa_id)->whereIn('id', $this->selectedKasId)->update(['id_bkk' => $bkk->id]);
            session()->flash('message_save', 'BKK berhasil dibuat');
        }
        $this->render();
    }
}
