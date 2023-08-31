<?php

namespace App\Http\Livewire;

use App\Models\BKK;
use Livewire\Component;

class DetailBkk extends Component
{
    public $detailBkk;
    protected $listeners = ['showBkkDetail' => 'getDetailBkk'];

    public function render()
    {
        return view('livewire.detail-bkk');
    }

    public function getDetailBkk($bkk_header_id) {
        dd($bkk_header_id);
        $this->detailBkk = BKK::where('bkk_header_id', $bkk_header_id)->get();
    }
}
