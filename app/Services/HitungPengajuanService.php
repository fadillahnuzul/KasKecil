<?php

namespace App\Services;

use App\Models\Pengajuan;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

/**
 * Class HitungPengajuanService
 * @package App\Services
 */
class HitungPengajuanService
{
    public function hitung_pengajuan($id = null, $startDate = null, $endDate = null, $unit = null)
    {
        $data_saldo = Pengajuan::where(function ($query) {
            $query->statusProgressAndApproved()->orWhere('status', 9);
        })->noUsernameUser()->SearchByUser($id)->get();
        $admin = ($id) ? User::find($id) : User::find(Auth::user()->id);
        $total_pengajuan = $data_saldo->sum('jumlah');
        if ($admin && $admin->kk_access == 1) {
            $data_pengajuan_user = Pengajuan::where(function ($query) {
                $query->statusProgressAndApproved()->orWhere('status', 9);
            })->noUsernameUser()->where('user_id', '!=', $admin->id)->get();
            $pengajuan_user = $data_pengajuan_user->sum('jumlah');
            $total_pengajuan = $total_pengajuan - $pengajuan_user;
        }

        return ($total_pengajuan);
    }

    public function hitung_pengajuan_admin(): float
    {
        $data_saldo = Pengajuan::whereHas('User', function($query){
            $query->where('kk_access', 1);
        })->where(function ($query) {
            $query->statusProgressAndApproved()->orWhere('status', 9);
        })->noUsernameUser()->get();
        $total_pengajuan = $data_saldo->sum('jumlah') - $this->hitung_pengajuan_all_user();

        return ($total_pengajuan);
    }

    public function hitung_pengajuan_all_user(): float
    {
        $data_saldo = Pengajuan::whereHas('User', function($query){
            $query->where('kk_access', 2);
        })->where(function ($query) {
            $query->statusProgressAndApproved()->orWhere('status', 9);
        })->noUsernameUser()->get();
        $total_pengajuan = $data_saldo->sum('jumlah');

        return ($total_pengajuan);
    }
}
