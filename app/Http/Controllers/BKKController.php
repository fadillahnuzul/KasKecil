<?php

namespace App\Http\Controllers;

use App\Models\BKK;
use App\Models\Pengeluaran;
use App\Models\Coa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class BKKController extends Controller
{
    public function create()
    {
        $title = "Create BKK";
        return view('admin/create_bkk',compact('title'));
    }

    public function store($bkk_collection)
    {
        $bkk_detail_array = [];
        foreach($bkk_collection as $bkk_data) {
            $bkk_detail_data = BKK::create($bkk_data);
            array_push($bkk_detail_array, $bkk_detail_data);
        }
        return($bkk_detail_array);
    }
}
