<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\PengeluaranController;
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
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Alert;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public $startDate;
    public $endDate;
    public $company;

    public function __construct() {
        $this->startDate = Carbon::now()->startOfMonth();
        $this->endDate = Carbon::now()->endOfMonth();
        $this->company = null;
    }
    
    public function index(Request $request,$id=null){
        // $saldoAwal = $request->session()->get('saldo_awal');
        $startDate = ($request->startDate) ? $request->startDate  : $this->startDate;
        $endDate = ($request->endDate) ? $request->endDate : $this->endDate;
        // $startDate = $request->startDate; $endDate = $request->endDate;
        $company = $request->id ? $request->id : null;
        $filter_keluar = FALSE;
        // $startDate = $this->startDate; $endDate = $this->endDate;
        $laporan = FALSE;

        $dataKas = Pengajuan::with('Sumber','User','Status')
        ->where(function ($query) use ($id) {
            if ($id) {
                $query->where('user_id', $id);
            }
        })
        ->where(function ($query) use ($startDate, $endDate) {
            if ($startDate && $endDate && $startDate!=$this->startDate && $endDate!=$this->endDate) {
                $query->whereBetween('tanggal', [$startDate, $endDate]);
            }
        })->get();
        $Saldo = Saldo::findOrFail(Auth::user()->id);
        $divisi = Divisi::get();
        $title = "Admin Kas Kecil";
        $userList = DB::table('user')->join('pettycash_pengajuan', 'user.id', '=', 'pettycash_pengajuan.user_id')->select('user.*')->get()->unique('id');
        $companyList = DB::table('project_company')->join('pettycash_pengeluaran', 'project_company.project_company_id', '=', 'pettycash_pengeluaran.pembebanan')->select('project_company.*')->get()->unique('project_company_id');
        // Perhitungan sisa dan total belanja
        foreach ($dataKas as $masuk) {
            $total = 0;
            $diklaim = 0;
            $data_pengeluaran = Pengeluaran::with('pengajuan')->where('pemasukan','=',$masuk->id)->whereNotIn('status',[3,6,7,8])->where('deskripsi','!=',"PENGEMBALIAN SALDO PENGAJUAN")
            ->where(function ($query) use ($id) {
                if ($id) {
                    $query->where('user_id', $id);
                }
            })
            ->where(function ($query) use ($startDate, $endDate) {
                if ($startDate && $endDate && $startDate!=$this->startDate && $endDate!=$this->endDate) {
                    $query->whereBetween('tanggal', [$startDate, $endDate]);
                }
            })->get();
            foreach ($data_pengeluaran as $keluar){
                $total = $total + $keluar->jumlah;
            }
            $data_diklaim = Pengeluaran::with('pengajuan')->where('pemasukan','=',$masuk->id)->whereIn('status',[7,8])->where('deskripsi','!=',"PENGEMBALIAN SALDO PENGAJUAN")
            ->where(function ($query) use ($id) {
                if ($id) {
                    $query->where('user_id', $id);
                }
            })
            ->where(function ($query) use ($startDate, $endDate) {
                if ($startDate && $endDate && $startDate!=$this->startDate && $endDate!=$this->endDate) {
                    $query->whereBetween('tanggal', [$startDate, $endDate]);
                }
            })->get();
            foreach ($data_diklaim as $keluar){
                $diklaim = $diklaim + $keluar->jumlah;
            }
            $masuk->total_belanja = $total;
            $masuk->diklaim = $diklaim;
            $masuk->sisa = $masuk->jumlah - $masuk->total_belanja;
        }

        // Perhitungan sisa dan total belanja pada card
        if ($id && $startDate && $endDate && $startDate!=$this->startDate && $endDate!=$this->endDate) {
            $total_pengajuan = $this->hitung_pengajuan($id,$startDate,$endDate);
            $total_pengeluaran = $this->hitung_belum_klaim($id,$startDate,$endDate);
            $total_diklaim = $this->hitung_klaim($id,$startDate,$endDate);
        } else if ($id) {
            $total_pengajuan = $this->hitung_pengajuan($id);
            $total_pengeluaran = $this->hitung_belum_klaim($id);
            $total_diklaim = $this->hitung_klaim($id);
        } else if ($startDate && $endDate && $startDate!=$this->startDate && $endDate!=$this->endDate) {
            $total_pengajuan = $this->hitung_pengajuan(null,$startDate,$endDate);
            $total_pengeluaran = $this->hitung_belum_klaim(null,$startDate,$endDate);
            $total_diklaim = $this->hitung_klaim(null,$startDate,$endDate);
        } else {
            $total_pengajuan = $this->hitung_pengajuan();
            $total_pengeluaran = $this->hitung_belum_klaim();
            $total_diklaim = $this->hitung_klaim();
        }

        $pengajuan_admin = Pengajuan::with('Status')->where('divisi_id', 1)->where('status', 2)->orWhere('status','4')->get();
        $admin = $pengajuan_admin->last();
        session(['total_pengajuan' => $total_pengajuan]);
        session(['total_pengeluaran' => $total_pengeluaran]);
        session(['total_diklaim' => $total_diklaim]);
        return view('admin/main', compact('dataKas','admin','Saldo','divisi','title','laporan','startDate','endDate','total_pengajuan','total_pengeluaran','total_diklaim','filter_keluar','userList','companyList'));
    }

    public function index_filter_keluar(Request $request, $filter=null, $klaim=null, $id=null)
    {
        // filter : (1 = user, 2 = company), klaim : (1 = belum diklaim, 2 = diklaim)
        // $startDate = $request->startDate; $endDate = $request->endDate;
        $startDate = ($request->startDate)? $request->startDate  : $this->startDate; 
        $endDate = ($request->endDate) ? $request->endDate : $this->endDate;
        $company = $request->id ? $request->id : null;
        $Saldo = Saldo::findOrFail(Auth::user()->id);
        $divisi = Divisi::get();
        $filter_keluar = TRUE;
        $title = "Admin Kas Kecil";
        $laporan = FALSE;
        $userList = DB::table('user')->join('pettycash_pengajuan', 'user.id', '=', 'pettycash_pengajuan.user_id')->select('user.*')->get()->unique('id');
        $companyList = DB::table('project_company')->join('pettycash_pengeluaran', 'project_company.project_company_id', '=', 'pettycash_pengeluaran.pembebanan')->select('project_company.*')->get()->unique('project_company_id');

        $dataKas = Pengeluaran::with('pengajuan', 'Status', 'COA','Pembebanan')
            ->where(function ($query) use ($filter, $id) {
                if ($filter == 1) {
                    $query->where('user_id',$id);
                } else if ($filter == 2) {
                    $query->where('pembebanan',$id);
                }
            })
            ->where(function ($query) use ($klaim) {
                if ($klaim == 1) {
                    $query->where('status', 4);
                } else if ($klaim == 2) {
                    $query->whereIn('status', [7,8]);
                }
            })
            ->where(function ($query) use ($startDate, $endDate) {
                if ($startDate && $endDate) {
                    $query->whereBetween('tanggal', [$startDate, $endDate]);
                }
            })->get();

        if($filter==1) {
            $total_pengajuan = $this->hitung_pengajuan($id);
            $total_pengeluaran = $this->hitung_belum_klaim($id);
            $total_diklaim = $this->hitung_klaim($id);
        } else if ($filter==2) {
            $total_pengajuan = $this->hitung_pengajuan();
            $total_pengeluaran = $this->hitung_belum_klaim(null,null,null,$id);
            $total_diklaim = $this->hitung_klaim(null,null,null,$id);
        } else {
            $total_pengajuan = $this->hitung_pengajuan();
            $total_pengeluaran = $this->hitung_belum_klaim();
            $total_diklaim = $this->hitung_klaim();
        }

        return view('admin/main', compact('dataKas','Saldo','divisi','title','laporan','startDate','endDate','total_pengajuan','total_pengeluaran','total_diklaim','filter_keluar','userList','companyList'));
    }

    public function hitung_pengajuan($id=null, $startDate=null, $endDate=null, $unit=null){
        $data_pengajuan = Pengajuan::whereNotIn('status',[1,3,6])->where(function ($query) use ($id) 
        {
            if ($id) {
                $query->where('user_id', $id);
            }
        })
        ->where(function ($query) use ($startDate, $endDate) {
            if ($startDate && $endDate) {
                $query->whereBetween('tanggal', [$startDate, $endDate]);
            }
        })
        ->where(function ($query) use ($unit) {
            if ($unit) {
                $query->where('divisi_id', $unit);
            }
        })->get();
        $total_pengajuan = 0;
        foreach ($data_pengajuan as $masuk){
            $total_pengajuan = $total_pengajuan + $masuk->jumlah;
        }
        return ($total_pengajuan);
    }

    public function hitung_belum_klaim($id=null, $startDate=null, $endDate=null,$company=null, $unit=null){
        $data_pengeluaran = Pengeluaran::whereNotIn('status',[3,6,7,8])->where('deskripsi','!=',"PENGEMBALIAN SALDO PENGAJUAN")
        ->where(function ($query) use ($id) {
            if ($id) {
                $query->where('user_id', $id);
            }
        })
        ->where(function ($query) use ($startDate, $endDate) {
            if ($startDate && $endDate) {
                $query->whereBetween('tanggal', [$startDate, $endDate]);
            }
        })
        ->where(function ($query) use ($company) {
            if ($company) {
                $query->where('pembebanan', $company);
            }
        })
        ->where(function ($query) use ($unit) {
            if ($unit) {
                $query->whereIn('pemasukan',Pengajuan::select('id')->where('divisi_id',$unit)->get());
            }
        })->get();
        $total_pengeluaran = 0;
        foreach ($data_pengeluaran as $keluar){
            $total_pengeluaran = $total_pengeluaran + $keluar->jumlah;
        }

        return ($total_pengeluaran);
    }

    public function hitung_klaim($id=null, $startDate=null, $endDate=null,$company=null,$unit=null) {
        $diklaim = Pengeluaran::whereIn('status',[7,8])->where('deskripsi','!=',"PENGEMBALIAN SALDO PENGAJUAN")
        ->where(function ($query) use ($id) {
            if ($id) {
                $query->where('user_id', $id);
            }
        })
        ->where(function ($query) use ($startDate, $endDate) {
            if ($startDate && $endDate) {
                $query->whereBetween('tanggal', [$startDate, $endDate]);
            }
        })
        ->where(function ($query) use ($company) {
            if ($company) {
                $query->where('pembebanan', $company);
            }
        })
        ->where(function ($query) use ($unit) {
            if ($unit) {
                $query->whereIn('pemasukan',Pengajuan::select('id')->where('divisi_id',$unit)->get());
            }
        })->get();
        $total_diklaim = 0;
        foreach ($diklaim as $keluar){
            $total_diklaim = $total_diklaim + $keluar->jumlah;
        }
        return ($total_diklaim);
    }


    public function laporan(Request $request){
        $startDate = $request->startDate ? $request->startDate : $this->startDate;
        $endDate = $request->endDate ? $request->endDate : $this->endDate;
        $laporan = TRUE; $filter_keluar = FALSE;
        $dataKas = Pengajuan::with('Sumber','User', 'Status')->where('status',5)->whereBetween('tanggal',[$startDate,$endDate])->get();
        $divisi = Divisi::get();

        // Perhitungan sisa dan total belanja pada card
        $total_pengajuan = $this->hitung_pengajuan(null,$startDate,$endDate);
        $total_pengeluaran = $this->hitung_belum_klaim(null,$startDate,$endDate);
        $total_diklaim = $this->hitung_klaim(null,$startDate,$endDate);

        // Perhitungan sisa dan total belanja pada card
        foreach ($dataKas as $masuk) {
            $total = 0;
            $diklaim = 0;
            $data_pengeluaran = Pengeluaran::with('pengajuan')->where('pemasukan','=',$masuk->id)->whereNotIn('status',[3,6,7,8])
                                ->whereBetween('tanggal',[$startDate,$endDate])->where('deskripsi','!=',"PENGEMBALIAN SALDO PENGAJUAN")->get();
            foreach ($data_pengeluaran as $keluar){
                $total = $total + $keluar->jumlah;
            }
            $data_diklaim = Pengeluaran::with('pengajuan')->where('pemasukan','=',$masuk->id)->whereIn('status',[7,8])
                            ->whereBetween('tanggal',[$startDate,$endDate])->where('deskripsi','!=',"PENGEMBALIAN SALDO PENGAJUAN")->get();
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

        return view('admin/main', compact('dataKas', 'admin', 'divisi', 'title', 'laporan','total_pengajuan','total_pengeluaran','total_diklaim','filter_keluar','startDate','endDate'));
    }

    public function laporan_keluar(Request $request)
    {
        $title = "Laporan Kas Kecil";
        $company = Company::get(); 
        $startDate = $request->startDate ? $request->startDate : $this->startDate;
        $endDate = $request->endDate ? $request->endDate : $this->endDate;
        // $dataKas = DB::table('pettycash_pengeluaran')->select('coa',DB::raw('sum(jumlah) as total'))->groupBy('coa')->get();
        $dataKas = Pengeluaran::with('pengajuan', 'Status', 'COA','Pembebanan')->whereIn('status', [7,8])
                    ->whereBetween('tanggal',[$startDate,$endDate])
                    ->where('deskripsi','!=','PENGEMBALIAN SALDO PENGAJUAN')->orderBy('status','asc')->get();
        $Saldo = Saldo::findOrFail(Auth::user()->id);
        $totalKeluar = 0; $totalSetBKK = 0; $totalBelumSetBKK = 0;
        foreach($dataKas as $value) {
            $totalKeluar = $totalKeluar + $value->jumlah;
            if($value->status == 7 && $value->deskripsi!="PENGEMBALIAN SALDO PENGAJUAN") {
                $totalBelumSetBKK = $totalBelumSetBKK + $value->jumlah;
            } elseif ($value->status == 8 && $value->deskripsi!="PENGEMBALIAN SALDO PENGAJUAN") {
                $totalSetBKK = $totalSetBKK + $value->jumlah;
            }
        }
        (new PengeluaranController)->set_tanggal($startDate, $endDate);
        return view ('/admin/laporan_kas', compact('title','startDate','endDate','company','dataKas','Saldo','totalKeluar','totalSetBKK','totalBelumSetBKK'));
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

    public function done(Request $request)
    {
        Pengeluaran::with('User', 'pengajuan')->whereIn('id',$request->ids)->update(['status'=>'7']);
        $this->kembalikan_saldo($request->ids);
        return response()->json(true);
    }

    public function kembalikan_saldo($ids) 
    {
        $pengeluaran = Pengeluaran::with('User', 'pengajuan')->whereIn('id',$ids)->get();
        foreach ($pengeluaran as $pengeluaran) {
            $user = User::find($pengeluaran->user_id);
            if ($user->kk_access==2) {
                if ($pengeluaran->deskripsi != "PENGEMBALIAN SALDO PENGAJUAN") {
                    $saldo = Saldo::find($user->id);
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
            $pengeluaran2 = Pengeluaran::with('pengajuan')->where('pemasukan',$pengeluaran->pemasukan)->get();
            $count_pengeluaran = $pengeluaran2->filter(function($item, $key){
                return $item->status == 7;
            });
        }
        // foreach ($ids as $id) {
        //     $pengeluaran = Pengeluaran::with('User', 'pengajuan')->find($id);
        //     if ($pengeluaran->User->kk_access==2) {
        //         if ($pengeluaran->deskripsi != "PENGEMBALIAN SALDO PENGAJUAN") {
        //             $saldo = Saldo::find($pengeluaran->user_id);
        //             $saldo->saldo = $saldo->saldo + $pengeluaran->jumlah;
        //             $saldo->save();
        //             dd($saldo);
        //         } elseif ($pengeluaran->deskripsi == "PENGEMBALIAN SALDO PENGAJUAN") {
        //             $saldo_admin = Saldo::findOrFail(Auth::user()->id);
        //             $saldo_admin->saldo = $saldo_admin->saldo + $pengeluaran->jumlah;
        //             if ($pengeluaran->pengajuan->sumber == 1) { //pengajuan tunai
        //                 $saldo_admin->tunai = $saldo_admin->tunai + $pengeluaran->jumlah;
        //             } elseif ($pengeluaran->pengajuan->sumber == 2) { //pengajuan bank
        //                 $saldo_admin->bank = $saldo_admin->bank + $pengeluaran->jumlah;
        //             }
        //             $saldo_admin->save();
        //         }
        //     }
        //     $pengeluaran2 = Pengeluaran::with('pengajuan')->where('pemasukan',$pengeluaran->pemasukan)->get();
        //     $count_pengeluaran = $pengeluaran2->filter(function($item, $key){
        //         return $item->status == 7;
        //     });
        // }

        // $pengeluaran2 = Pengeluaran::with('pengajuan')->where('pemasukan',$pengeluaran->pemasukan)->get();
        // $count_pengeluaran = $pengeluaran2->filter(function($item, $key){
        //     return $item->status == 7;
        // });

        if (count($count_pengeluaran) == count($pengeluaran2)) {
            $pengeluaran->pengajuan->status = 5;
            $pengeluaran->pengajuan->save();
        } else {
            $pengeluaran->pengajuan->status = 4;
            $pengeluaran->pengajuan->save();
        }
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

    public function kas_divisi(Request $request, $laporan, $id)
    {
        $dataKas = Pengajuan::with('Sumber','User', 'Status')->where('divisi_id', $id)->get();
        $divisi = Divisi::get();
        session(['key' => $id]);
        if ($laporan == 1) {
            $laporan = FALSE;
        } else {
            $laporan = TRUE;
        }
        $title = "Admin Kas Kecil";
        $startDate = $this->startDate; $endDate = $this->endDate;
        $userList = DB::table('user')->join('pettycash_pengajuan', 'user.id', '=', 'pettycash_pengajuan.user_id')->select('user.*')->get()->unique('id');
        $companyList = DB::table('project_company')->join('pettycash_pengeluaran', 'project_company.project_company_id', '=', 'pettycash_pengeluaran.pembebanan')->select('project_company.*')->get()->unique('project_company_id');
        $filter_keluar = FALSE; 
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
        $total_pengajuan = $this->hitung_pengajuan(null,null,null,$id);
        $total_pengeluaran = $this->hitung_belum_klaim(null,null,null,null,$id);
        $total_diklaim = $this->hitung_klaim(null,null,null,null,$id);

        return view('admin/main', compact('dataKas', 'divisi', 'title', 'laporan','startDate', 'endDate', 'Saldo','total_pengajuan','total_pengeluaran','total_diklaim','userList','companyList','filter_keluar'));
    }

    public function detail_divisi(Request $request, $id)
    {
        $idPengajuan = ($id)??$request->session()->get('key');
        $pengajuan = Pengajuan::find($idPengajuan);
        $totalDiklaim = 0; $totalPengeluaran = 0;
        $dataKas = Pengeluaran::with('pengajuan', 'Status','Pembebanan','COA')->where('pemasukan','=',$id)->where('status','!=',6)->orderBy('status','asc')->get();
        foreach($dataKas as $k) {
            if ($k->deskripsi != "PENGEMBALIAN SALDO PENGAJUAN") {
                $totalPengeluaran = $totalPengeluaran + $k->jumlah;
            }
        }
        session(['key' => $id]);
        $kasTotal = Pengeluaran::with('pengajuan', 'Status')->where('pemasukan','=',$id)->whereIn('status',[7,8])->where('deskripsi','!=',"PENGEMBALIAN SALDO PENGAJUAN")->get();
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
        $dataKas = Pengeluaran::with('pengajuan', 'Status','Pembebanan','COA')->where('pemasukan','=',$idPengajuan)->where('status','!=',6)->where('pembebanan',$id)->orderBy('status','asc')->get();
        foreach($dataKas as $k) {
            $totalPengeluaran = $totalPengeluaran + $k->jumlah;
        }
        $kasTotal = Pengeluaran::with('pengajuan', 'Status')->where('pemasukan','=',$idPengajuan)->whereIn('status',[7,8])->where('pembebanan',$id)->get();
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

    public function set_bkk(Request $request)
    {
        Pengeluaran::whereIn('id',$request->ids)->update(['status'=>'8','tanggal_set_bkk'=>Carbon::now()]);
        return response()->json(true);
    }

}
