<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sumber;

class SumberController extends Controller
{
    public function save(Request $request) 
    {
        $sumber = new Sumber;
        $sumber->sumber_dana = $request->sumber;

        $sumber->save();

        return back();
    }
}
