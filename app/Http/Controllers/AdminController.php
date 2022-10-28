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

class AdminController extends Controller
{
    public $startDate;
    public $endDate;

    public function __construct() {
        $this->startDate = Carbon::now()->startOfMonth();
        $this->endDate = Carbon::now()->endOfMonth();
    }
    
    public function index(){
        $laporan = FALSE;
        $data_kas = Pengajuan::with('Sumber','User','Status')->where('status','!=',5)->get();
        $saldo = Saldo::findOrFail(Auth::user()->id);
        $divisi = Divisi::get();
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

        return view('admin/main', ['dataKas' => $data_kas, 'admin'=>$admin], ['Saldo'=>$saldo,'divisi' => $divisi, 'title' => $title, 'laporan' => $laporan, 'startDate'=>$this->startDate, 'endDate'=>$this->endDate]);
    }

    public function laporan(){
        $startDate = $this->startDate;
        $endDate = $this->endDate;
        $laporan = TRUE;
        $dataKas = Pengajuan::with('Sumber','User', 'Status')->where('status',5)->get();
        $divisi = Divisi::get();
        $data_pengajuan = Pengajuan::get();
        // dd($data_pengajuan);
        $data_pengeluaran = Pengeluaran::get();
        // Perhitungan sisa dan total belanja pada card
        $total_pengajuan = 0;
        foreach ($data_pengajuan as $masuk){
            $total_pengajuan = $total_pengajuan + $masuk->jumlah;
        }
        $total_pengeluaran = 0;
        foreach ($data_pengeluaran as $keluar){
            $total_pengeluaran = $total_pengeluaran + $keluar->jumlah;
        }
        $sisa = $total_pengajuan - $total_pengeluaran;

        // Perhitungan sisa dan total belanja pada card
        foreach ($dataKas as $masuk) {
            $total = 0;
            $data_pengeluaran = Pengeluaran::with('pengajuan')->where('pemasukan','=',$masuk->id)->get();
            foreach ($data_pengeluaran as $keluar){
                $total = $total + $keluar->jumlah;
            }
            $masuk->total_belanja = $total;
            $masuk->sisa = $masuk->jumlah - $masuk->total_belanja;
        }


        $title = "Laporan Pengajuan";

        $pengajuan_admin = Pengajuan::with('Status')->where('divisi_id', 1)->where('status', 2)->orWhere('status', '4')->get();
        $admin = $pengajuan_admin->last();


        return view('admin/main', compact('dataKas', 'admin', 'divisi', 'title', 'laporan','total_pengajuan','total_pengeluaran','sisa','startDate','endDate'));
    }

    public function laporan_keluar()
    {
        $data_pengeluaran = Pengeluaran::with('pengajuan', 'Status', 'kategori')->where('status', 5)->get();
        $title = "Laporan Kas Kecil";
        $kategori = Kategori::with('pengeluaran')->get();

        return view ('/admin/laporan_kas', ['kategori' => $kategori, 'title' => $title, 'startDate'=>$this->startDate, 'endDate'=>$this->endDate], 
        ['dataKas' => $data_pengeluaran]);
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
        $pengajuan->jumlah = preg_replace("/[^0-9]/","",$request->jumlah);
        $pengajuan->sumber = $request->sumber;     

        if ($pengajuan->User->access == 'admin') {
            $tunai_awal = $pengajuan->tunai;
            $bank_awal = $pengajuan->bank;
            $pengajuan->tunai = preg_replace("/[^0-9]/","",$request->tunai);
            $pengajuan->bank = preg_replace("/[^0-9]/","",$request->bank);
            $pengajuan->Divisi->saldo = $pengajuan->Divisi->saldo - ($tunai_awal+$bank_awal) + ($pengajuan->tunai+$pengajuan->bank);
        } else {
            $saldo = Auth::user()->saldo;
            if ($pengajuan->jumlah > $saldo){
                Alert::error('Approve gagal', 'Maaf, saldo admin tidak cukup');
                return back(); 
            } else {
                $saldo_awal = $pengajuan->Divisi->saldo;
                $saldo_akhir = $saldo_awal + preg_replace("/[^0-9]/","",$request->jumlah);
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
        $pengajuan = Pengajuan::with('User')->findOrFail($id);
        $saldo_user = Saldo::with('User')->findOrFail($pengajuan->user_id);
        //mengambil saldo pengajuan admin yang terbaru
        $saldo_admin = Saldo::with('User')->findOrFail(Auth::user()->id);
        // $pengajuan_admin = Pengajuan::with('Status')->where('user_id', Auth::user()->id)->where('status', 2)->orWhere('status', '4')->get();
        // $admin = $pengajuan_admin->last();
        //menyimpan data
        $pengajuan->tanggal = $request->tanggal;
        $pengajuan->deskripsi = $request->deskripsi;
        $pengajuan->tanggal = $request->tanggal;
        $pengajuan->sumber = $request->sumber;
        $pengajuan->status = "2";        

        if ($pengajuan->User->access == 'admin') {
            $pengajuan->tunai = preg_replace("/[^0-9]/","",$request->tunai);
            $pengajuan->bank = preg_replace("/[^0-9]/","",$request->bank);
            $pengajuan->jumlah = $pengajuan->tunai + $pengajuan->bank;
            $saldo_user->tunai = $pengajuan->tunai;
            $saldo_user->bank = $pengajuan->bank;
            $saldo_user->saldo = $pengajuan->tunai + $pengajuan->bank;

            $saldo_user->save();
        } else {
            $pengajuan->jumlah = preg_replace("/[^0-9]/","",$request->jumlah);
            if ($pengajuan->jumlah > $saldo_admin){
                Alert::error('Approve gagal', 'Maaf, saldo admin tidak cukup');
                return back();
            } else {
                $saldo_awal = $saldo_user->saldo;
                $saldo_akhir = $saldo_awal + preg_replace("/[^0-9]/","",$request->jumlah);
                $saldo_user->saldo = $saldo_akhir;
                $saldo_admin->saldo = $saldo_admin->saldo - $pengajuan->jumlah;
                if ($pengajuan->sumber == 1) {
                    if ($pengajuan->jumlah > $saldo_admin->tunai) {
                        Alert::error('Approve gagal', 'Maaf, saldo tunai tidak cukup');
                        return back();
                    } else {
                        $saldo_admin->tunai = $saldo_admin->tunai - $pengajuan->jumlah;
                    }
                } elseif ($pengajuan->sumber == 2) {
                    if ($pengajuan->jumlah > $saldo_admin->bank) {
                        Alert::error('Approve gagal', 'Maaf, saldo bank tidak cukup');
                        return back();
                    } else {
                        $saldo_admin->bank = $saldo_admin->bank - $pengajuan->jumlah;
                    }
                }
                // $admin->save();
                // Auth::user()->save();
                $saldo_user->save();
                $saldo_admin->save();
            }
        }
        // $pengajuan->Divisi->save();
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

    public function hapus($pengajuan, $id)
    {
        if ($pengajuan == 1) {
            $delete = Pengajuan::with('Divisi')->findOrFail($id);
            if ($delete->divisi_id == 1) {
                $delete->Divisi->saldo = $delete->Divisi->saldo - $delete->jumlah;
            } else {
            Auth::user()->saldo = Auth::user()->saldo + $delete->jumlah;
            $delete->Divisi->saldo = $delete->Divisi->saldo - $delete->jumlah;
            $pengajuan_admin = Pengajuan::with('Status')->where('divisi_id', 1)->where('status', 2)->orWhere('status', '4')->get();
            $admin = $pengajuan_admin->last();
            if ($delete->sumber == 1) {
                $admin->tunai = $admin->tunai + $delete->jumlah;
            } elseif ($delete->sumber == 2) {
                $admin->bank = $admin->bank + $delete->jumlah;
            }
            Auth::user()->save();
            $admin->save();
            }
           
        } else if ($pengajuan == 2) {
            $delete = Pengeluaran::with('Divisi')->findOrFail($id);
            $saldo_awal = $delete->Divisi->saldo;
            $saldo_akhir = $saldo_awal + $delete->jumlah;
            $delete->Divisi->saldo = $saldo_akhir;
        }
        $delete->status = 6;
        $delete->Divisi->save();
        $delete->save();
        
        return back();
    }

    public function kas_divisi($id)
    {
        $data_kas = Pengajuan::with('Sumber','User', 'Status')->where('divisi_id', $id)->get();
        $divisi = Divisi::get();
        session(['key' => $id]);
        $laporan = FALSE;
        $title = "Admin Kas Kecil";
        $pengajuan_admin = Pengajuan::with('Status')->where('divisi_id', 1)->where('status', 2)->orWhere('status', '4')->get();
        $admin = $pengajuan_admin->last();
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
        return view('admin/main', ['dataKas' => $data_kas, 'admin'=>$admin], ['divisi' => $divisi, 'title'=>$title, 'laporan'=>$laporan,'startDate'=>$this->startDate, 'endDate'=>$this->endDate]);
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
        $pengeluaran->jumlah = preg_replace("/[^0-9]/","",$request->jumlah);
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
