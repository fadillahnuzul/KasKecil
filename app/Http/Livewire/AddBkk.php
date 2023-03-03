<?php

namespace App\Http\Livewire;

use App\Models\Coa;
use Livewire\Component;
use App\Models\Pengeluaran;
use Carbon\Carbon;

class AddBkk extends Component
{
    public $kas;
    public $startDate;
    public $endDate;
    public $Coa;

    public function __construct()
    {
        // $this->startDate = Carbon::now()->startOfYear('d-m-Y');
        // $this->endDate = Carbon::now()->endOfYear('d-m-Y');
    }
    public function render()
    {
        return view('livewire.add-bkk');
    }

    public function mount()
    {
        $this->kas = Pengeluaran::with('COA')->where('status',7)->bukanPengembalianSaldo()->searchByDateRange($this->startDate, $this->endDate)->searchByCoa($this->Coa)->get();
        $this->Coa = Coa::where('status','!=',0)->get();
    }
}
