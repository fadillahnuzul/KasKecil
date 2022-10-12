<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Pengajuan;
use App\Models\Pengeluaran;
use App\Models\Kategori;
use App\Models\Pembebanan;
use Alert;

class PengeluaranController extends Controller
{
    public function index(Request $request, $id)
    {
        $button_kas = TRUE;
        $title = "Detail Pengajuan";
        $kas = Pengeluaran::with('Pembebanan', 'Status', 'Kategori')->where('pemasukan','=',$id)->get();
        session(['key' => $id]);
        $total = $kas->sum('jumlah');
        
        return view ('detail_pengajuan', ['dataKas' => $kas],['title' => $title, 'button_kas'=>$button_kas]);
    }

    public function laporan()
    {
        $button_kas = FALSE;
        $divisi = Auth::user()->id;
        $data_pengeluaran = Pengeluaran::with('pengajuan', 'Status', 'Kategori', 'Pembebanan')->where('divisi_id', $divisi)->where('status', 5)->get();
        $title = "Laporan Kas Kecil";

        return view ('detail_pengajuan', ['dataKas' => $data_pengeluaran],['title' => $title, 'button_kas'=>$button_kas]);
    }

    public function create()
    {
        $kategori = Kategori::get();
        $pembebanan = Pembebanan::get();
        
        return view('form_kas', ['kategori'=> $kategori, 'pembebanan' => $pembebanan]);
    }

    public function save(Request $request)
    {
        $kas = new Pengeluaran;
        $kas->tanggal = $request->tanggal;
        $kas->deskripsi = $request->deskripsi;
        $kas->kategori = $request->kategori;
        $kas->pembebanan = $request->pembebanan;
        $kas->status = "4";
        $kas->pemasukan = $request->session()->get('key');
        $kas->divisi_id = Auth::user()->id;

        if (Auth::user()->role_id == 1) {
            $tunai = $request->tunai;
            $bank = $request->bank;
            $kas->jumlah = $tunai + $bank;
            $saldo = Auth::user()->saldo;
            if ($kas->jumlah > $saldo) {
                Alert::error('Input kas gagal', 'Maaf, saldo tidak cukup');
                return back();
            } else {
                $saldo_akhir = $saldo - $kas->jumlah;
                Auth::user()->saldo = $saldo_akhir;
                Auth::user()->save();
                $kas->save();

                #mengurangi saldo tunai dan bank
                $lastInsertedId = $kas->id;
                $pengeluaran = Pengeluaran::with('pengajuan')->find($lastInsertedId);
                $pengeluaran->pengajuan->tunai = $pengeluaran->pengajuan->tunai - $tunai;
                $pengeluaran->pengajuan->bank = $pengeluaran->pengajuan->bank - $bank;
                $pengeluaran->pengajuan->save();
            }
        } else {
            $kas->jumlah = $request->kredit;
            $saldo = Auth::user()->saldo;
            if ($kas->jumlah > $saldo) {
                Alert::error('Input kas gagal', 'Maaf, saldo tidak cukup');
                return back();
            } else {
                $saldo_akhir = $saldo - $kas->jumlah;
                Auth::user()->saldo = $saldo_akhir;
                Auth::user()->save();
                $kas->save();
            }

        }
        return redirect ('home');
    }

    public function edit(Request $request, $id)
    {
        $kas = Pengeluaran::with('pengajuan')->findOrFail($id);

        return view('form-edit', ['kas' => $kas]);
    }

    public function update(Request $request, $id)
    {
        $kas = Pengeluaran::with('pengajuan', 'Divisi')->findOrFail($id);

        $kas_awal = $kas->jumlah;
        $kas->tanggal = $request->tanggal;
        $kas->deskripsi = $request->deskripsi;
        $kas->jumlah = $request->jumlah;


        $saldo_awal = $kas->Divisi->saldo;
        $saldo_akhir = $saldo_awal + $kas_awal - $kas->jumlah;
        $kas->Divisi->saldo = $saldo_akhir;

        $kas->Divisi->save();
        $kas->save();

        return redirect ('home');
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
        $pengeluaran->status = "4";
        $pengeluaran->pengajuan->status = "4";
        $pengeluaran->tanggal_respon = $request->tanggal;
        

        $pengeluaran->pengajuan->save();
        $pengeluaran->save();


        
        return back();
    }
}
