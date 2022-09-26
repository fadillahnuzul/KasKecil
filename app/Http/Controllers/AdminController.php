<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Fascades\Session;
use App\Models\Kas;
use App\Models\Rekening;
use App\Models\Divisi;
use App\Models\Pengajuan;
use App\Models\Sumber;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function index(){
        $data_kas = Pengajuan::with('Sumber','Divisi', 'Status')->get();
        $divisi = Divisi::get();
    
        return view('admin/main', ['dataKas' => $data_kas], ['divisi' => $divisi]);
    }

    public function acc(Request $request, $id)
    {
        $sumber = Sumber::select('id', 'sumber_dana')->get();
        $pengajuan = Pengajuan::with('sumber')->findOrFail($id);

        return view('admin/form-edit', ['pengajuan' => $pengajuan], ['sumber' => $sumber]);
    }

    public function setujui(Request $request, $id)
    {
        $pengajuan = Pengajuan::with('Divisi')->findOrFail($id);

        $pengajuan->tanggal = $request->tanggal;
        $pengajuan->deskripsi = $request->deskripsi;
        $pengajuan->tanggal = $request->tanggal;
        $pengajuan->jumlah = $request->jumlah;
        $pengajuan->sumber = $request->sumber;
        $pengajuan->status = "2";        

        $saldo_awal = $pengajuan->Divisi->saldo;
        $saldo_akhir = $saldo_awal + $request->jumlah;
        $pengajuan->Divisi->saldo = $saldo_akhir;
        
        $pengajuan->Divisi->save();
        $pengajuan->save();

        return redirect('home/admin');
    }

    public function tolak(Request $request, $id)
    {
        $pengajuan = Pengajuan::findOrFail($id);

        $pengajuan->sumber = NULL;
        $pengajuan->status = "3";

        $pengajuan->save();

        return redirect('home/admin');
    }

    public function done($id)
    {
        $pengajuan = Pengajuan::with('Divisi')->findOrFail($id);
        $pengajuan->status = "5";
        $pengajuan->Divisi->saldo = 0;

        $pengajuan->Divisi->save();
        $pengajuan->save();
        
        return redirect('home/admin');
    }

    public function kas_divisi($id)
    {
        $data_kas = Pengajuan::with('Sumber','Divisi', 'Status')->where('divisi_id', $id)->get();
        $divisi = Divisi::get();
        session(['key' => $id]);

        return view('admin/kas_divisi', ['dataKas' => $data_kas], ['divisi' => $divisi]);
    }
}
