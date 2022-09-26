<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Pengajuan;
use App\Models\Pengeluaran;

class PengeluaranController extends Controller
{
    public function index(Request $request, $id)
    {
        $kas = Pengeluaran::with('pengajuan', 'Status')->where('pemasukan','=',$id)->get();
        session(['key' => $id]);
        $total = $kas->sum('jumlah');
        
        return view ('detail_pengajuan', ['dataKas' => $kas]);
    }

    public function create()
    {
        return view('form_kas');
    }

    public function save(Request $request)
    {
        $kas = new Pengeluaran;
        $kas->tanggal = $request->tanggal;
        $kas->deskripsi = $request->deskripsi;
        $kas->jumlah = $request->kredit;
        $kas->status = "4";
        $kas->pemasukan = $request->session()->get('key');
        $kas->divisi_id = Auth::user()->id;

        $saldo = Auth::user()->saldo;
        $saldo_akhir = $saldo - $request->kredit;
        Auth::user()->saldo = $saldo_akhir;
        Auth::user()->save();

        $kas->save();
        
        return back();
    }

    public function edit(Request $request, $id)
    {
        $kas = Pengeluaran::with('pengajuan')->findOrFail($id);

        return view('form-edit', ['kas' => $kas]);
    }

    public function update(Request $request, $id)
    {
        $kas = Pengeluaran::with('pengajuan')->findOrFail($id);

        $kas_awal = $kas->jumlah;
        $kas->tanggal = $request->tanggal;
        $kas->deskripsi = $request->deskripsi;
        $kas->jumlah = $request->jumlah;

        $saldo_awal = Auth::user()->saldo;
        $saldo_akhir = $saldo_awal + $kas_awal - $kas->jumlah;
        Auth::user()->saldo = $saldo_akhir;

        $kas->save();

        return back();
    }

    public function delete($id)
    {
        $delete = Pengeluaran::with('Divisi')->findOrFail($id);
        $saldo_awal = $delete->Divisi->saldo;
        $saldo_akhir = $saldo_awal + $delete->jumlah;
        $delete->Divisi->saldo = $saldo_akhir;
        
        $delete->Divisi->save();
        $delete->delete();
        return back();
    }

    public function done(Request $request)
    {
        $pengeluaran = Pengeluaran::with('pengajuan')->findOrFail($request->modal_id);
        $pengeluaran->status = "5";
        $pengeluaran->pengajuan->status = "4";
        $pengeluaran->tanggal_respon = $request->tanggal;

        $pengeluaran->pengajuan->save();
        $pengeluaran->save();
        
        return back();
    }
}
