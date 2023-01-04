<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Pengajuan;
use App\Models\Pengeluaran;
use App\Models\Kategori;
use App\Models\Pembebanan;
use App\Models\Divisi;
use App\Models\Saldo;
use App\Models\COA;
use App\Models\Company;
use App\Models\Project;
use App\Exports\KasKecilExport;
use Alert;
use Carbon\Carbon;

class PengeluaranController extends Controller
{
    public $startDate;
    public $endDate;

    public function __construct()
    {
        $this->startDate = Carbon::now()->startOfMonth('d-m-Y');
        $this->endDate = Carbon::now()->endOfMonth('d-m-Y');
        $this->company = NULL;
        session(['startDate' => $this->startDate]);
        session(['endDate' => $this->endDate]);
        session(['company' => $this->company]);
    }

    public function index(Request $request, $id)
    {
        $pengajuan = Pengajuan::find($id);
        $company = Company::get();
        $button_kas = TRUE;
        $title = "Detail Pengajuan";
        $dataKas = Pengeluaran::with('Pembebanan', 'Status', 'COA')->where('pemasukan', '=', $id)->orderBy('status','asc')->get();
        session(['key' => $id]);
        $total = $dataKas->sum('jumlah');
        $saldo = Saldo::find(Auth::id());
        $totalDiklaim = 0; $totalPengeluaran = 0;
        $kas = Pengeluaran::with('pengajuan', 'Status','Pembebanan','COA')->where('pemasukan','=',$id)->where('status','!=',6)->get();
        foreach($kas as $k) {
            $totalPengeluaran = $totalPengeluaran + $k->jumlah;
        }
        session(['key' => $id]);
        $kasTotal = Pengeluaran::with('pengajuan', 'Status')->where('pemasukan','=',$id)->where('status',7)->get();
        foreach($kasTotal as $k) {
            $totalDiklaim = $totalDiklaim + $k->jumlah;
        }

        return view('detail_pengajuan', compact('dataKas', 'title', 'button_kas', 'saldo','totalDiklaim', 'totalPengeluaran','pengajuan','company'));
    }

    public function laporan()
    {
        session(['company' => $this->company]);
        $startDate = $this->startDate;
        $endDate = $this->endDate;
        $button_kas = FALSE;
        $dataKas = Pengeluaran::with('pengajuan', 'Status', 'Kategori', 'Pembebanan')->where('user_id', Auth::user()->id)->where('status', 7)->get();
        $title = "Laporan Pengeluaran Kas Kecil";
        // dd(view('detail_pengajuan', ['dataKas' => $data_pengeluaran], ['title' => $title, 'button_kas' => $button_kas, 'startDate' => $startDate, 'endDate' => $endDate]));
        $saldo = Saldo::find(Auth::id());
        $company = Company::get();
        
        return view('detail_pengajuan', compact('dataKas','title', 'button_kas', 'startDate', 'endDate', 'saldo','company'));
    }

    public function kas_company(Request $request, $id, $id_comp) {
        $idPengajuan = $request->session()->get('key');
        session(['company' => $id_comp]);
        $pengajuan = Pengajuan::find($idPengajuan);
        $totalDiklaim = 0; $totalPengeluaran = 0;
        if ($id == 1) { //index
            $dataKas = Pengeluaran::with('pengajuan', 'Status','Pembebanan','COA')->where('pemasukan','=',$idPengajuan)->where('status','!=',6)->where('pembebanan',$id_comp)->get();
        } elseif ($id == 2) { //laporan
            $dataKas = Pengeluaran::with('pengajuan', 'Status','Pembebanan','COA')->where('status',7)->where('pembebanan',$id_comp)->get();
        }
        foreach($dataKas as $k) {
            $totalPengeluaran = $totalPengeluaran + $k->jumlah;
        }
        $kasTotal = Pengeluaran::with('pengajuan', 'Status')->where('pemasukan','=',$idPengajuan)->where('status',7)->where('pembebanan',$id_comp)->get();
        foreach($kasTotal as $k) {
            $totalDiklaim = $totalDiklaim + $k->jumlah;
        }
        $company = Company::get();
        $saldo = Saldo::find(Auth::id());
        if ($id == 1) { //index
            $title = "Detail Pengajuan";
            $button_kas = TRUE; 
            return view('detail_pengajuan', compact('dataKas', 'title', 'button_kas', 'saldo','totalDiklaim', 'totalPengeluaran','pengajuan','company'));
        } elseif ($id == 2) { //laporan
            $title = "Laporan Pengeluaran Kas Kecil";
            $button_kas = FALSE; 
            $startDate = $this->startDate;
            $endDate = $this->endDate;
            return view('detail_pengajuan', compact('dataKas','title', 'button_kas', 'startDate', 'endDate', 'saldo','company'));
        }
    }

    public function create(Request $request)
    {
        $kategori = Kategori::get();
        $Company = Company::get();
        if ($request->search_coa) {
            dd($request->search_coa);
            
        }
        $Coa = COA::where('status','!=',0)->get();

        return view('form_kas', ['kategori' => $kategori, 'Company' => $Company, 'Coa' => $Coa]);
    }

    public function save(Request $request)
    {
        $kas = new Pengeluaran;
        $kas->tanggal = $request->tanggal;
        $kas->deskripsi = $request->deskripsi;
        $kas->kategori = $request->kategori;
        $kas->coa = $request->coa;
        $kas->pembebanan = $request->company;
        $kas->tujuan = $request->tujuan;
        $kas->status = "4";
        $kas->pemasukan = $request->session()->get('key');
        $kas->user_id = Auth::user()->id;
        $kas->divisi_id = Auth::user()->level;
        $pengajuan = Pengajuan::find($kas->pemasukan);
        if ($pengajuan->status == 5) {
            $pengajuan->status = 4;
            $pengajuan->save();
        }
        //Kas admin
        if (Auth::user()->kk_access == '1') {
            $tunai =  (float) preg_replace("/[^0-9]/", "", $request->tunai);
            $bank =  (float) preg_replace("/[^0-9]/", "", $request->bank);
            $kas->jumlah = $tunai + $bank;
            $saldo = Saldo::findOrFail(Auth::user()->id);
            if ($kas->jumlah > $saldo->saldo) {
                Alert::error('Input kas gagal', 'Maaf, saldo tidak cukup');
                return back();
            } else {
                $saldo_akhir = $saldo->saldo - $kas->jumlah;
                $saldo->saldo = $saldo_akhir;
                $kas->save();
                #mengurangi saldo tunai dan bank
                $lastInsertedId = $kas->id;
                $pengeluaran = Pengeluaran::with('pengajuan')->find($lastInsertedId);
                $saldo->tunai = $saldo->tunai - $tunai;
                $saldo->bank = $saldo->bank - $bank;
                $pengeluaran->pengajuan->tunai = $pengeluaran->pengajuan->tunai - $tunai;
                $pengeluaran->pengajuan->bank = $pengeluaran->pengajuan->bank - $bank;
                $pengeluaran->pengajuan->save();
                $saldo->save();
            }
        //Kas non admin
        } else {
            $kas->jumlah = preg_replace("/[^0-9]/", "", $request->kredit);
            $saldo = Saldo::findOrFail(Auth::user()->id);
            if ($kas->jumlah > $saldo->saldo) {
                Alert::error('Input kas gagal', 'Maaf, saldo tidak cukup');
                return back();
            } else {
                $saldo_akhir = $saldo->saldo - $kas->jumlah;
                $saldo->saldo = $saldo_akhir;
                $saldo->save();
                $kas->save();
            }
        }
        return redirect('home');
    }

    public function edit(Request $request, $id)
    {
        $kas = Pengeluaran::with('pengajuan', 'Kategori', 'Pembebanan')->findOrFail($id);
        $Company = Company::get();
        $Coa = COA::where('status','!=',0)->get();

        return view('form-edit', compact('kas','Company','Coa'));
    }

    public function update(Request $request, $id)
    {
        $kas = Pengeluaran::with('pengajuan', 'Divisi')->findOrFail($id);
        $kas_input = preg_replace("/[^0-9]/", "", $request->jumlah);
        $kas->tanggal = $request->tanggal;
        $kas->deskripsi = $request->deskripsi;
        $kas->kategori = $request->kategori;
        $kas->coa = $request->coa;
        $kas->pembebanan = $request->company;
        $kas->tujuan = $request->tujuan;
        $saldo = Saldo::findOrFail(Auth::user()->id);
        //mengembalikan saldo
        $saldo->saldo = $saldo->saldo - $kas_input + $kas->jumlah;
        //simpan data
        $kas->jumlah = preg_replace("/[^0-9]/", "", $request->jumlah);
        $saldo->save();
        $kas->save();

        return redirect('home');
    }

    public function delete($id)
    {
        $delete = Pengeluaran::with('Divisi')->findOrFail($id);
        $saldo = Saldo::findOrFail(Auth::user()->id);
        $saldo_awal = $saldo->saldo;
        $saldo_akhir = $saldo_awal + $delete->jumlah;
        $saldo->saldo = $saldo_akhir;

        $saldo->save();
        $delete->status = 6;
        $delete->save();
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

    public function filter(Request $request)
    {
        $button_kas = FALSE;
        $this->startDate = $request->startDate; session(['startDate' => $request->startDate]);
        $this->endDate = $request->endDate; session(['endDate' => $request->endDate]);
        if (Auth::user()->kk_access == 1) {
            $data_pengeluaran = Pengeluaran::with('pengajuan', 'Status', 'kategori')->where('status', 7)->where('tanggal', '>=', $this->startDate)->where('tanggal', '<=', $this->endDate)->get();
        } elseif (Auth::user()->kk_access == 2) {
            $data_pengeluaran = Pengeluaran::with('pengajuan', 'Status', 'kategori')->where('user_id', Auth::user()->id)->where('status', 7)->where('tanggal', '>=', $this->startDate)->where('tanggal', '<=', $this->endDate)->get();
        }
        $company = Company::get();
        $title = "Laporan Pengeluaran Kas Kecil";
        $kategori = Kategori::with('pengeluaran')->get();
        $saldo = Saldo::find(Auth::id());

        if (Auth::user()->kk_access == 1) {
            return view('/admin/laporan_kas', ['kategori' => $kategori, 'title' => $title, 'startDate' => $this->startDate, 'endDate' => $this->endDate, 'company'=>$company],['dataKas' => $data_pengeluaran]);
        } elseif (Auth::user()->kk_access == 2) {
            return view('detail_pengajuan', ['dataKas' => $data_pengeluaran], ['title' => $title, 'button_kas' => $button_kas, 'startDate' => $this->startDate, 'endDate' => $this->endDate, 'saldo' => $saldo, 'company'=>$company]);
        }
    }

    public function export(Request $request)
    {
        $startDate = $request->session()->get('startDate');
        $endDate = $request->session()->get('endDate');
        $company = $request->session()->get('company');
        
        return (new KasKecilExport($startDate,$endDate,$company))->download("Laporan_Kas_Kecil" . ".xlsx");
    }

    public function pengembalian_saldo(Request $request, $id) {
        $kas = new Pengeluaran;
        $kas->tanggal = $request->tanggal;
        $kas->deskripsi = "PENGEMBALIAN SALDO PENGAJUAN";
        $kas->pemasukan = $id;
        $kas->user_id = Auth::user()->id;
        $kas->status = "4";
        $kas->divisi_id = Auth::user()->level;
        $pengajuan = Pengajuan::find($id);
        if ($pengajuan->status == 5) {
            $pengajuan->status = 4;
            $pengajuan->save();
        }
        $pengajuan = Pengajuan::findOrFail($id);
        $saldo = Saldo::findOrFail(Auth::user()->id);
        //Ambil saldo admin
        $saldo_admin = Saldo::with('User')->where('kk_access',1)->where('id','!=',23)->first();
        //Kas admin
        if (Auth::user()->kk_access == '1') {
            $tunai =  (float) preg_replace("/[^0-9]/", "", $request->tunai);
            $bank =  (float) preg_replace("/[^0-9]/", "", $request->bank);
            $kas->jumlah = $tunai + $bank;
            $saldo = Saldo::findOrFail(Auth::user()->id);
            if ($kas->jumlah > $pengajuan->jumlah) {
                Alert::error('Input kas gagal', 'Maaf, saldo tidak cukup');
                return back();
            } else {
                $kas->save();
                $saldo_akhir = $saldo->saldo - $kas->jumlah;
                $saldo->saldo = $saldo_akhir;
                $saldo->tunai = $saldo->tunai - $tunai;
                $saldo->bank = $saldo->bank - $bank;
                $saldo->save();
                $saldo_admin = Saldo::findOrFail(Auth::user()->id);
                $saldo_admin->saldo = $saldo_admin->saldo + $kas->jumlah;
                $saldo_admin->tunai = $saldo_admin->tunai + $tunai;
                $saldo_admin->bank = $saldo_admin->bank + $bank;
                $saldo_admin->save();
                #mengurangi saldo tunai dan bank
                $lastInsertedId = $kas->id;
                $pengeluaran = Pengeluaran::with('pengajuan')->find($lastInsertedId);
                $pengeluaran->pengajuan->tunai = $pengeluaran->pengajuan->tunai - $tunai;
                $pengeluaran->pengajuan->bank = $pengeluaran->pengajuan->bank - $bank;
                $pengeluaran->pengajuan->save();
            }
        //Kas non admin
        } else {
            $kas->jumlah = preg_replace("/[^0-9]/", "", $request->jumlah);
            if ($kas->jumlah > $pengajuan->jumlah) {
                Alert::error('Input kas gagal', 'Maaf, saldo tidak cukup');
                return back();
            } else {
                $saldo_akhir = $saldo->saldo - $kas->jumlah;
                $saldo->saldo = $saldo_akhir;
                $saldo->save();
                $kas->save();
            }
        }
        return redirect('home');
        dd($kas);
    }
}
