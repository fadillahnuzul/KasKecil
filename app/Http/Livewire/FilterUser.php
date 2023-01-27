<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Http\Controllers\AdminController;
use App\Models\User;
use App\Models\Pengeluaran;
use Illuminate\Support\Facades\DB;


class FilterUser extends Component
{
    public $user=null;
    public $userList;
    public function render()
    {
        return view('livewire.filter-user');
    }
    public function __construct()
    {
        $this->userList = User::join('pettycash_pengeluaran', 'user.id', '=', 'pettycash_pengeluaran.user_id')->get();
        
    }

    public function filterUser()
    {
        $dataKas = Pengeluaran::where('user_id',$this->user)->get();
        (new AdminController)->index($dataKas);
    }

}
