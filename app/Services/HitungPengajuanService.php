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
    public function hitung_pengajuan($id = null, $startDate = null, $endDate = null, $unit = null, $company = null)
    {
        $data_saldo = Pengajuan::with('User')->where(function ($query) {
            $query->statusProgressAndApproved()->orWhere('status', 9);
        })->noUsernameUser()->SearchByUser($id)->searchByCompany($company)->get();
        $admin = ($data_saldo->first()) ? $data_saldo->first()->User : null;
        $total_pengajuan = $data_saldo->sum('jumlah');
        if ($admin && $admin->kk_access == 1) {
            $data_pengajuan_user = Pengajuan::where(function ($query) {
                $query->statusProgressAndApproved()->orWhere('status', 9);
            })->noUsernameUser()->where('user_id', '!=', $admin->id)->searchByCompany($company)->get();
            $pengajuan_user = $data_pengajuan_user->sum('jumlah');
            $total_pengajuan = $total_pengajuan - $pengajuan_user;
        }

        return ($total_pengajuan);
    }

    public function hitung_pengajuan_admin($company = null): float
    {
        $data_saldo = Pengajuan::with('User')->whereHas('User', function($query){
            $query->where('kk_access', 1);
        })->where(function ($query) {
            $query->statusProgressAndApproved()->orWhere('status', 9);
        })->noUsernameUser()->searchByCompany($company)->get();
        $total_pengajuan = $data_saldo->sum('jumlah') - $this->hitung_pengajuan_all_user();

        return ($total_pengajuan);
    }

    public function hitung_pengajuan_all_user($company = null): float
    {
        $data_saldo = Pengajuan::with('User')->whereHas('User', function($query){
            $query->where('kk_access', 2);
        })->where(function ($query) {
            $query->statusProgressAndApproved()->orWhere('status', 9);
        })->noUsernameUser()->searchByCompany($company)->get();
        $total_pengajuan = $data_saldo->sum('jumlah');

        return ($total_pengajuan);
    }
}
