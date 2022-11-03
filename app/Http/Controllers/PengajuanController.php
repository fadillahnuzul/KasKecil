<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Pengajuan;
use App\Models\Pengeluaran;
use App\Models\Divisi;
use App\Models\Sumber;
use App\Models\Saldo;
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

    public function index(){
        $title = "Kas Kecil";
        $data_pengajuan = Pengajuan::with('Status')->where('user_id', Auth::user()->id)->where('status','!=','5')->get();
        // dd($data_pengajuan);
        //Menghitung total belanja, sisa
        foreach ($data_pengajuan as $masuk) {
            $total = 0;
            $data_pengeluaran = Pengeluaran::with('pengajuan')->where('pemasukan','=',$masuk->id)->where('status','!=',6)->get();
            foreach ($data_pengeluaran as $keluar){
                $total = $total + $keluar->jumlah;
            }
            $masuk->total_belanja = $total;
            $masuk->sisa = $masuk->jumlah - $masuk->total_belanja;
        }

        $saldo = Saldo::findOrFail(Auth::user()->id);
        $pengajuan_admin = Pengajuan::with('Status')->where('divisi_id', 1)->where('status', 2)->orWhere('status', '4')->get();
        $admin = $pengajuan_admin->last();
        // dd($admin);

        return view ('main', ['dataKas' => $data_pengajuan, 'admin' => $admin],['title'=>$title, 'Saldo'=>$saldo]);
    }

    public function laporan(){
        $title = "Laporan Pengajuan Kas Kecil";
        $data_pengajuan = Pengajuan::with('Status')->where('user_id', Auth::user()->id)->where('status', 5)->get();
        foreach ($data_pengajuan as $masuk) {
            $total = 0;
            $data_pengeluaran = Pengeluaran::with('pengajuan')->where('pemasukan','=',$masuk->id)->where('status','!=',6)->get();
            foreach ($data_pengeluaran as $keluar){
                $total = $total + $keluar->jumlah;
            }
            $masuk->total_belanja = $total;
            $masuk->sisa = $masuk->jumlah - $masuk->total_belanja;
        }
        $saldo = Saldo::findOrFail(Auth::user()->id);
        if (Auth::user()->access == 'admin') {
            return view ('admin/main', ['dataKas' => $data_pengajuan],['title'=>$title, 'Saldo'=>$saldo]);
        } else {
            return view ('main', ['dataKas' => $data_pengajuan],['title'=>$title, 'Saldo'=>$saldo]);
        }
        
    }

    public function create()
    {
        // if (Auth::user()->saldo == 0) {
        //     return view('form_pengajuan');
        // } else {
        //     Alert::error('Pengajuan gagal', 'Selesaikan pengajuan sebelumnya');
        //     return redirect('home');
        // }
        return view('form_pengajuan');
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
        $data_pengajuan = Pengajuan::where('status','!=', 1)->where('status','!=', 3)->get();
        $data_pengeluaran = Pengeluaran::where('status','!=', 1)->where('status','!=', 3)->get();
        $divisi = Divisi::get();
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

        if ($id == 1) {
            $tanggal = Pengajuan::with('Sumber','Divisi', 'Status')->where('status','!=',5)->where('tanggal','>=',$this->startDate)->where('tanggal','<=',$this->endDate)->get();
            $title = "Admin Kas Kecil";
            $laporan = FALSE;
            return view('admin/main',['dataKas'=>$tanggal, 'startDate'=>$this->startDate, 'endDate'=>$this->endDate, 'title'=>$title, 'laporan'=>$laporan, 'admin'=>$admin, 'divisi'=>$divisi]);
        } elseif ($id == 2) {
            $tanggal = Pengajuan::with('Sumber','Divisi', 'Status')->where('status',5)->where('tanggal','>=',$this->startDate)->where('tanggal','<=',$this->endDate)->get();
            $title = "Laporan Pengajuan";
            $laporan = TRUE;
            return view('admin/main',['dataKas'=>$tanggal, 'startDate'=>$this->startDate, 'endDate'=>$this->endDate, 'title'=>$title, 'laporan'=>$laporan, 'admin'=>$admin, 'divisi'=>$divisi],['total_pengajuan'=>$total_pengajuan,'total_pengeluaran'=>$total_pengeluaran,'sisa'=>$sisa]);
        }
    }

    public function export(Request $request) {
        $startDate = $request->session()->get('startDate');
        $endDate = $request->session()->get('endDate');
        if ($startDate AND $endDate) {
            $data_pengajuan = Pengajuan::with('User','Divisi','Sumber')->where('status',5)->where('tanggal','>=',$startDate)->where('tanggal','<=',$endDate)->get();
        } else {
            $data_pengajuan = Pengajuan::with('User','Divisi','Sumber')->where('status', 5)->get();
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
