<?php

namespace App\Services;

use App\Http\Controllers\BKMController;
use App\Http\Controllers\BKMHeaderController;

/**
 * Class CreateBKMService
 * @package App\Services
 */
class CreateBKMService
{
    public static function CreateBkmApi($header, $detail)
    {
        $bkm_header = (new BKMHeaderController)->store($header);
        foreach ($detail as $key => $detail_bkm) {
            $detail[$key]["bkm_header_id"] = $bkm_header["id"];
        }
        $bkm_detail = (new BKMController)->store($detail);
        $bkm_header["detail"] = $bkm_detail;
        return $bkm_header;
    }
}
