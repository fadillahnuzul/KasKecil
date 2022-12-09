<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Fascades\Session;
use App\Models\Kas;
use App\Models\Rekening;
use App\Models\Divisi;
use App\Models\Pengajuan;
use App\Models\Pengeluaran;
use App\Models\Sumber;
use App\Models\Kategori;
use App\Models\Saldo;
use Illuminate\Support\Facades\Auth;
use Alert;
use Carbon\Carbon;

class BankController extends Controller
{
    public $startDate;
    public $endDate;

    public function __construct() {
        $this->startDate = Carbon::now()->startOfMonth();
        $this->endDate = Carbon::now()->endOfMonth();
    }

    public function index(Request $request){
        $saldoAwal = $request->session()->get('saldo_awal');
        $startDate = $this->startDate;
        $endDate = $this->endDate;
        $laporan = FALSE;
        $dataKas = Pengajuan::with('Sumber','User','Status')->where('status','!=',5)->get();
        $dataKas = $dataKas->filter(function($item, $key){
            return $item->User->access == 'admin';
        });
        $Saldo = Saldo::findOrFail(Auth::user()->id);
        $divisi = Divisi::get();
        $title = "Bank Kas Kecil";
       
        // Perhitungan sisa dan total belanja
        foreach ($dataKas as $masuk) {
            $total = 0;
            $diklaim = 0;
            $data_pengeluaran = Pengeluaran::with('pengajuan')->where('pemasukan','=',$masuk->id)->where('status','!=',6)->get();
            foreach ($data_pengeluaran as $keluar){
                $total = $total + $keluar->jumlah;
            }
            $data_diklaim = Pengeluaran::with('pengajuan')->where('pemasukan','=',$masuk->id)->where('status',7)->get();
            foreach ($data_diklaim as $keluar){
                $diklaim = $diklaim + $keluar->jumlah;
            }
            $masuk->total_belanja = $total;
            $masuk->diklaim = $diklaim;
            $masuk->sisa = $masuk->jumlah - $masuk->total_belanja;
        }

        // Perhitungan sisa dan total belanja pada card
        $pengajuan = Pengajuan::where('status','!=',3)->where('status','!=',6)->where('status','!=',1)->get();
        $data_pengajuan = $pengajuan->filter(function($item, $key){
            return $item->User->access != 'admin';
        });
        $pengeluaran = Pengeluaran::where('status','!=',3)->where('status','!=',6)->where('status','!=',1)->get();
        $data_pengeluaran = $pengeluaran->filter(function($item, $key){
            return $item->User->access != 'admin';
        });
        $total_pengajuan = 0;
        foreach ($data_pengajuan as $masuk){
            $total_pengajuan = $total_pengajuan + $masuk->jumlah;
        }
        $total_pengeluaran = 0;
        foreach ($data_pengeluaran as $keluar){
            $total_pengeluaran = $total_pengeluaran + $keluar->jumlah;
        }
        $sisa = $total_pengajuan - $total_pengeluaran;

        $pengajuan_admin = Pengajuan::with('Status')->where('divisi_id', 1)->where('status', 2)->orWhere('status', '4')->get();
        $admin = $pengajuan_admin->last();

        return view('bank/main', compact('dataKas','admin','Saldo','divisi','title','laporan','startDate','endDate','total_pengajuan','total_pengeluaran','sisa'));
    }

    public function laporan(Request $request){
        $saldoAwal = $request->session()->get('saldo_awal');
        $startDate = $this->startDate;
        $endDate = $this->endDate;
        $laporan = FALSE;
        $dataKas = Pengajuan::with('Sumber','User','Status')->where('status','==',5)->get();
        $Saldo = Saldo::findOrFail(Auth::user()->id);
        $divisi = Divisi::get();
        $title = "Bank Kas Kecil";
       
        // Perhitungan sisa dan total belanja
        foreach ($dataKas as $masuk) {
            $total = 0;
            $diklaim = 0;
            $data_pengeluaran = Pengeluaran::with('pengajuan')->where('pemasukan','=',$masuk->id)->where('status','!=',6)->get();
            foreach ($data_pengeluaran as $keluar){
                $total = $total + $keluar->jumlah;
            }
            $data_diklaim = Pengeluaran::with('pengajuan')->where('pemasukan','=',$masuk->id)->where('status',7)->get();
            foreach ($data_diklaim as $keluar){
                $diklaim = $diklaim + $keluar->jumlah;
            }
            $masuk->total_belanja = $total;
            $masuk->diklaim = $diklaim;
            $masuk->sisa = $masuk->jumlah - $masuk->total_belanja;
        }

        // Perhitungan sisa dan total belanja pada card
        $pengajuan = Pengajuan::where('status','!=',3)->where('status','!=',6)->where('status','!=',1)->get();
        $data_pengajuan = $pengajuan->filter(function($item, $key){
            return $item->User->access != 'admin';
        });
        $pengeluaran = Pengeluaran::where('status','!=',3)->where('status','!=',6)->where('status','!=',1)->get();
        $data_pengeluaran = $pengeluaran->filter(function($item, $key){
            return $item->User->access != 'admin';
        });
        $total_pengajuan = 0;
        foreach ($data_pengajuan as $masuk){
            $total_pengajuan = $total_pengajuan + $masuk->jumlah;
        }
        $total_pengeluaran = 0;
        foreach ($data_pengeluaran as $keluar){
            $total_pengeluaran = $total_pengeluaran + $keluar->jumlah;
        }
        $sisa = $total_pengajuan - $total_pengeluaran;

        $pengajuan_admin = Pengajuan::with('Status')->where('divisi_id', 1)->where('status', 2)->orWhere('status', '4')->get();
        $admin = $pengajuan_admin->last();

        return view('bank/main', compact('dataKas','admin','Saldo','divisi','title','laporan','startDate','endDate','total_pengajuan','total_pengeluaran','sisa'));
    }

    public function acc(Request $request, $id)
    {
        $title = "Form Persetujuan";
        $edit = FALSE;
        $pengajuan = Pengajuan::with('sumber', 'Divisi')->findOrFail($id);

        return view('bank/form-edit', ['pengajuan' => $pengajuan], ['edit' => $edit,'title'=>$title]);
    }

    public function edit(Request $request, $id)
    {
        $title = "Form Edit";
        $edit = TRUE;
        $pengajuan = Pengajuan::with('sumber', 'Divisi')->findOrFail($id);

        return view('bank/form-edit', ['pengajuan' => $pengajuan], ['edit' => $edit, 'title'=>$title]);
    }

    public function setujui(Request $request, $id)
    {
        $pengajuan = Pengajuan::with('User')->findOrFail($id);
        $saldo_user = Saldo::with('User')->findOrFail($pengajuan->user_id);
        $jumlah = preg_replace("/[^0-9]/","",$request->jumlah);   
        
        //menyimpan data
        $pengajuan->tanggal = $request->tanggal;
        $pengajuan->deskripsi = $request->deskripsi;
        $pengajuan->tanggal = $request->tanggal;
        $pengajuan->status = "2";        
        $pengajuan->jumlah = $jumlah;
        $saldo_user->saldo = $saldo_user->saldo + $pengajuan->jumlah;

        $saldo_user->save();
        $pengajuan->save();

        return redirect('home_bank');
    }

    public function update(Request $request, $id)
    {
        $pengajuan = Pengajuan::with('Divisi')->findOrFail($id);
        $saldo = Saldo::findOrFail($pengajuan->user_id);

        $pengajuan->tanggal = $request->tanggal;
        $pengajuan->deskripsi = $request->deskripsi;
        $pengajuan->tanggal = $request->tanggal;
        $pengajuan->sumber = $request->sumber;
        $jumlah = preg_replace("/[^0-9]/","",$request->jumlah);     
        //update tabel saldo
        $saldo_awal = $saldo->saldo;
        $saldo->saldo = $saldo->saldo - $saldo_awal + $jumlah;
        //update tabel pengajuan
        $pengajuan->jumlah = $jumlah;

        $pengajuan->save();
        $saldo->save();

        return redirect('home_bank');
    }

    public function laporan_keluar()
    {
        $data_pengeluaran = Pengeluaran::with('pengajuan', 'Status', 'kategori')->where('status', 5)->get();
        $title = "Laporan Kas Kecil";
        $kategori = Kategori::with('pengeluaran')->get();

        return view ('/bank/laporan_kas', ['kategori' => $kategori, 'title' => $title, 'startDate'=>$this->startDate, 'endDate'=>$this->endDate], 
        ['dataKas' => $data_pengeluaran]);
    }

    public function tolak(Request $request, $id)
    {
        $pengajuan = Pengajuan::findOrFail($id);
        $pengajuan->sumber = NULL;
        $pengajuan->status = "3";

        $pengajuan->save();

        return redirect('home_bank');
    }

    public function hapus($id)
    {
        $delete = Pengajuan::with('Divisi','User')->findOrFail($id);
        $saldo = Saldo::findOrFail($delete->user_id);
        //JIKA STATUSNYA BELUM DIAPPROVE ATAU DECLINE
        if ($delete->status != 1 AND $delete->status != 3) {
            $saldo->saldo = $saldo->saldo - $delete->jumlah;
            }
        $delete->status = 6;
        $saldo->save();
        $delete->save();
        
        return back();
    }
}
