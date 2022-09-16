<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Rekening;

class RekeningController extends Controller
{
    public function index(){
        $data_rekening = Rekening::with('kas')->get();
        return view ('lihat_rekening', ['dataRekening' => $data_rekening]);
    }

}
