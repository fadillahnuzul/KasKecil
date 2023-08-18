<?php

namespace App\Services;

use App\Http\Controllers\BKKController;
use App\Http\Controllers\BKKDetailSPKController;
use App\Http\Controllers\BKKHeaderController;
use App\Http\Controllers\BKKHeaderSPKController;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use PhpParser\Node\Expr\Cast\Array_;

/**
 * Class CreateBKKService
 * @package App\Services
 */
class CreateBKKService
{
    public static function createBKK(array $header, array $detail)
    {
        $bkk_header = (new BKKHeaderController)->store($header);
        foreach ($detail as $key => $detail_bkk) {
            $detail[$key]["bkk_header_id"] = $bkk_header["id"];
        }
        $bkk_detail = (new BKKController)->store($detail);
        return  ['bkk_header' => $bkk_header, 'bkk_detail' => $bkk_detail];
    }

    public static function createBkkApi(array $header, array $detail)
    {
        $bkk_header = (new BKKHeaderController)->store($header);
        foreach ($detail as $key => $detail_bkk) {
            $detail[$key]["bkk_header_id"] = $bkk_header["id"];
        }
        $bkk_detail = (new BKKController)->store($detail);
        $bkk_header["detail"] = $bkk_detail;
        return $bkk_header;
    }

    public static function createBkkSpkApi(array $header, array $detail)
    {
        $bkk_header = (new BKKHeaderSPKController)->store($header);
        foreach ($detail as $key => $detail_bkk) {
            $detail[$key]["bkk_header_id"] = $bkk_header["id"];
        }
        $bkk_detail = (new BKKDetailSPKController)->store($detail);
        $bkk_header["detail"] = $bkk_detail;
        return $bkk_header;
    }
}
