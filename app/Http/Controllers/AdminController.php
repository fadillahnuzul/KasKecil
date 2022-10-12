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
use Illuminate\Support\Facades\Auth;
use Alert;

class AdminController extends Controller
{
    public function index(){
        $laporan = FALSE;
        $data_kas = Pengajuan::with('Sumber','Divisi', 'Status')->where('status','!=',5)->get();
        $divisi = Divisi::where('role_id', '!=', '1')->get();
        $title = "Admin Kas Kecil";
       
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

        $pengajuan_admin = Pengajuan::with('Status')->where('divisi_id', 1)->where('status', 2)->orWhere('status', '4')->get();
        $admin = $pengajuan_admin->last();

        return view('admin/main', ['dataKas' => $data_kas, 'admin'=>$admin], ['divisi' => $divisi, 'title' => $title, 'laporan' => $laporan]);
    }

    public function laporan(){
        $laporan = TRUE;
        $dataKas = Pengajuan::with('Sumber','Divisi', 'Status')->where('status',5)->get();
        $divisi = Divisi::where('role_id', '!=', '1')->get();
        $data_pengajuan = Pengajuan::where('divisi_id','!=', 1)->where('status','!=', 1)->where('status','!=', 3)->get();
        $data_pengeluaran = Pengeluaran::where('divisi_id','!=', 1)->where('status','!=', 1)->where('status','!=', 3)->get();
        // Perhitungan sisa dan total belanja
        $total_masuk = 0;
        foreach ($data_pengajuan as $masuk){
            $total_masuk = $total_masuk + $masuk->jumlah;
        }
        $total_pengajuan = $total_masuk;

        $total_keluar = 0;
        foreach ($data_pengeluaran as $keluar){
            $total_keluar = $total_keluar + $keluar->jumlah;
        }
        $total_pengeluaran = $total_keluar;

        $sisa = $total_pengajuan - $total_pengeluaran;

        $title = "Laporan Pengajuan";

        $pengajuan_admin = Pengajuan::with('Status')->where('divisi_id', 1)->where('status', 2)->orWhere('status', '4')->get();
        $admin = $pengajuan_admin->last();


        return view('admin/main', compact('dataKas', 'admin', 'divisi', 'title', 'laporan','total_pengajuan','total_pengeluaran','sisa'));
    }

    public function laporan_keluar()
    {
        $data_pengeluaran = Pengeluaran::with('pengajuan', 'Status', 'kategori')->where('status', 5)->get();
        $title = "Laporan Kas Kecil";
        $kategori = Kategori::with('pengeluaran')->get();

        return view ('/admin/laporan_kas', ['kategori' => $kategori, 'title' => $title], ['dataKas' => $data_pengeluaran]);
    }

    public function kategori($id)
    {
        $data_pengeluaran = Pengeluaran::with('pengajuan', 'Status', 'kategori')->where('status', 5)->where('kategori',$id)->get();
        $title = "Laporan Kas Kecil";
        $kategori = Kategori::with('pengeluaran')->get();

        return view ('/admin/laporan_kas', ['kategori' => $kategori, 'title' => $title], ['dataKas' => $data_pengeluaran]);
    }

    public function acc(Request $request, $id)
    {
        $edit = FALSE;
        $sumber = Sumber::select('id', 'sumber_dana')->get();
        $pengajuan = Pengajuan::with('sumber', 'Divisi')->findOrFail($id);

        return view('admin/form-edit', ['pengajuan' => $pengajuan], ['sumber' => $sumber, 'edit' => $edit]);
    }

    public function edit(Request $request, $id)
    {
        $edit = TRUE;
        $sumber = Sumber::select('id', 'sumber_dana')->get();
        $pengajuan = Pengajuan::with('sumber', 'Divisi')->findOrFail($id);

        return view('admin/form-edit', ['pengajuan' => $pengajuan], ['sumber' => $sumber, 'edit' => $edit]);
    }

    public function update(Request $request, $id)
    {
        $pengajuan = Pengajuan::with('Divisi')->findOrFail($id);
        $pengajuan_admin = Pengajuan::with('Status')->where('divisi_id', 1)->where('status', 2)->orWhere('status', '4')->get();
        $admin = $pengajuan_admin->last();

        $pengajuan->tanggal = $request->tanggal;
        $pengajuan->deskripsi = $request->deskripsi;
        $pengajuan->tanggal = $request->tanggal;
        $pengajuan->jumlah = $request->jumlah;
        $pengajuan->sumber = $request->sumber;     

        if ($pengajuan->Divisi->role_id == 1) {
            $tunai_awal = $pengajuan->tunai;
            $bank_awal = $pengajuan->bank;
            $pengajuan->tunai = $request->tunai;
            $pengajuan->bank = $request->bank;
            $pengajuan->Divisi->saldo = $pengajuan->Divisi->saldo - ($tunai_awal+$bank_awal) + ($pengajuan->tunai+$pengajuan->bank);
        } else {
            $saldo = Auth::user()->saldo;
            if ($pengajuan->jumlah > $saldo){
                Alert::error('Approve gagal', 'Maaf, saldo admin tidak cukup');
                return back(); 
            } else {
                $saldo_awal = $pengajuan->Divisi->saldo;
                $saldo_akhir = $saldo_awal + $request->jumlah;
                $pengajuan->Divisi->saldo = $saldo_akhir;
                if ($pengajuan->sumber == 1) {
                    if ($pengajuan->jumlah > $admin->tunai) {
                        Alert::error('Approve gagal', 'Maaf, saldo tunai tidak cukup');
                        return back();
                    } else {
                        $tunai_awal = $admin->tunai;
                        $admin->tunai = $admin->tunai + $tunai_awal - $pengajuan->jumlah;
                        Auth::user()->saldo = Auth::user()->saldo + $tunai_awal - $pengajuan->jumlah;
                    }
                } elseif ($pengajuan->sumber == 2) {
                    if ($pengajuan->jumlah > $admin->bank) {
                        Alert::error('Approve gagal', 'Maaf, saldo bank tidak cukup');
                        return back();
                    } else {
                        $bank_awal = $admin->bank;
                        $admin->bank = $admin->bank + $bank_awal - $pengajuan->jumlah;
                        Auth::user()->saldo = Auth::user()->saldo + $bank_awal - $pengajuan->jumlah;
                    }
                }
                $admin->save();
                Auth::user()->save();
            }
        }
        
        $pengajuan->Divisi->save();
        $pengajuan->save();

        return redirect('home_admin');
    }

    public function setujui(Request $request, $id)
    {
        $pengajuan = Pengajuan::with('Divisi')->findOrFail($id);
        $pengajuan_admin = Pengajuan::with('Status')->where('divisi_id', 1)->where('status', 2)->orWhere('status', '4')->get();
        $admin = $pengajuan_admin->last();

        $pengajuan->tanggal = $request->tanggal;
        $pengajuan->deskripsi = $request->deskripsi;
        $pengajuan->tanggal = $request->tanggal;
        $pengajuan->jumlah = $request->jumlah;
        $pengajuan->sumber = $request->sumber;
        $pengajuan->status = "2";        

        if ($pengajuan->Divisi->role_id == 1) {
            $pengajuan->tunai = $request->tunai;
            $pengajuan->bank = $request->bank;
            $pengajuan->Divisi->saldo = $pengajuan->tunai + $pengajuan->bank;
        } else {
            $saldo = Auth::user()->saldo;
            if ($pengajuan->jumlah > $saldo){
                Alert::error('Approve gagal', 'Maaf, saldo admin tidak cukup');
                return back();
            } else {
                $saldo_awal = $pengajuan->Divisi->saldo;
                $saldo_akhir = $saldo_awal + $request->jumlah;
                $pengajuan->Divisi->saldo = $saldo_akhir;
                Auth::user()->saldo = Auth::user()->saldo - $pengajuan->jumlah;
                if ($pengajuan->sumber == 1) {
                    if ($pengajuan->jumlah > $admin->tunai) {
                        Alert::error('Approve gagal', 'Maaf, saldo tunai tidak cukup');
                        return back();
                    } else {
                        $admin->tunai = $admin->tunai - $pengajuan->jumlah;
                    }
                } elseif ($pengajuan->sumber == 2) {
                    if ($pengajuan->jumlah > $admin->bank) {
                        Alert::error('Approve gagal', 'Maaf, saldo bank tidak cukup');
                        return back();
                    } else {
                        $admin->bank = $admin->bank - $pengajuan->jumlah;
                    }
                }
                $admin->save();
                Auth::user()->save();
            }
        }
        
        $pengajuan->Divisi->save();
        $pengajuan->save();

        return redirect('home_admin');
    }

    public function tolak(Request $request, $id)
    {
        $pengajuan = Pengajuan::findOrFail($id);

        $pengajuan->sumber = NULL;
        $pengajuan->status = "3";

        $pengajuan->save();

        return redirect('home_admin');
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
        } else {
            $pengeluaran->pengajuan->status = 4;
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
        $pengajuan = Pengajuan::find($id);
        $kas = Pengeluaran::with('pengajuan', 'Status')->where('pemasukan','=',$id)->get();
        session(['key' => $id]);
        
        return view ('admin/detail_pengajuan', ['dataKas' => $kas], ['pengajuan' => $pengajuan]);
    }

    public function edit_done($id)
    {
        $edit = FALSE;
        $pengeluaran = Pengeluaran::with('pengajuan')->findOrFail($id);

        return view('admin/form-edit-done', ['pengeluaran' => $pengeluaran, 'edit'=>$edit]);
    }

    public function simpan_done(Request $request, $id)
    {
        $pengeluaran = Pengeluaran::with('pengajuan')->findOrFail($id);

        $pengeluaran->tanggal = $request->tanggal;
        $pengeluaran->deskripsi = $request->deskripsi;
        $pengeluaran->jumlah = $request->jumlah;
        $pengeluaran->tanggal_respon = $request->tanggal_respon;    

        $pengeluaran->save();

        return redirect('home_admin');
    }

    public function batal_done($id)
    {
        $pengeluaran = Pengeluaran::with('pengajuan')->findOrFail($id);
        $pengeluaran->tanggal_respon = NULL;
        $pengeluaran->status = 4;

        $pengeluaran->save();

        $pengeluaran2 = Pengeluaran::with('pengajuan')->where('pemasukan',$pengeluaran->pemasukan)->get();
        $count_pengeluaran = $pengeluaran2->filter(function($item, $key){
            return $item->status == 5;
        });

        if (count($count_pengeluaran) == count($pengeluaran2)) {
            $pengeluaran->pengajuan->status = 5;
            $pengeluaran->pengajuan->save();
        } else {
            $pengeluaran->pengajuan->status = 4;
            $pengeluaran->pengajuan->save();
        }
        

        return back();
    }
}
