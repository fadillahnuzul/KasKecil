<?php

namespace App\Http\Controllers;

use App\Models\BKKHeader_SPK;
use Illuminate\Http\Request;

class BKKHeaderSPKController extends Controller
{
    public function store($bkk_header_data)
    {
        $bkk_header = BKKHeader_SPK::create($bkk_header_data);
        return $bkk_header;
    }
}
