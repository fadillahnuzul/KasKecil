<?php

namespace App\Http\Controllers;

use App\Models\BKMHeader;
use Illuminate\Http\Request;

class BKMHeaderController extends Controller
{
    public function store($bkm_header_data)
    {
        $bkm_header = BKMHeader::create($bkm_header_data);   
        return $bkm_header;
    }
}
