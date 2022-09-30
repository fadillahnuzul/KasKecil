<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Pengajuan;
use App\Models\Pengeluaran;
use App\Models\Divisi;
use App\Models\Sumber;

class PengajuanController extends Controller
{
    public function index(){
        $divisi = Auth::user()->id;
        $data_pengajuan = Pengajuan::with('Status')->where('divisi_id', $divisi)->get();
        foreach ($data_pengajuan as $masuk) {
            $total = 0;
            $data_pengeluaran = Pengeluaran::with('pengajuan')->where('pemasukan','=',$masuk->id)->get();
            foreach ($data_pengeluaran as $keluar){
                $total = $total + $keluar->jumlah;
            }
            $masuk->total_belanja = $total;
            $masuk->sisa = $masuk->jumlah - $masuk->total_belanja;
        }

        return view ('main', ['dataKas' => $data_pengajuan]);
    }

    public function create()
    {
        // if (Auth::user()->saldo == 0) {
        //     return view('form_pengajuan');
        // } else {
        //     echo "<script>alert('Tidak bisa buat pengajuan. Silahkan selesaikan pengajuan sebelumnya'); 
        //     window.location='home';</script>";
        // }
        return view('form_pengajuan');
    }

    public function save(Request $request)
    {
        $pengajuan = new Pengajuan;
        $pengajuan->tanggal = $request->tanggal;
        $pengajuan->deskripsi = $request->deskripsi;
        $pengajuan->tanggal = $request->tanggal;
        $pengajuan->jumlah = $request->jumlah;
        $pengajuan->sumber = $request->sumber;
        $pengajuan->status = "1";
        $pengajuan->divisi_id = Auth::user()->id;

        $pengajuan->save();
        
        return redirect('home');
    }

    public function detail(Request $request, $id)
    {
        $data_pengajuan = [];
        $data_pengajuan = Pengajuan::with('Status')->findOrFail($id);
        return view ('detail_pengajuan', ['dataKas' => $data_pengajuan]);
    }
}
