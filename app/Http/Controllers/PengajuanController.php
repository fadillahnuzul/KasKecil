<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Pengajuan;
use App\Models\Divisi;
use App\Models\Sumber;

class PengajuanController extends Controller
{
    public function index(){
        $divisi = Auth::user()->id;
        $data_pengajuan = Pengajuan::with('Status')->where('divisi_id', $divisi)->get();
        return view ('main', ['dataKas' => $data_pengajuan]);
    }

    public function create()
    {
        $sumber = Sumber::select('id', 'sumber_dana')->get();
        return view('form_pengajuan', ['sumber' => $sumber]);
    }

    public function save(Request $request)
    {
        $pengajuan = new Pengajuan;
        $pengajuan->tanggal = $request->tanggal;
        $pengajuan->deskripsi = $request->deskripsi;
        $pengajuan->tanggal = $request->tanggal;
        $pengajuan->jumlah = $request->jumlah;
        $pengajuan->sumber = $request->sumber;
        $pengajuan->status = "pending";

        // $saldo = Auth::user()->saldo;
        // $saldo_akhir = $saldo + $request->jumlah;
        // Auth::user()->saldo = $saldo_akhir;
        // Auth::user()->save();


        $pengajuan->riwayat_saldo = $saldo_akhir;
        $pengajuan->divisi_id = Auth::user()->id;

        $pengajuan->save();
        
        return redirect('home');
    }

    public function detail(Request $request, $id)
    {
        $divisi = Auth::user()->id;
        $data_pengajuan = [];
        $data_pengajuan = Pengajuan::with('sumber')->findOrFail($id);
        return view ('detail_pengajuan', ['dataKas' => $data_pengajuan]);
    }
}
