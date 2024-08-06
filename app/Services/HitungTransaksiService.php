<?php

namespace App\Services;

use App\Models\Pengeluaran;
use App\Models\User;

/**
 * Class HitungTransaksiService
 * @package App\Services
 */
class HitungTransaksiService
{
    public function hitung_belum_klaim($id = null, $startDate = null, $endDate = null, $company = null, $unit = null)
    {
        $user = ($id) ? User::find($id) : null;

        $data_pengeluaran_user = collect();

        if ($id) {
            $data_pengeluaran = Pengeluaran::bukanPengembalianSaldo()
                ->searchByDateRange($startDate, $endDate)
                ->searchByCompany($company)
                ->searchByUnit($unit)
                ->notDisabled()
                ->searchByUser($id)
                ->statusProgress()
                // ->where(function ($query) use ($user) {
                //     ($user->kk_access == 1) ? $query->statusProgressAndKlaim() : $query->statusProgress();
                // })
                ->get();
            if (count($data_pengeluaran) > 0) {
                $data_pengeluaran->map(function ($item) use (&$data_pengeluaran_user) {
                    $data_pengeluaran_user->push($item);
                });
            }
        } else {
            $listUserId = Pengeluaran::with('user')->getUserId()->get();
            foreach ($listUserId as $user) {
                $data_pengeluaran = (new Pengeluaran())->bukanPengembalianSaldo()
                    ->searchByDateRange($startDate, $endDate)
                    ->searchByCompany($company)
                    ->searchByUnit($unit)
                    ->notDisabled()
                    ->searchByUser($user->user->id)
                    ->statusProgress()
                    // ->where(function ($query) use ($user) {
                    //     ($user->user->kk_access == 1) ? $query->statusProgressAndKlaim() : $query->statusProgress();
                    // })
                    ->get();
                if (count($data_pengeluaran) > 0) {
                    $data_pengeluaran->map(function ($item) use (&$data_pengeluaran_user) {
                        $data_pengeluaran_user->push($item);
                    });
                }
            }
        }
        $total_pengeluaran = 0;
        foreach ($data_pengeluaran_user as $keluar) {
            $total_pengeluaran = $total_pengeluaran + $keluar->jumlah;
        }

        return ($total_pengeluaran);
    }

    public function hitung_klaim($id = null, $startDate = null, $endDate = null, $company = null, $unit = null)
    {
        $user = ($id) ? User::find($id) : null;

        $data_klaim_user = collect();

        if ($id) {
            $data_pengeluaran = Pengeluaran::bukanPengembalianSaldo()
                ->searchByDateRange($startDate, $endDate)
                ->searchByCompany($company)
                ->searchByUnit($unit)
                ->notDisabled()
                ->searchByUser($id)
                ->where(function ($query) {
                    $query->statusKlaim()->orWhere(function ($query) {
                        $query->whereIn('status', [8, 9])->whereHas('bkkHeader', function ($q) {
                            $q->where('tanggal', '<', '2021-01-01')->where('status','=',1)->whereNull('flag_sudah_rekon')->whereNull('tanggal_rekon');
                        });
                    });
                })
                ->get();
            if (count($data_pengeluaran) > 0) {
                $data_pengeluaran->map(function ($item) use (&$data_klaim_user) {
                    $data_klaim_user->push($item);
                });
            }
        } else {
            $listUserId = Pengeluaran::with('user')->getUserId()->get();
            foreach ($listUserId as $user) {
                $data_pengeluaran = (new Pengeluaran())->bukanPengembalianSaldo()
                    ->searchByDateRange($startDate, $endDate)
                    ->searchByCompany($company)
                    ->searchByUnit($unit)
                    ->notDisabled()
                    ->searchByUser($user->user->id)
                    ->where(function ($query) {
                        $query->statusKlaim()->orWhere(function ($query) {
                            $query->whereIn('status', [8, 9])->whereHas('bkkHeader', function ($q) {
                                $q->where('tanggal', '<', '2021-01-01')->where('status','=',1)->whereNull('flag_sudah_rekon')->whereNull('tanggal_rekon');
                            });
                        });
                    })
                    ->get();
                if (count($data_pengeluaran) > 0) {
                    $data_pengeluaran->map(function ($item) use (&$data_klaim_user) {
                        $data_klaim_user->push($item);
                    });
                }
            }
        }

        $total_diklaim = 0;
        foreach ($data_klaim_user as $keluar) {
            $total_diklaim = $total_diklaim + $keluar->jumlah;
        }

        return ($total_diklaim);
    }
}
