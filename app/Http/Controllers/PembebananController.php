<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pembebanan;

class PembebananController extends Controller
{
    public function save(Request $request) 
    {
        $beban = new Pembebanan;
        $beban->nama_pembebanan = $request->pembebanan;

        $beban->save();

        return back();
    }
}
