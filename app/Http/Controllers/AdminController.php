<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Fascades\Session;
use App\Models\Kas;
use App\Models\Rekening;
use App\Models\Divisi;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function index(){
        $data_kas = Kas::with('rekening')->get();
    
        return view('admin/main', ['dataKas' => $data_kas]);
    }
}
