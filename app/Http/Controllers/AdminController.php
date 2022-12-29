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
use App\Models\Company;
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
    
    public function index(Request $request){
        $saldoAwal = $request->session()->get('saldo_awal');
        $startDate = $this->startDate;
        $endDate = $this->endDate;
        $laporan = FALSE;
        $dataKas = Pengajuan::with('Sumber','User','Status')->where('status','!=',5)->get();
        $Saldo = Saldo::findOrFail(Auth::user()->id);
        $divisi = Divisi::get();
        $title = "Admin Kas Kecil";
       
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
            return $item->User->kk_access != '1';
        });
        $pengeluaran = Pengeluaran::where('status','!=',3)->where('status','!=',6)->where('status','!=',1)->get();
        $data_pengeluaran = $pengeluaran->filter(function($item, $key){
            return $item->User->kk_access != '1';
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
        session(['total_pengajuan' => $total_pengajuan]);
        session(['total_pengeluaran' => $total_pengeluaran]);
        session(['sisa' => $sisa]);
        return view('admin/main', compact('dataKas','admin','Saldo','divisi','title','laporan','startDate','endDate','total_pengajuan','total_pengeluaran','sisa'));
    }

    public function laporan(){
        $startDate = $this->startDate;
        $endDate = $this->endDate;
        $laporan = TRUE;
        $dataKas = Pengajuan::with('Sumber','User', 'Status')->where('status',5)->get();
        $divisi = Divisi::get();

        // Perhitungan sisa dan total belanja pada card
        $data_pengajuan = Pengajuan::with('User')->where('status','!=',3)->where('status','!=',6)->get();
        $data_pengajuan = $data_pengajuan->filter(function($item, $key){
            return $item->User->kk_access != '1';
        });
        // dd($data_pengajuan);
        $data_pengeluaran = Pengeluaran::with('User')->where('status','!=',3)->where('status','!=',6)->get();
        $data_pengeluaran = $data_pengeluaran->filter(function($item, $key){
            return $item->User->kk_access != '1';
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

        // Perhitungan sisa dan total belanja pada card
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
        $title = "Laporan Pengajuan";

        $pengajuan_admin = Pengajuan::with('Status')->where('divisi_id', 1)->where('status', 2)->orWhere('status', '4')->get();
        $admin = $pengajuan_admin->last();

        return view('admin/main', compact('dataKas', 'admin', 'divisi', 'title', 'laporan','total_pengajuan','total_pengeluaran','sisa','startDate','endDate'));
    }

    public function laporan_keluar()
    {
        $data_pengeluaran = Pengeluaran::with('pengajuan', 'Status', 'COA','Pembebanan')->where('status', 7)->get();
        $title = "Laporan Kas Kecil";
        $kategori = Kategori::with('pengeluaran')->get();
        $company = Company::get();

        return view ('/admin/laporan_kas', ['kategori' => $kategori, 'title' => $title, 'startDate'=>$this->startDate, 'endDate'=>$this->endDate, 'company'=>$company], 
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
        $saldo = Saldo::findOrFail($pengajuan->user_id);

        return view('admin/form-edit', ['pengajuan' => $pengajuan, 'saldo'=> $saldo], ['sumber' => $sumber, 'edit' => $edit]);
    }

    public function update(Request $request, $id)
    {
        $pengajuan = Pengajuan::with('Divisi')->findOrFail($id);
        // $pengajuan_admin = Pengajuan::with('Status')->where('divisi_id', 1)->where('status', 2)->orWhere('status', '4')->get();
        // $admin = $pengajuan_admin->last();
        $saldo = Saldo::findOrFail($pengajuan->user_id);
        $saldo_admin = Saldo::findOrFail(Auth::user()->id);

        $pengajuan->tanggal = $request->tanggal;
        $pengajuan->deskripsi = $request->deskripsi;
        $pengajuan->tanggal = $request->tanggal;
        $pengajuan->sumber = $request->sumber;     
        //pengajuan admin
        if ($pengajuan->User->kk_access == '1') {
            //update tabel saldo
            $tunai_awal = $saldo->tunai;
            $bank_awal = $saldo->bank;
            $pengajuan_lama = $pengajuan->jumlah;
            $pengajuan->jumlah = preg_replace("/[^0-9]/","",$request->jumlah);
            $saldo->tunai = $saldo->tunai - $pengajuan->tunai + preg_replace("/[^0-9]/","",$request->tunai);
            $saldo->bank = $saldo->bank - $pengajuan->bank + preg_replace("/[^0-9]/","",$request->bank);
            $saldo->saldo = $saldo->saldo - $pengajuan_lama + $pengajuan->jumlah;
            //update tabel pengajuan
            $pengajuan->tunai = $saldo->tunai;
            $pengajuan->bank = $saldo->bank;
        } else {
            // EDIT PENGAJUAN NON-ADMIN
            // if ($pengajuan->jumlah > $saldo->saldo){
            //     Alert::error('Approve gagal', 'Maaf, saldo admin tidak cukup');
            //     return back(); 
            // } else {
                // $saldo_awal = $saldo->saldo;
                // $saldo_akhir = $saldo_awal + preg_replace("/[^0-9]/","",$request->jumlah);
                // $saldo->saldo = $saldo_akhir;
                // PENGAJUAN SUMBER TUNAI
                if ($pengajuan->sumber == 1) {
                    $saldo_admin->tunai = $saldo_admin->tunai + $pengajuan->jumlah;
                    $saldo_admin->saldo = $saldo_admin->saldo + $pengajuan->jumlah;
                    $jumlah = preg_replace("/[^0-9]/","",$request->jumlah);
                    if ($jumlah > $saldo_admin->tunai) {
                        Alert::error('Approve gagal', 'Maaf, saldo tunai tidak cukup');
                        return back();
                    } else {
                        $tunai_awal = $pengajuan->jumlah;
                        $pengajuan->jumlah = $jumlah;
                        $saldo_admin->tunai = $saldo_admin->tunai - $pengajuan->jumlah;
                        $saldo_admin->saldo = $saldo_admin->saldo - $pengajuan->jumlah;
                        $saldo->saldo = $saldo->saldo - $tunai_awal + $pengajuan->jumlah;
                    }
                } elseif ($pengajuan->sumber == 2) {
                    $saldo_admin->bank = $saldo_admin->bank + $pengajuan->jumlah;
                    $saldo_admin->saldo = $saldo_admin->saldo + $pengajuan->jumlah;
                    $jumlah = preg_replace("/[^0-9]/","",$request->jumlah);
                    if ($jumlah > $saldo_admin->bank) {
                        Alert::error('Approve gagal', 'Maaf, saldo bank tidak cukup');
                        return back();
                    } else {
                        $bank_awal = $pengajuan->jumlah;
                        $pengajuan->jumlah = $jumlah;
                        $saldo_admin->bank = $saldo_admin->bank - $pengajuan->jumlah;
                        $saldo_admin->saldo = $saldo_admin->saldo - $pengajuan->jumlah;
                        $saldo->saldo = $saldo->saldo - $bank_awal + $pengajuan->jumlah;
                        // dd($saldo_admin->bank);
                    }
                }
                $saldo_admin->save();
                // Auth::user()->save();
        }
        
        $saldo->save();
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

        if ($pengajuan->User->kk_access == '1') {
            $pengajuan->tunai = preg_replace("/[^0-9]/","",$request->tunai);
            $pengajuan->bank = preg_replace("/[^0-9]/","",$request->bank);
            $pengajuan->jumlah = $pengajuan->tunai + $pengajuan->bank;
            $saldo_user->tunai = $pengajuan->tunai;
            $saldo_user->bank = $pengajuan->bank;
            $saldo_user->saldo = $pengajuan->tunai + $pengajuan->bank;
            session(['saldo_awal' => $saldo_user->saldo]);
            $saldo_user->save();
        //PENGAJUAN USER
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
                //PENGAJUAN TUNAI
                if ($pengajuan->sumber == 1) {
                    if ($pengajuan->jumlah > $saldo_admin->tunai) {
                        Alert::error('Approve gagal', 'Maaf, saldo tunai tidak cukup');
                        return back();
                    } else {
                        $saldo_admin->tunai = $saldo_admin->tunai - $pengajuan->jumlah;
                    }
                //PENGAJUAN BANK
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
                $saldo_admin->save();
                $saldo_user->save();
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
        $pengeluaran = Pengeluaran::with('User', 'pengajuan')->findOrFail($id);
        if ($pengeluaran->User->kk_access==2) {
            if ($pengeluaran->deskripsi != "PENGEMBALIAN SALDO PENGAJUAN") {
                $saldo = Saldo::findOrFail($pengeluaran->user_id);
                $saldo->saldo = $saldo->saldo + $pengeluaran->jumlah;
                $saldo->save();
            } elseif ($pengeluaran->deskripsi == "PENGEMBALIAN SALDO PENGAJUAN") {
                $saldo_admin = Saldo::findOrFail(Auth::user()->id);
                $saldo_admin->saldo = $saldo_admin->saldo + $pengeluaran->jumlah;
                if ($pengeluaran->pengajuan->sumber == 1) { //pengajuan tunai
                    $saldo_admin->tunai = $saldo_admin->tunai + $pengeluaran->jumlah;
                } elseif ($pengeluaran->pengajuan->sumber == 2) { //pengajuan bank
                    $saldo_admin->bank = $saldo_admin->bank + $pengeluaran->jumlah;
                }
                $saldo_admin->save();
            }
        }
        $pengeluaran->status = 7;
        $pengeluaran->save();

        $pengeluaran2 = Pengeluaran::with('pengajuan')->where('pemasukan',$pengeluaran->pemasukan)->get();
        $count_pengeluaran = $pengeluaran2->filter(function($item, $key){
            return $item->status == 7;
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
        //HAPUS PENGAJUAN
        if ($pengajuan == 1) {
            $delete = Pengajuan::with('Divisi','User')->findOrFail($id);
            $saldo = Saldo::findOrFail($delete->user_id);
            $saldo_admin = Saldo::findOrFail(Auth::user()->id);
            //HAPUS ADMIN
            if ($delete->User->kk_access == '1') {
                $saldo->saldo = $saldo->saldo - $delete->jumlah;
            } else {
                //JIKA STATUSNYA BELUM DIAPPROVE ATAU DECLINE
                if ($delete->status != 1 AND $delete->status != 3) {
                    $saldo->saldo = $saldo->saldo - $delete->jumlah;
                    $saldo_admin->saldo = $saldo_admin->saldo + $delete->jumlah;
                    // $pengajuan_admin = Pengajuan::with('Status')->where('divisi_id', 1)->where('status', 2)->orWhere('status', '4')->get();
                    // $admin = $pengajuan_admin->last();
                    if ($delete->sumber == 1) {
                        $saldo_admin->tunai = $saldo_admin->tunai + $delete->jumlah;
                    } elseif ($delete->sumber == 2) {
                        $saldo_admin->bank = $saldo_admin->bank + $delete->jumlah;
                    }
                    // $saldo->save();
                    $saldo_admin->save();
                }
            }
        //HAPUS PENGELUARAN
        } else if ($pengajuan == 2) {
            $delete = Pengeluaran::with('Divisi', ' User')->findOrFail($id);
            $saldo = Saldo::findOrFail($delete->user_id);
            $saldo->saldo = $saldo->saldo + $delete->jumlah;
        }
        $delete->status = 6;
        $saldo->save();
        $delete->save();
        
        return back();
    }

    public function kas_divisi(Request $request, $id)
    {
        $dataKas = Pengajuan::with('Sumber','User', 'Status')->where('divisi_id', $id)->get();
        $divisi = Divisi::get();
        session(['key' => $id]);
        $laporan = FALSE;
        $title = "Admin Kas Kecil";
        $startDate = $this->startDate; $endDate = $this->endDate; 
        // $pengajuan_admin = Pengajuan::with('Status')->where('divisi_id', 1)->where('status', 2)->orWhere('status', '4')->get();
        // $admin = $pengajuan_admin->last();
        $Saldo = Saldo::findOrFail(Auth::user()->id);
        // Perhitungan sisa dan total belanja
        foreach ($dataKas as $masuk) {
            $total = 0;
            $data_pengeluaran = Pengeluaran::with('pengajuan')->where('pemasukan','=',$masuk->id)->where('status','!=',6)->get();
            foreach ($data_pengeluaran as $keluar){
                $total = $total + $keluar->jumlah;
            }
            $masuk->total_belanja = $total;
            $masuk->sisa = $masuk->jumlah - $masuk->total_belanja;
        }
        $total_pengajuan = $request->session()->get('total_pengajuan');
        $total_pengeluaran = $request->session()->get('total_pengeluaran');
        $sisa = $request->session()->get('sisa');

        return view('admin/main', compact('dataKas', 'divisi', 'title', 'laporan','startDate', 'endDate', 'Saldo','total_pengajuan','total_pengeluaran','sisa'));
    }

    public function detail_divisi(Request $request, $id)
    {
        $idPengajuan = ($id)??$request->session()->get('key');
        $pengajuan = Pengajuan::find($idPengajuan);
        $totalDiklaim = 0; $totalPengeluaran = 0;
        $dataKas = Pengeluaran::with('pengajuan', 'Status','Pembebanan','COA')->where('pemasukan','=',$id)->where('status','!=',6)->get();
        foreach($dataKas as $k) {
            $totalPengeluaran = $totalPengeluaran + $k->jumlah;
        }
        session(['key' => $id]);
        $kasTotal = Pengeluaran::with('pengajuan', 'Status')->where('pemasukan','=',$id)->where('status',7)->get();
        foreach($kasTotal as $k) {
            $totalDiklaim = $totalDiklaim + $k->jumlah;
        }
        $company = Company::get();
        return view ('admin/detail_pengajuan', compact('dataKas', 'pengajuan', 'totalDiklaim', 'totalPengeluaran','company'));
    }
    
    public function kas_company(Request $request, $id) {
        $idPengajuan = $request->session()->get('key');
        $pengajuan = Pengajuan::find($idPengajuan);
        $totalDiklaim = 0; $totalPengeluaran = 0;
        $dataKas = Pengeluaran::with('pengajuan', 'Status','Pembebanan','COA')->where('pemasukan','=',$idPengajuan)->where('status','!=',6)->where('pembebanan',$id)->get();
        foreach($dataKas as $k) {
            $totalPengeluaran = $totalPengeluaran + $k->jumlah;
        }
        $kasTotal = Pengeluaran::with('pengajuan', 'Status')->where('pemasukan','=',$idPengajuan)->where('status',7)->where('pembebanan',$id)->get();
        foreach($kasTotal as $k) {
            $totalDiklaim = $totalDiklaim + $k->jumlah;
        }
        $company = Company::get();
        return view ('admin/detail_pengajuan', compact('dataKas', 'pengajuan', 'totalDiklaim', 'totalPengeluaran','company'));
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

    public function klaim(Request $request) {
        $total = 0;
        $id = $request->get('pengeluaranId');
        $dataKas = Pengeluaran::with('pengajuan')->whereIn('id',$id)->get();
        foreach ($dataKas as $kas){
            $kas->status = 7;
            $total = $total + $kas->jumlah;
        }
        $dataKas->save();

        return response()->json(['data' => $total]);
    }

}
