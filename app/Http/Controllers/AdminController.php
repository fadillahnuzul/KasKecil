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
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function index(){
        $data_kas = Pengajuan::with('Sumber','Divisi', 'Status')->get();
        $divisi = Divisi::where('role_id', '!=', '1')->get();
       
        // Status done otomatis
        // foreach ($data_kas as $masuk) {
        //     $data_pengeluaran = Pengeluaran::with('pengajuan')->where('pemasukan','=',$masuk->id)->get();
        //     if ($data_pengeluaran) {
        //         $status = TRUE;
        //         foreach ($data_pengeluaran as $keluar){
        //             if ($keluar->status != 5) {
        //                 $status = FALSE;
        //             }
        //         }
        //         if ($status == TRUE) {
        //             $masuk->status = "5";
        //             $masuk->save();
        //         }
        //     }
        // }

        // Perhitungan sisa dan total belanja
        foreach ($data_kas as $masuk) {
            $total = 0;
            $data_pengeluaran = Pengeluaran::with('pengajuan')->where('pemasukan','=',$masuk->id)->get();
            foreach ($data_pengeluaran as $keluar){
                $total = $total + $keluar->jumlah;
            }
            $masuk->total_belanja = $total;
            $masuk->sisa = $masuk->jumlah - $masuk->total_belanja;
        }

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
        $pengeluaran = Pengeluaran::with('Divisi', 'pengajuan')->findOrFail($id);
        $pengeluaran->status = 5;
        
        $pengeluaran->save();

        $pengeluaran2 = Pengeluaran::with('pengajuan')->where('pemasukan',$pengeluaran->pemasukan)->get();
        $count_pengeluaran = $pengeluaran2->filter(function($item, $key){
            return $item->status == 5;
        });

        if (count($count_pengeluaran) == count($pengeluaran2)) {
            $pengeluaran->pengajuan->status = 5;
            $pengeluaran->pengajuan->save();
        }
        

        return back();
    }

    public function kas_divisi($id)
    {
        $data_kas = Pengajuan::with('Sumber','Divisi', 'Status')->where('divisi_id', $id)->get();
        $divisi = Divisi::where('role_id', '!=', '1')->get();
        session(['key' => $id]);

        return view('admin/main', ['dataKas' => $data_kas], ['divisi' => $divisi]);
    }

    public function detail_divisi($id)
    {
        $kas = Pengeluaran::with('pengajuan', 'Status')->where('pemasukan','=',$id)->get();
        session(['key' => $id]);
        
        return view ('admin/detail_pengajuan', ['dataKas' => $kas]);
    }
}
