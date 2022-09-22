<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Fascades\Session;
use App\Models\Kas;
use App\Models\Rekening;
use App\Models\Divisi;
use Illuminate\Support\Facades\Auth;

class KasController extends Controller
{
    public function index_admin(){
        $divisi = Auth::user()->id;
        $data_kas = Kas::with('rekening')->get();
        return view ('main', ['dataKas' => $data_kas]);
    }

    public function index(){
        $divisi = Auth::user()->id;
        $data_kas = Kas::with('rekening')->where('divisi_id', $divisi)->get();
        return view ('main', ['dataKas' => $data_kas]);
    }

    public function create_masuk()
    {
        $rekening = Rekening::select('id', 'nama_rekening')->get();
        return view('form_pengajuan', ['rekening' => $rekening]);
    }

    public function create_keluar()
    {
        $rekening = Rekening::select('id', 'nama_rekening')->get();
        return view('form_kas', ['rekening' => $rekening]);
    }

    public function save(Request $request)
    {
        $kas = new Kas;
        $kas->tanggal = $request->tanggal;
        $kas->deskripsi = $request->deskripsi;
        $kas->tanggal = $request->tanggal;
        $kas->debit = $request->debit;
        $kas->kredit = $request->kredit;
        $kas->mutasi = $request->mutasi;

        $saldo = Auth::user()->saldo;
        $saldo_akhir = $saldo + $request->debit - $request->kredit;
        Auth::user()->saldo = $saldo_akhir;
        Auth::user()->save();


        $kas->riwayat_saldo = $saldo_akhir;
        $kas->divisi_id = Auth::user()->id;

        $kas->save();
        
        return redirect('home');
    }

    public function edit(Request $request, $id)
    {
        // $rekening = Rekening::select('id', 'nama_rekening')->get();
        $kas = Kas::with('rekening')->findOrFail($id);
        $rekening = Rekening::where('id', '!=', $kas->mutasi)->get(['id','nama_rekening']);

        return view('form-edit', ['kas' => $kas], ['rekening' => $rekening]);
    }

    public function update(Request $request, $id)
    {
        $kas = Kas::findOrFail($id);

        $kas->tanggal = $request->tanggal;
        $kas->deskripsi = $request->deskripsi;
        $kas->tanggal = $request->tanggal;
        $kas->debit = $request->debit;
        $kas->kredit = $request->kredit;
        $kas->mutasi = $request->mutasi;

        $saldo = Auth::user()->saldo;
        $saldo_akhir = $saldo + $request->debit - $request->kredit;
        Auth::user()->saldo = $saldo_akhir;
        Auth::user()->save();


        $kas->riwayat_saldo = $saldo_akhir;
        $kas->divisi_id = Auth::user()->id;

        return redirect('home');
    }

    public function delete($id)
    {
        $delete = Kas::findOrFail($id);
        $delete->delete();

        return redirect('home');
    }

    public function admin(){
        return view('admin/main');
    }
}
