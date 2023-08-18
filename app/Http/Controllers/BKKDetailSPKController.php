<?php

namespace App\Http\Controllers;

use App\Models\BKK_SPK;
use Illuminate\Http\Request;

class BKKDetailSPKController extends Controller
{
    public function store($bkk_collection)
    {
        $bkk_detail_array = [];
        foreach($bkk_collection as $bkk_data) {
            $bkk_detail_data = BKK_SPK::create($bkk_data);
            array_push($bkk_detail_array, $bkk_detail_data);
        }
        return($bkk_detail_array);
    }
}
