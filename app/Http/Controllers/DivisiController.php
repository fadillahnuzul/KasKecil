<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Divisi;

class DivisiController extends Controller
{
    public function index(){
        $data_divisi = Divisi::all();
        dd($data_divisi);
    }
}
