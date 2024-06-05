<?php

namespace App\Http\Controllers;

use App\Http\Controllers\PengeluaranController;
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
use App\Services\HitungPengajuanService;
use App\Services\HitungSaldoService;
use App\Services\HitungTransaksiService;
use Carbon\Carbon;
use Dompdf\Dompdf;


class PengajuanController extends Controller
{
    public $startDate;
    public $endDate;

    public function __construct() {
        $this->startDate = Carbon::now()->startOfMonth('d-m-Y');
        $this->endDate = Carbon::now()->endOfMonth('d-m-Y');
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
        $title = "Kas Kecil";
        $dataKas = Pengajuan::with('Status')->searchByUser(Auth::user()->id)->get();
        $Saldo = (new HitungSaldoService)->hitung_saldo_user(Auth::user()->id);
        $saldoAwal = (new HitungPengajuanService)->hitung_pengajuan(Auth::user()->id);
        $totalPengeluaran = (new HitungTransaksiService)->hitung_belum_klaim(Auth::user()->id);
        $totalKlaim = (new HitungTransaksiService)->hitung_klaim(Auth::user()->id); 
        
        return view ('main', compact('dataKas','title', 'saldoAwal','Saldo','laporan','totalPengeluaran','totalKlaim'));
    }

    public function laporan(Request $request){
        $laporan = TRUE;
        $title = "Laporan Pengajuan Kas Kecil";
        $startDate = ($request->startDate) ? $request->startDate : $this->startDate;
        $endDate = ($request->endDate) ? $request->endDate : $this->endDate;
        session(['startDate' => $startDate]); session(['endDate' => $endDate]);
        $dataKas = Pengajuan::with('Status')->where('user_id', Auth::user()->id)->searchByDateRange($startDate, $endDate)->get();
        $Saldo = (new HitungSaldoService)->hitung_saldo_user(Auth::user()->id);
        $saldoAwal = (new HitungPengajuanService)->hitung_pengajuan(Auth::user()->id);
        $totalPengeluaran = (new HitungTransaksiService)->hitung_belum_klaim(Auth::user()->id);
        $totalKlaim = (new HitungTransaksiService)->hitung_klaim(Auth::user()->id);
        if (Auth::user()->kk_access == '1') {
            return view ('admin/main', compact('dataKas','title', 'Saldo'));
        } else {
            return view ('main', compact('dataKas','title', 'saldoAwal','Saldo','laporan', 'startDate', 'endDate','totalPengeluaran','totalKlaim'));
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
        $company = $request->session()->get('company');
        $project = $request->session()->get('project');
        $Company = Company::get();
        $Project = Project::where('project_id',$project)->get();

        return view('form_pengajuan', compact('Company','Project'));
    }

    public function save(Request $request)
    {
        $pengajuan = new Pengajuan;
        $pengajuan->tanggal = $request->tanggal;
        $pengajuan->deskripsi = $request->deskripsi;
        $pengajuan->tanggal = $request->tanggal;
        $pengajuan->sumber = $request->sumber;
        $pengajuan->status = "1";
        $pengajuan->user_id = Auth::user()->id;
        $pengajuan->divisi_id = Auth::user()->level;
        $pengajuan->company = $request->company;
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

    public function pengembalian_saldo($id)
    {
        $pengajuan = Pengajuan::find($id)->update(['status'=>9]);
        return redirect('home');
    }

    // public function filter(Request $request, $id) {
    //     //$id = 1 : index, $id = 2 : laporan
    //     $this->startDate = $request->startDate;
    //     $this->endDate = $request->endDate;
    //     session(['startDate' => $this->startDate, 'endDate' => $this->endDate]);
    //     $pengajuan_admin = Pengajuan::with('Status')->where('divisi_id', 1)->statusProgressAndApproved()->get();
    //     $admin = $pengajuan_admin->last();
    //     //Data pengajuan
    //     if ($id == 1) {
    //         $tanggal = Pengajuan::with('Sumber','Divisi', 'Status')->statusProgressAndApproved()->searchByDateRange($this->startDate,$this->endDate)->get();
    //     } elseif ($id == 2) {
    //         if (Auth::user()->kk_access == 1) {
    //             $tanggal = Pengajuan::with('Sumber','Divisi', 'Status')->isDone()->searchByDateRange($this->startDate,$this->endDate)->get();
    //         } elseif (Auth::user()->kk_access == 2) {
    //             $tanggal = Pengajuan::with('Sumber','Divisi', 'Status')->isDone()->where('user_id', Auth::user()->id)->searchByDateRange($this->startDate,$this->endDate)->get();
    //         }
    //     }
    //     //Untuk perhitungan saldo
    //     $data_pengajuan = Pengajuan::whereNotIn('status',[1,3,6])->searchByDateRange($this->startDate,$this->endDate)->get();
    //     $data_pengajuan = $data_pengajuan->filter(function($item, $key){
    //         return $item->User->kk_access != '1';
    //     });
    //     $data_pengeluaran = Pengeluaran::whereNotIn('status', [1,3,6])->where('deskripsi','!=',"PENGEMBALIAN SALDO PENGAJUAN")->searchByDateRange($this->startDate,$this->endDate)->get();
    //     $data_pengeluaran = $data_pengeluaran->filter(function($item, $key){
    //         return $item->User->kk_access != '1';
    //     });
    //     $divisi = Divisi::get();

    //     // Ambil data
    //     if ($id == 1) {
    //         $title = "Admin Kas Kecil";
    //         $laporan = FALSE;
    //         $saldo = Saldo::findOrFail(Auth::user()->id);
    //         return view('admin/main',['Saldo'=>$saldo,'dataKas'=>$tanggal, 'startDate'=>$this->startDate, 'endDate'=>$this->endDate, 'title'=>$title, 'laporan'=>$laporan, 'admin'=>$admin, 'divisi'=>$divisi]);
    //     } elseif ($id == 2) {
    //         $title = "Laporan Pengajuan";
    //         $laporan = TRUE;
    //         if (Auth::user()->kk_access == 1) {
    //             return view('admin/main',['dataKas'=>$tanggal, 'startDate'=>$this->startDate, 'endDate'=>$this->endDate, 'title'=>$title, 'laporan'=>$laporan, 'admin'=>$admin, 'divisi'=>$divisi]);
    //         } elseif (Auth::user()->kk_access == 2) {
    //             $saldo = Saldo::findOrFail(Auth::user()->id);
    //             return view ('main', ['dataKas' => $tanggal],['title'=>$title, 'Saldo'=>$saldo,'laporan'=>$laporan, 'startDate' => $this->startDate, 'endDate' => $this->endDate]);
    //         }
    //     }
    // }

    // public function export(Request $request) {
    //     $startDate = $request->session()->get('startDate');
    //     $endDate = $request->session()->get('endDate');
    //     if (Auth::user()->kk_access==1) {
    //         if ($startDate && $endDate) {
    //             $data_pengajuan = Pengajuan::with('User','Divisi','Sumber')->isDone()->searchByDateRange($startDate,$endDate)->get();
    //         } else {
    //             $data_pengajuan = Pengajuan::with('User','Divisi','Sumber')->isDone()->get();
    //         }
    //     } elseif (Auth::user()->kk_access==2) {
    //         if ($startDate && $endDate) {
    //             $data_pengajuan = Pengajuan::with('User','Divisi','Sumber')->where('user_id', Auth::user()->id)->isDone()->searchByDateRange($startDate,$endDate)->get();
    //         } else {
    //             $data_pengajuan = Pengajuan::with('User','Divisi','Sumber')->where('user_id', Auth::user()->id)->isDone()->get();
    //         }
    //     }

    //     for ($i = 0; $i<count($data_pengajuan); $i++) {
    //         $data_pengajuan[$i]->nama_sumber = Sumber::select('sumber_dana')->where('id',$data_pengajuan[$i]->sumber)->get();
    //         $data_pengajuan[$i]->user = $data_pengajuan[$i]->User->username;
    //     }
    //     if (!$data_pengajuan) {
    //         return false;
    //     }
    
    //     return (new PengajuanExport($data_pengajuan))->download("Pengajuan_Kas_Kecil" . ".xlsx");
    // }

    public function export_pdf($id) {
        $data = Pengajuan::with('Divisi','User')->findOrFail($id);
        $data->today = Carbon::now()->isoFormat('dddd, D MMMM Y');

        $html = view('printpdf',['data'=>$data]);

        // instantiate and use the dompdf class
        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        // (Optional) Setup the paper size and orientation
        $dompdf->setPaper('A5', 'landscape');
        $options = $dompdf->getOptions();
        $options->setDefaultFont('Times New Roman');
        $dompdf->setOptions($options);
        // Render the HTML as PDF
        $dompdf->render();
        // Output the generated PDF to Browser
        $dompdf->stream('Pengajuan Kas Kecil_'.$data->User->username.'_'.Carbon::now()->isoFormat('D-M-Y'));
    }
}
