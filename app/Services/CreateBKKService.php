<?php

namespace App\Services;

use App\Http\Controllers\BKKController;
use App\Http\Controllers\BKKHeaderController;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use PhpParser\Node\Expr\Cast\Array_;

/**
 * Class CreateBKKService
 * @package App\Services
 */
class CreateBKKService
{
    // public static function createBKKHeader(array $header)
    // {
    //     $bkk_header = (new BKKHeaderController)->store($header);
    //     return ($bkk_header);
    // }

    // public static function createBKK(array $detail)
    // {
    //     $bkk_detail = (new BKKController)->store($detail);
    //     return ($bkk_detail);
    // }

    public static function createBKK(array $header, array $detail)
    {
        $bkk_header = (new BKKHeaderController)->store($header);
        foreach ($detail as $key => $detail_bkk) {
            $detail_bkk[$key]["bkk_header_id"] = $bkk_header[0]["id"];
        }
        $bkk_detail = (new BKKController)->store($detail);
        return  ['bkk_header' => $bkk_header, 'bkk_detail' => $bkk_detail];
    }
}
