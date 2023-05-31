<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BKKHeader;

class BKKHeaderController extends Controller
{
    public function store($bkk_header_data)
    {
        $bkk_header = BKKHeader::create($bkk_header_data);
        return $bkk_header;
    }
}
