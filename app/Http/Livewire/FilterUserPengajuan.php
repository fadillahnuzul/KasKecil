<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Http\Controllers\AdminController;
use App\Models\User;
use App\Models\Pengajuan;
use Illuminate\Support\Facades\DB;

class FilterUserPengajuan extends Component
{
    public $user=null;
    public $userList;
    public function render()
    {
        return view('livewire.filter-user-pengajuan');
    }

    public function __construct()
    {
        $this->userList = DB::table('user')->join('pettycash_pengajuan', 'user.id', '=', 'pettycash_pengajuan.user_id')->get();
        $this->userList = collect($this->userList)->unique('id');
    }

    public function filterUser()
    {
        $dataKas = Pengajuan::with('Sumber','User','Status')->where('user_id',$this->user)->get();
        // (new AdminController)->index($dataKas);
    }
}
