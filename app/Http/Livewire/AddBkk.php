<?php

namespace App\Http\Livewire;

use App\Models\Coa;
use Livewire\Component;
use App\Models\Pengeluaran;

class AddBkk extends Component
{
    public $kas;
    public $startDate;
    public $endDate;
    public $coa;
    public function render()
    {
        return view('livewire.add-bkk');
    }

    public function mount()
    {
        $this->kas = Pengeluaran::with('COA')->where('status',7)->bukanPengembalianSaldo()->searchByDateRange($this->startDate, $this->endDate)->searchByCoa($this->coa)->get();
        $this->coa = Coa
    }
}
