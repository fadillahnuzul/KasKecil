<?php

namespace App\Http\Controllers;

use App\Models\BKM;
use Illuminate\Http\Request;

class BKMController extends Controller
{
    public function store($bkm_collection)
    {
        $bkm_detail_array = [];
        foreach($bkm_collection as $bkm_data) {
            $bkm_detail_data = BKM::create($bkm_data);
            array_push($bkm_detail_array, $bkm_detail_data);
        }
        return($bkm_detail_array);
    }
}
