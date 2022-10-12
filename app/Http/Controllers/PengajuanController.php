<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Pengajuan;
use App\Models\Pengeluaran;
use App\Models\Divisi;
use App\Models\Sumber;
use Alert;

class PengajuanController extends Controller
{
    public function index(){
        $divisi = Auth::user()->id;
        $title = "Kas Kecil";
        $data_pengajuan = Pengajuan::with('Status')->where('divisi_id', $divisi)->where('status','!=','5')->get();
        foreach ($data_pengajuan as $masuk) {
            $total = 0;
            $data_pengeluaran = Pengeluaran::with('pengajuan')->where('pemasukan','=',$masuk->id)->get();
            foreach ($data_pengeluaran as $keluar){
                $total = $total + $keluar->jumlah;
            }
            $masuk->total_belanja = $total;
            $masuk->sisa = $masuk->jumlah - $masuk->total_belanja;
        }

        $pengajuan_admin = Pengajuan::with('Status')->where('divisi_id', 1)->where('status', 2)->orWhere('status', '4')->get();
        $admin = $pengajuan_admin->last();
        // dd($admin);

        return view ('main', ['dataKas' => $data_pengajuan, 'admin' => $admin],['title'=>$title]);
    }

    public function laporan(){
        $divisi = Auth::user()->id;
        $title = "Laporan Kas Kecil";
        $data_pengajuan = Pengajuan::with('Status')->where('divisi_id', $divisi)->where('status', 5)->get();
        foreach ($data_pengajuan as $masuk) {
            $total = 0;
            $data_pengeluaran = Pengeluaran::with('pengajuan')->where('pemasukan','=',$masuk->id)->get();
            foreach ($data_pengeluaran as $keluar){
                $total = $total + $keluar->jumlah;
            }
            $masuk->total_belanja = $total;
            $masuk->sisa = $masuk->jumlah - $masuk->total_belanja;
        }

        if ($divisi == 1) {
            return view ('admin/main', ['dataKas' => $data_pengajuan],['title'=>$title]);
        } else {
            return view ('main', ['dataKas' => $data_pengajuan],['title'=>$title]);
        }
        
    }

    public function create()
    {
        if (Auth::user()->saldo == 0) {
            return view('form_pengajuan');
        } else {
            Alert::error('Pengajuan gagal', 'Selesaikan pengajuan sebelumnya');
            return redirect('home');
        }
        return view('form_pengajuan');
    }

    public function save(Request $request)
    {
        $pengajuan = new Pengajuan;
        $pengajuan->tanggal = $request->tanggal;
        $pengajuan->deskripsi = $request->deskripsi;
        $pengajuan->tanggal = $request->tanggal;
        $pengajuan->sumber = $request->sumber;
        $pengajuan->status = "1";
        $pengajuan->divisi_id = Auth::user()->id;
        if ($pengajuan->divisi_id == 1) {
            $pengajuan->tunai = $request->tunai;
            $pengajuan->bank = $request->bank;
        } else {
            $pengajuan->jumlah = $request->jumlah;
        }

        $divisi = Divisi::find($pengajuan->divisi_id);
        $pengajuan->divisi = $divisi->nama_divisi;
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
