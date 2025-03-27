<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;

/**
 * Class HitungSaldoService
 * @package App\Services
 */
class HitungSaldoService
{
    public function hitung_saldo_user(int $id=null, int $company=null) : float {
        $transaksi = new HitungTransaksiService;
        $id = ($id) ? $id : Auth::user()->id;
        $saldo = (new HitungPengajuanService)->hitung_pengajuan($id, null, null, null, $company);
        $kas = $transaksi->hitung_belum_klaim($id) + $transaksi->hitung_klaim($id);
        $saldo = $saldo - $kas;
        return $saldo;
    }

    public function hitung_saldo_all_user(int $company=null) : float {
        $transaksi = new HitungTransaksiService;
        $saldo = (new HitungPengajuanService)->hitung_pengajuan(null,null,null,null, $company);
        $kas = $transaksi->hitung_belum_klaim() + $transaksi->hitung_klaim();
        $saldo = $saldo - $kas;
        return $saldo;
    }

}
