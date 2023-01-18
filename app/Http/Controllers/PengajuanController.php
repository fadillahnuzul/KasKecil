<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Pengajuan;
use App\Models\Pengeluaran;
use App\Models\Divisi;
use App\Models\Sumber;
use App\Models\Saldo;
use App\Models\COA;
use App\Models\Company;
use App\Models\Project;
use App\Exports\PengajuanExport;
use Maatwebsite\Excel\Facades\Excel;
use Alert;
use Carbon\Carbon;
use Dompdf\Dompdf;


class PengajuanController extends Controller
{
    public $startDate;
    public $endDate;

    public function __construct() {
        $this->startDate = Carbon::now()->startOfMonth()->format('Y-m-d');
        $this->endDate = Carbon::now()->endOfMonth('Y-m-d');
    }

    public function welcome() {
        $Company = Company::get();
        return view('welcome_user',compact('Company'));
    }

    public function getCompany(Request $request){
        session(['company' => $request->company]);
        session(['project' => $request->project]);

        return redirect('home');
    }

    public function index(Request $request){
        $laporan = FALSE;
        // $company = $request->session()->get('company');
        // $project = $request->session()->get('project');
        $title = "Kas Kecil";
        $data_pengajuan = Pengajuan::with('Status')->where('user_id', Auth::user()->id)->get();
        //Menghitung total belanja, sisa
        foreach ($data_pengajuan as $masuk) {
            $total = 0;
            $diklaim = 0;
            $data_pengeluaran = Pengeluaran::with('pengajuan')->where('pemasukan','=',$masuk->id)->where('status','!=',6)->where('deskripsi','!=',"PENGEMBALIAN SALDO PENGAJUAN")->get();
            foreach ($data_pengeluaran as $keluar){
                $total = $total + $keluar->jumlah;
            }
            $data_diklaim = Pengeluaran::with('pengajuan')->where('pemasukan','=',$masuk->id)->whereIn('status',[7,8])->where('deskripsi','!=',"PENGEMBALIAN SALDO PENGAJUAN")->get();
            foreach ($data_diklaim as $keluar){
                $diklaim = $diklaim + $keluar->jumlah;
            }
            $masuk->total_belanja = $total;
            $masuk->diklaim = $diklaim;
            $masuk->sisa = $masuk->jumlah - $masuk->total_belanja;
        }

        $saldo = Saldo::findOrFail(Auth::user()->id);
        $pengajuan_admin = Pengajuan::with('Status')->where('divisi_id', 1)->where('status', 2)->orWhere('status', '4')->get();
        $admin = $pengajuan_admin->last();
        // dd($admin);

        
        return view ('main', ['dataKas' => $data_pengajuan, 'admin' => $admin],['title'=>$title, 'Saldo'=>$saldo, 'laporan'=>$laporan]);
    }

    public function laporan(Request $request){
        $laporan = TRUE;
        $company = $request->session()->get('company');
        $project = $request->session()->get('project');
        $title = "Laporan Pengajuan Kas Kecil";
        $data_pengajuan = Pengajuan::with('Status')->where('user_id', Auth::user()->id)->where('status', 5)->where('company',$company)->where('project',$project)->get();
        foreach ($data_pengajuan as $masuk) {
            $total = 0;
            $diklaim = 0;
            $data_pengeluaran = Pengeluaran::with('pengajuan')->where('pemasukan','=',$masuk->id)->where('status','!=',6)->where('deskripsi','!=',"PENGEMBALIAN SALDO PENGAJUAN")->get();
            foreach ($data_pengeluaran as $keluar){
                $total = $total + $keluar->jumlah;
            }
            $data_diklaim = Pengeluaran::with('pengajuan')->where('pemasukan','=',$masuk->id)->whereIn('status',[7,8])->where('deskripsi','!=',"PENGEMBALIAN SALDO PENGAJUAN")->get();
            foreach ($data_diklaim as $keluar){
                $diklaim = $diklaim + $keluar->jumlah;
            }
            $masuk->total_belanja = $total;
            $masuk->diklaim = $diklaim;
            $masuk->sisa = $masuk->jumlah - $masuk->total_belanja;
        }
        $saldo = Saldo::findOrFail(Auth::user()->id);
        if (Auth::user()->kk_access == '1') {
            return view ('admin/main', ['dataKas' => $data_pengajuan],['title'=>$title, 'Saldo'=>$saldo]);
        } else {
            return view ('main', ['dataKas' => $data_pengajuan],['title'=>$title, 'Saldo'=>$saldo,'laporan'=>$laporan, 'startDate' => $this->startDate, 'endDate' => $this->endDate]);
        }
        
    }

    //Memilih project
    public function project($id)
    {
        $project = Project::where('project_company_id',$id)->pluck('name','project_id');
        return json_encode($project);
    }

    public function create(Request $request)
    {
        // if (Auth::user()->saldo == 0) {
        //     return view('form_pengajuan');
        // } else {
        //     Alert::error('Pengajuan gagal', 'Selesaikan pengajuan sebelumnya');
        //     return redirect('home');
        // }
        $company = $request->session()->get('company');
        $project = $request->session()->get('project');
        $Company = Company::where('project_company_id',$company)->get();
        $Project = Project::where('project_id',$project)->get();
        // $Coa = COA::where('status','!=',0)->get();
        return view('form_pengajuan', compact('Company','Project'));
    }

    public function save(Request $request)
    {
        $pengajuan = new Pengajuan;
        $pengajuan->tanggal = $request->tanggal;
        $pengajuan->deskripsi = $request->deskripsi;
        $pengajuan->tanggal = $request->tanggal;
        $pengajuan->sumber = $request->sumber;
        // $pengajuan->coa = $request->coa;
        // $pengajuan->company = $request->company;
        // $pengajuan->project = $request->project;
        $pengajuan->status = "1";
        $pengajuan->user_id = Auth::user()->id;
        $pengajuan->divisi_id = Auth::user()->level;
        $pengajuan->jumlah = preg_replace("/[^0-9]/","",$request->jumlah);
        //mengambil nama divisi untuk generate kode pengajuan
        $divisi = Divisi::find($pengajuan->divisi_id);
        $pengajuan->divisi = $divisi->name;
        //simpan
        $pengajuan->save();

        return redirect('home');
    }

    public function detail(Request $request, $id)
    {
        $data_pengajuan = [];
        $data_pengajuan = Pengajuan::with('Status')->findOrFail($id);
        return view ('detail_pengajuan', ['dataKas' => $data_pengajuan]);
    }

    public function filter(Request $request, $id) {
        $this->startDate = $request->startDate;
        $this->endDate = $request->endDate;
        session(['startDate' => $this->startDate, 'endDate' => $this->endDate]);
        $pengajuan_admin = Pengajuan::with('Status')->where('divisi_id', 1)->where('status', 2)->orWhere('status', '4')->get();
        $admin = $pengajuan_admin->last();
        //Data pengajuan
        if ($id == 1) {
            $tanggal = Pengajuan::with('Sumber','Divisi', 'Status')->where('status','!=',5)->whereBetween('tanggal',[$this->startDate,$this->endDate])->get();
        } elseif ($id == 2) {
            if (Auth::user()->kk_access == 1) {
                $tanggal = Pengajuan::with('Sumber','Divisi', 'Status')->where('status',5)->whereBetween('tanggal',[$this->startDate,$this->endDate])->get();
            } elseif (Auth::user()->kk_access == 2) {
                $tanggal = Pengajuan::with('Sumber','Divisi', 'Status')->where('status',5)->where('user_id', Auth::user()->id)->whereBetween('tanggal',[$this->startDate,$this->endDate])->get();
            }
        }
        //Untuk perhitungan saldo
        $data_pengajuan = Pengajuan::whereNotIn('status',[1,3,6])->whereBetween('tanggal',[$this->startDate,$this->endDate])->get();
        $data_pengajuan = $data_pengajuan->filter(function($item, $key){
            return $item->User->kk_access != '1';
        });
        $data_pengeluaran = Pengeluaran::whereNotIn('status', [1,3,6])->where('deskripsi','!=',"PENGEMBALIAN SALDO PENGAJUAN")->whereBetween('tanggal',[$this->startDate,$this->endDate])->get();
        $data_pengeluaran = $data_pengeluaran->filter(function($item, $key){
            return $item->User->kk_access != '1';
        });
        $divisi = Divisi::get();
        // Perhitungan sisa dan total belanja
        $total_masuk = 0;
        foreach ($tanggal as $masuk){
            $total_masuk = $total_masuk + $masuk->jumlah;
        }
        $total_pengajuan = $total_masuk;
        $total_keluar = 0;
        //Menghitung total belanja, sisa
        foreach ($tanggal as $masuk) {
            $total_keluar_pengajuan = 0;
            $total = 0; $diklaim = 0;
            $data_pengeluaran = Pengeluaran::with('pengajuan')->where('pemasukan','=',$masuk->id)->where('status','!=',6)->where('deskripsi','!=',"PENGEMBALIAN SALDO PENGAJUAN")->get();
            foreach ($data_pengeluaran as $keluar){
                $total = $total + $keluar->jumlah;
                $total_keluar_pengajuan = $total_keluar_pengajuan + $keluar->jumlah;
            }
            $data_diklaim = Pengeluaran::with('pengajuan')->where('pemasukan','=',$masuk->id)->whereIn('status',[7,8])->where('deskripsi','!=',"PENGEMBALIAN SALDO PENGAJUAN")->get();
            foreach ($data_diklaim as $keluar){
                $diklaim = $diklaim + $keluar->jumlah;
            }
            $total_keluar = $total_keluar + $total_keluar_pengajuan;
            $masuk->total_belanja = $total;
            $masuk->sisa = $masuk->jumlah - $masuk->total_belanja;
            $masuk->diklaim = $diklaim;
        }
        $total_pengeluaran = $total_keluar;
        $sisa = $total_pengajuan - $total_pengeluaran;
        // Ambil data
        if ($id == 1) {
            $title = "Admin Kas Kecil";
            $laporan = FALSE;
            $saldo = Saldo::findOrFail(Auth::user()->id);
            return view('admin/main',['Saldo'=>$saldo,'dataKas'=>$tanggal, 'startDate'=>$this->startDate, 'endDate'=>$this->endDate, 'title'=>$title, 'laporan'=>$laporan, 'admin'=>$admin, 'divisi'=>$divisi],['total_pengajuan'=>$total_pengajuan,'total_pengeluaran'=>$total_pengeluaran,'sisa'=>$sisa]);
        } elseif ($id == 2) {
            $title = "Laporan Pengajuan";
            $laporan = TRUE;
            if (Auth::user()->kk_access == 1) {
                return view('admin/main',['dataKas'=>$tanggal, 'startDate'=>$this->startDate, 'endDate'=>$this->endDate, 'title'=>$title, 'laporan'=>$laporan, 'admin'=>$admin, 'divisi'=>$divisi],['total_pengajuan'=>$total_pengajuan,'total_pengeluaran'=>$total_pengeluaran,'sisa'=>$sisa]);
            } elseif (Auth::user()->kk_access == 2) {
                $saldo = Saldo::findOrFail(Auth::user()->id);
                return view ('main', ['dataKas' => $tanggal],['title'=>$title, 'Saldo'=>$saldo,'laporan'=>$laporan, 'startDate' => $this->startDate, 'endDate' => $this->endDate]);
            }
        }
    }

    public function export(Request $request) {
        $startDate = $request->session()->get('startDate');
        $endDate = $request->session()->get('endDate');
        if (Auth::user()->kk_access==1) {
            if ($startDate AND $endDate) {
                $data_pengajuan = Pengajuan::with('User','Divisi','Sumber')->where('status',5)->where('tanggal','>=',$startDate)->where('tanggal','<=',$endDate)->get();
            } else {
                $data_pengajuan = Pengajuan::with('User','Divisi','Sumber')->where('status', 5)->get();
            }
        } elseif (Auth::user()->kk_access==2) {
            if ($startDate AND $endDate) {
                $data_pengajuan = Pengajuan::with('User','Divisi','Sumber')->where('user_id', Auth::user()->id)->where('status',5)->where('tanggal','>=',$startDate)->where('tanggal','<=',$endDate)->get();
            } else {
                $data_pengajuan = Pengajuan::with('User','Divisi','Sumber')->where('user_id', Auth::user()->id)->where('status', 5)->get();
            }
        }

        for ($i = 0; $i<count($data_pengajuan); $i++) {
            $data_pengajuan[$i]->nama_sumber = Sumber::select('sumber_dana')->where('id',$data_pengajuan[$i]->sumber)->get();
            $data_pengajuan[$i]->user = $data_pengajuan[$i]->User->username;
        }
        if (!$data_pengajuan) {
            return false;
        }
    
        return (new PengajuanExport($data_pengajuan))->download("Pengajuan_Kas_Kecil" . ".xlsx");
    }

    public function export_pdf(Request $request) {
        $data = Pengajuan::with('Divisi')->findOrFail($request->modal_id);
        $data->pengaju = $request->pengaju;
        $data->penerima = $request->penerima;
        $data->today = Carbon::now()->isoFormat('dddd, D MMMM Y');

        $html = view('printpdf',['data'=>$data]);

        // instantiate and use the dompdf class
        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        // (Optional) Setup the paper size and orientation
        $dompdf->setPaper('A4', 'portrait');
        $options = $dompdf->getOptions();
        $options->setDefaultFont('Times New Roman');
        $dompdf->setOptions($options);
        // Render the HTML as PDF
        $dompdf->render();
        // Output the generated PDF to Browser
        $dompdf->stream('Pengajuan Kas Kecil');
    }
}
