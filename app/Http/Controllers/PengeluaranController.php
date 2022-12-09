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
        $dataKas = Pengeluaran::with('Pembebanan', 'Status', 'COA')->where('pemasukan', '=', $id)->get();
        session(['key' => $id]);
        $total = $dataKas->sum('jumlah');
        $saldo = Saldo::find(Auth::id());
        $totalDiklaim = 0; $totalPengeluaran = 0;
        $kas = Pengeluaran::with('pengajuan', 'Status','Pembebanan','COA')->where('pemasukan','=',$id)->where('status','!=',6)->get();
        foreach($dataKas as $k) {
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

    public function create()
    {
        $kategori = Kategori::get();
        $Company = Company::get();
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
            return view('/admin/laporan_kas', ['kategori' => $kategori, 'title' => $title, 'startDate' => $this->startDate, 'endDate' => $this->endDate],['dataKas' => $data_pengeluaran]);
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

    public function coba_export(Request $request)
    {
        $dateNow = Carbon::now()->format('d-m-Y');
        $startDate = $this->startDate;
        $endDate = $this->endDate;
        if (Auth::user()->kk_access == 1) {
            if ($startDate and $endDate) {
                $data_pengeluaran = Pengeluaran::with('User', 'pengajuan', 'Kategori')->where('status', 7)->whereBetween('tanggal', [$startDate, $endDate])->get();
                $pengajuan_klaim = Pengeluaran::with('User', 'pengajuan', 'Kategori')->where('status', 4)->whereBetween('tanggal', [$startDate, $endDate])->get();
            } else {
                $data_pengeluaran = Pengeluaran::with('User', 'pengajuan', 'Kategori')->where('status', 7)->get();
                $pengajuan_klaim = Pengeluaran::with('User', 'pengajuan', 'Kategori')->where('status', 4)->get();
            }
            $pengajuan = Pengajuan::with('User')->where('status','!=',3)->where('status','!=',6)->where('status','!=',1)->get();
            $data_pengajuan = $pengajuan->filter(function($item, $key){
                return $item->User->kk_access != '1';
            });
        } elseif (Auth::user()->kk_access == 2){
            if ($startDate and $endDate) {
                $data_pengeluaran = Pengeluaran::with('User', 'pengajuan', 'Kategori')->where('user_id', Auth::user()->id)->where('status', 7)->whereBetween('tanggal', [$startDate, $endDate])->get();
                $pengajuan_klaim = Pengeluaran::with('User', 'pengajuan', 'Kategori')->where('user_id', Auth::user()->id)->where('status', 4)->whereBetween('tanggal', [$startDate, $endDate])->get();
            } else {
                $data_pengeluaran = Pengeluaran::with('User', 'pengajuan', 'Kategori')->where('user_id', Auth::user()->id)->where('status', 7)->get();
                $pengajuan_klaim = Pengeluaran::with('User', 'pengajuan', 'Kategori')->where('user_id', Auth::user()->id)->where('status', 4)->get();
            }
            $data_pengajuan = Pengajuan::with('User')->where('status','!=',3)->where('status','!=',6)->where('status','!=',1)->where('user_id',Auth::user()->id)->get();
        }
                // for ($i = 0; $i < count($data_pengeluaran); $i++) {
        //     $data_pengeluaran[$i]->pengajuan = Pengajuan::select('kode')->where('id', $data_pengeluaran[$i]->pemasukan)->get();
        //     $data_pengeluaran[$i]->coa = COA::select('code')->where('coa_id', $data_pengeluaran[$i]->coa)->get();
        //     $data_pengeluaran[$i]->nama_coa = COA::select('name')->where('coa_id', $data_pengeluaran[$i]->coa)->get();
        //     $data_pengeluaran[$i]->nama_pembebanan = Company::select('name')->where('project_company_id', $data_pengeluaran[$i]->pembebanan)->get();
        //     $data_pengeluaran[$i]->divisi = Divisi::select('name')->where('id', $data_pengeluaran[$i]->User->level)->get();
        //     $data_pengeluaran[$i]->user = $data_pengeluaran[$i]->User->username;
        // }
        $total = 0;
        foreach ($data_pengeluaran as $kas) {
            $total = $total + $kas->jumlah;
        }
        $total_belum_diklaim = 0;
        foreach ($pengajuan_klaim as $kas) {
            $total_belum_diklaim = $total_belum_diklaim + $kas->jumlah;
        }
        $total_pengajuan = 0;
        foreach ($data_pengajuan as $masuk){
            $total_pengajuan = $total_pengajuan + $masuk->jumlah;
        }
        $data_pengeluaran->sisa = $total_pengajuan - $total_belum_diklaim - $total;
        $data_pengeluaran->belum_diklaim = $total_belum_diklaim;
        $data_pengeluaran->total = $total;
        $saldo = Saldo::find(Auth::user()->id);
        $data_pengeluaran->saldo = $saldo->saldo;
        return view('export_kaskecil', compact('data_pengeluaran','startDate','endDate','dateNow'));
    }
}
