<?php

namespace App\Http\Controllers;

use App\Http\Controllers\AdminController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Pengajuan;
use App\Models\Pengeluaran;
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
    public $company;
    
    public function __construct()
    {
        $this->startDate = Carbon::now()->startOfMonth('d-m-Y');
        $this->endDate = Carbon::now()->endOfMonth('d-m-Y');
        $this->company = NULL;
        session(['startDate' => $this->startDate]);
        session(['endDate' => $this->endDate]);
        session(['company' => $this->company]);
    }

    public function hitung_saldo($id=null, $unit=null) {
        $saldo_user = ($id) ? Saldo::find($id) : Saldo::find(Auth::user()->id);
        $saldo = $saldo_user->saldo;
        $data_kas = ($id) ? Pengeluaran::statusProgress()->where('user_id', $id)->get() : Pengeluaran::statusProgress()->where('user_id', Auth::user()->id)->get();
        if (Auth::user()->kk_access==1) {
            $data_kas = Pengeluaran::statusProgressAndKlaim()->where('user_id', Auth::user()->id)->get();
        }
        foreach ($data_kas as $kas) {
            $saldo = $saldo - $kas->jumlah;
        }
        return $saldo;
    }

    public function index(Request $request, $id=null)
    {
        $pengajuan = ($id) ? Pengajuan::find($id) : null;
        $company = Company::get();
        $button_kas = TRUE;
        $title = "Kas Keluar";
        $dataKas = Pengeluaran::with('Pembebanan', 'Status', 'COA')->statusProgress()->searchByUser(Auth::user()->id)->orderBy('status','asc')->get();
        session(['key' => $id]);
        $total = $dataKas->sum('jumlah');
        $saldo = $this->hitung_saldo();
        $totalDiklaim = 0; $totalPengeluaran = 0;
        $kas = Pengeluaran::with('pengajuan', 'Status','Pembebanan','COA')->where('pemasukan','=',$id)->where('status','!=',6)->where('deskripsi','!=',"PENGEMBALIAN SALDO PENGAJUAN")->get();
        $kas_belum_klaim = Pengeluaran::with('pengajuan', 'Status','Pembebanan','COA')->where('pemasukan','=',$id)->where('status',4)->where('deskripsi','!=',"PENGEMBALIAN SALDO PENGAJUAN")->get();
        foreach($kas_belum_klaim as $k) {
            $totalPengeluaran = $totalPengeluaran + $k->jumlah;
        }
        session(['key' => $id]);
        $kasTotal = Pengeluaran::with('pengajuan', 'Status')->where('pemasukan','=',$id)->whereIn('status',[7,8])->where('deskripsi','!=',"PENGEMBALIAN SALDO PENGAJUAN")->get();
        foreach($kasTotal as $k) {
            $totalDiklaim = $totalDiklaim + $k->jumlah;
        }

        return view('detail_pengajuan', compact('dataKas', 'title', 'button_kas', 'saldo','totalDiklaim', 'totalPengeluaran','pengajuan','company'));
    }

    public function laporan(Request $request)
    {
        session(['company' => $this->company]);
        $startDate = $request->startDate ? $request->startDate : $this->startDate;
        $endDate = $request->endDate ? $request->endDate : $this->endDate;
        $button_kas = FALSE;
        if(Auth::user()->kk_access==1) {
            $dataKas = Pengeluaran::with('pengajuan', 'Status', 'Pembebanan')->whereIn('status', [7, 8])->get();
        } else {
            $dataKas = Pengeluaran::with('pengajuan', 'Status', 'Pembebanan')->where('user_id', Auth::user()->id)->where('status','!=',6)->where('deskripsi','!=',"PENGEMBALIAN SALDO PENGAJUAN")->whereBetween('tanggal',[$startDate,$endDate])->get();
        }
        $title = "Laporan Pengeluaran Kas Kecil";
        $saldo = $this->hitung_saldo();
        $company = Company::get();
        
        return view('detail_pengajuan', compact('dataKas','title', 'button_kas', 'startDate', 'endDate', 'saldo','company'));
    }

    public function set_tanggal($startDate, $endDate){
        session(['startDate' => $startDate]);
        session(['endDate' => $endDate]);
    }

    public function kas_company(Request $request, $id, $id_comp) {
        $idPengajuan = $request->session()->get('key');
        session(['company' => $id_comp]);
        $startDate = ($request->startDate)? $request->startDate  : $this->startDate; 
        $endDate = ($request->endDate) ? $request->endDate : $this->endDate;
        $pengajuan = Pengajuan::find($idPengajuan);
        $totalDiklaim = 0; $totalPengeluaran = 0;
        if ($id == 1) { //index
            $dataKas = Pengeluaran::with('pengajuan', 'Status','Pembebanan','COA')->where('pemasukan',$idPengajuan)->where('status','!=',6)->where('pembebanan',$id_comp)->get();
            $belumDiklaim = Pengeluaran::with('pengajuan', 'Status','Pembebanan','COA')->where('pemasukan',$idPengajuan)->whereNotIn('status',[3,6,7,8])->where('pembebanan',$id_comp)->get();
        } elseif ($id == 2) { //laporan
            $dataKas = Pengeluaran::with('pengajuan', 'Status','Pembebanan','COA')->whereNotIn('status',[3,6])->where('pembebanan',$id_comp)->where('user_id',Auth::user()->id)
            ->where(function ($query) use ($startDate, $endDate) {
                if ($startDate && $endDate) {
                    $query->whereBetween('tanggal', [$startDate, $endDate]);
                }
            })->get();
            $belumDiklaim = Pengeluaran::with('pengajuan', 'Status','Pembebanan','COA')->whereNotIn('status',[3,6,7,8])->where('pembebanan',$id_comp)->get();
        }
        foreach($belumDiklaim as $k) {
            $totalPengeluaran = $totalPengeluaran + $k->jumlah;
        }
        $kasTotal = Pengeluaran::with('pengajuan', 'Status')->where('pemasukan','=',$idPengajuan)->whereIn('status', [7, 8])->where('pembebanan',$id_comp)->get();
        foreach($kasTotal as $k) {
            $totalDiklaim = $totalDiklaim + $k->jumlah;
        }
        $company = Company::get();
        $saldo = $this->hitung_saldo();
        if ($id == 1) { //index
            $laporan = FALSE;
            $title = "Detail Pengajuan";
            $button_kas = TRUE; 
            if (Auth::user()->kk_access == 1) {
                $dataKas = Pengeluaran::with('pengajuan', 'Status', 'COA','Pembebanan')->where('status', 4)->where('pembebanan',$id_comp)->whereBetween('tanggal',[$startDate,$endDate])->orderBy('status','desc')->get();
                $Saldo = $this->hitung_saldo();
                $totalKeluar = 0; $totalSetBKK = 0; $totalBelumSetBKK = 0;
                foreach($dataKas as $value) {
                    $totalKeluar = $totalKeluar + $value->jumlah;
                    if($value->status == 7) {
                        $totalBelumSetBKK = $totalBelumSetBKK + $value->jumlah;
                    } elseif ($value->status == 8) {
                        $totalSetBKK = $totalSetBKK + $value->jumlah;
                    }
                }
                return view('/admin/kas', compact('title', 'startDate', 'endDate', 'company','dataKas','Saldo','totalKeluar','totalSetBKK','totalBelumSetBKK', 'laporan'));
            } elseif (Auth::user()->kk_access == 2) {
                return view('detail_pengajuan', compact('dataKas', 'title', 'button_kas', 'saldo','totalDiklaim', 'totalPengeluaran','pengajuan','company'));
            }
        } elseif ($id == 2) { //laporan
            $title = "Laporan Pengeluaran Kas Kecil";
            $button_kas = FALSE; 
            $laporan = TRUE;
            if (Auth::user()->kk_access == 1) {
                $dataKas = Pengeluaran::with('pengajuan', 'Status', 'COA','Pembebanan')->whereIn('status', [7,8])->where('pembebanan',$id_comp)->whereBetween('tanggal',[$startDate,$endDate])->orderBy('status','desc')->get();
                $Saldo = $this->hitung_saldo();
                $totalKeluar = 0; $totalSetBKK = 0; $totalBelumSetBKK = 0;
                foreach($dataKas as $value) {
                    $totalKeluar = $totalKeluar + $value->jumlah;
                    if($value->status == 7) {
                        $totalBelumSetBKK = $totalBelumSetBKK + $value->jumlah;
                    } elseif ($value->status == 8) {
                        $totalSetBKK = $totalSetBKK + $value->jumlah;
                    }
                }
                return view('/admin/kas', compact('title', 'startDate', 'endDate', 'company','dataKas','Saldo','totalKeluar','totalSetBKK','totalBelumSetBKK', 'laporan'));
            } elseif (Auth::user()->kk_access == 2) {
                return view('detail_pengajuan', compact('dataKas','title', 'button_kas', 'startDate', 'endDate', 'saldo','company'));
            }
        }
    }

    public function create(Request $request)
    {
        $Company = Company::get();
        if ($request->search_coa) {
            dd($request->search_coa);
            
        }
        $Coa = COA::where('status','!=',0)->get();

        return view('form_kas', ['Company' => $Company, 'Coa' => $Coa]);
    }

    public function save(Request $request)
    {
        $kas = new Pengeluaran;
        $kas->tanggal = $request->tanggal;
        $kas->deskripsi = $request->deskripsi;
        $kas->coa = $request->coa;
        $kas->pic = $request->pic;
        $kas->pembebanan = $request->company;
        $kas->tujuan = $request->tujuan;
        $kas->status = "4";
        // $kas->pemasukan = $request->session()->get('key');
        $kas->user_id = Auth::user()->id;
        $kas->divisi_id = Auth::user()->level;

        $kas->jumlah = preg_replace("/[^0-9]/", "", $request->kredit);
        $saldo = $this->hitung_saldo();
        if ($kas->jumlah > $saldo) {
            Alert::error('Input kas gagal', 'Maaf, saldo tidak cukup');
            return back();
        } else {
            $kas->save();
        }
        return redirect('home');
    }

    public function edit(Request $request, $id)
    {
        $kas = Pengeluaran::with('pengajuan', 'Pembebanan')->findOrFail($id);
        $Company = Company::get();
        $Coa = COA::where('status','!=',0)->get();

        return view('form-edit', compact('kas','Company','Coa'));
    }

    public function update(Request $request, $id)
    {
        $kas = Pengeluaran::with('pengajuan', 'Divisi')->findOrFail($id);
        $kas->tanggal = $request->tanggal;
        $kas->pic = $request->pic;
        $kas->deskripsi = $request->deskripsi;
        $kas->coa = $request->coa;
        $kas->pembebanan = $request->company;
        $kas->tujuan = $request->tujuan;
        $saldo = $this->hitung_saldo();
        //simpan data
        $kas->jumlah = preg_replace("/[^0-9]/", "", $request->jumlah);
        $kas->save();

        return redirect('home');
    }

    public function delete($id)
    {
        Pengeluaran::with('Divisi','User')->find($id)->update(['status'=>'6']);
        return back();
    }

    public function done(Request $request)
    {
        if (Auth::user()->kk_access == 1) { //Jika user admin langsung klaim
            Pengeluaran::whereIn('id',$request->ids)->update(['status'=>'7','tanggal_respon'=>Carbon::now()]);
        } else if (Auth::user()->kk_access == 2) { //Jika user biasa tunggu klaim admin
            Pengeluaran::whereIn('id',$request->ids)->update(['status'=>'4','tanggal_respon'=>Carbon::now()]);
        }
        Pengajuan::find($request->session()->get('key'))->update(['status'=>'4']);

        return response()->json(true);
    }

    public function filter(Request $request)
    {
        $button_kas = FALSE;
        $this->startDate = $request->startDate; session(['startDate' => $request->startDate]);
        $this->endDate = $request->endDate; session(['endDate' => $request->endDate]);
        if (Auth::user()->kk_access == 1) {
            $dataKas = Pengeluaran::with('pengajuan', 'Status', 'COA','Pembebanan')->whereIn('status', [7,8])->whereBetween('tanggal',[$this->startDate,$this->endDate])
                    ->where('deskripsi','!=','PENGEMBALIAN SALDO PENGAJUAN')->orderBy('status','asc')->get();
        } elseif (Auth::user()->kk_access == 2) {
            $dataKas = Pengeluaran::with('pengajuan', 'Status', 'COA')->where('user_id', Auth::user()->id)->whereIn('status', [7,8])->whereBetween('tanggal',[$this->startDate,$this->endDate])->orderBy('status','asc')->get();
        }
        $company = Company::get();
        $title = "Laporan Pengeluaran Kas Kecil";
        $Saldo = $this->hitung_saldo();

        if (Auth::user()->kk_access == 1) {
            $startDate = $this->startDate; $endDate = $this->endDate;
            $totalKeluar = 0; $totalSetBKK = 0; $totalBelumSetBKK = 0;
            foreach($dataKas as $value) {
                $totalKeluar = $totalKeluar + $value->jumlah;
                if($value->status == 7) {
                    $totalBelumSetBKK = $totalBelumSetBKK + $value->jumlah;
                } elseif ($value->status == 8) {
                    $totalSetBKK = $totalSetBKK + $value->jumlah;
                }
            }
            return view('/admin/kas', compact('title', 'startDate', 'endDate', 'company','dataKas','Saldo','totalKeluar','totalSetBKK','totalBelumSetBKK'));
        } elseif (Auth::user()->kk_access == 2) {
            return view('detail_pengajuan', ['dataKas' => $dataKas], ['title' => $title, 'button_kas' => $button_kas, 'startDate' => $this->startDate, 'endDate' => $this->endDate, 'saldo' => $Saldo, 'company'=>$company]);
        }
    }

    public function export(Request $request)
    {
        $startDate = ($request->startDate) ? $request->startDate : $request->session()->get('startDate');
        $endDate = ($request->endDate) ? $request->endDate : $request->session()->get('endDate');
        $company = $request->session()->get('company');
        
        return (new KasKecilExport($startDate,$endDate,$company))->download("Laporan_Kas_Kecil" . $startDate . $endDate . ".xlsx");
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
            $saldo_akhir = $saldo->saldo - $kas->jumlah;
            $saldo->saldo = $saldo_akhir;
            $saldo->save();
            $kas->save();
        }
        return redirect('home');
    }
}
