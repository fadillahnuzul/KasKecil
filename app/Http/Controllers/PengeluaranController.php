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
use App\Exports\KasKecilExport;
use Alert;
use Carbon\Carbon;

class PengeluaranController extends Controller
{
    public $startDate;
    public $endDate;

    public function __construct()
    {
        $this->startDate = Carbon::now()->startOfMonth()->format('Y-m-d');
        $this->endDate = Carbon::now()->endOfMonth('Y-m-d');
    }

    public function index(Request $request, $id)
    {
        $button_kas = TRUE;
        $title = "Detail Pengajuan";
        $kas = Pengeluaran::with('Pembebanan', 'Status', 'Kategori')->where('pemasukan', '=', $id)->get();
        session(['key' => $id]);
        $total = $kas->sum('jumlah');
        $saldo = Saldo::find(Auth::id());

        return view('detail_pengajuan', ['dataKas' => $kas], ['title' => $title, 'button_kas' => $button_kas, 'saldo' => $saldo]);
    }

    public function laporan()
    {
        $startDate = $this->startDate;
        $endDate = $this->endDate;
        $button_kas = FALSE;
        $data_pengeluaran = Pengeluaran::with('pengajuan', 'Status', 'Kategori', 'Pembebanan')->where('user_id', Auth::user()->id)->where('status', 5)->get();
        $title = "Laporan Pengeluaran Kas Kecil";
        // dd(view('detail_pengajuan', ['dataKas' => $data_pengeluaran], ['title' => $title, 'button_kas' => $button_kas, 'startDate' => $startDate, 'endDate' => $endDate]));
        $saldo = Saldo::find(Auth::id());
        
        return view('detail_pengajuan', ['dataKas' => $data_pengeluaran], ['title' => $title, 'button_kas' => $button_kas, 'startDate' => $startDate, 'endDate' => $endDate, 'saldo' => $saldo]);
    }

    public function create()
    {
        $kategori = Kategori::get();
        $pembebanan = Pembebanan::get();

        return view('form_kas', ['kategori' => $kategori, 'pembebanan' => $pembebanan]);
    }

    public function save(Request $request)
    {
        $kas = new Pengeluaran;
        $kas->tanggal = $request->tanggal;
        $kas->deskripsi = $request->deskripsi;
        $kas->kategori = $request->kategori;
        $kas->pembebanan = $request->pembebanan;
        $kas->status = "4";
        $kas->pemasukan = $request->session()->get('key');
        $kas->user_id = Auth::user()->id;
        $kas->divisi_id = Auth::user()->level;
        //Kas admin
        if (Auth::user()->access == 'admin') {
            $tunai = preg_replace("/[^0-9]/", "", $request->tunai);
            $bank = preg_replace("/[^0-9]/", "", $request->bank);
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
        $kategori = Kategori::where('id', '!=', $kas->kategori)->get();
        $pembebanan = Pembebanan::where('id', '!=', $kas->pembebanan)->get();

        return view('form-edit', ['kas' => $kas, 'kategori' => $kategori, 'pembebanan' => $pembebanan]);
    }

    public function update(Request $request, $id)
    {
        $kas = Pengeluaran::with('pengajuan', 'Divisi')->findOrFail($id);
        $kas_input = preg_replace("/[^0-9]/", "", $request->jumlah);
        $kas->tanggal = $request->tanggal;
        $kas->deskripsi = $request->deskripsi;
        $kas->kategori = $request->kategori;
        $kas->pembebanan = $request->pembebanan;
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
        $this->startDate = $request->startDate;
        $this->endDate = $request->endDate;
        $data_pengeluaran = Pengeluaran::with('pengajuan', 'Status', 'kategori')->where('status', 5)->where('tanggal', '>=', $this->startDate)->where('tanggal', '<=', $this->endDate)->get();
        $title = "Laporan Pengeluaran Kas Kecil";
        $kategori = Kategori::with('pengeluaran')->get();

        return view(
            '/admin/laporan_kas',
            ['kategori' => $kategori, 'title' => $title, 'startDate' => $this->startDate, 'endDate' => $this->endDate],
            ['dataKas' => $data_pengeluaran]
        );
    }

    public function export(Request $request)
    {
        $startDate = $request->session()->get('startDate');
        $endDate = $request->session()->get('endDate');
        if ($startDate and $endDate) {
            $data_pengeluaran = Pengeluaran::with('User', 'pengajuan', 'Kategori')->where('status', 5)->where('tanggal', '>=', $startDate)->where('tanggal', '<=', $endDate)->get();
        } else {
            $data_pengeluaran = Pengeluaran::with('User', 'pengajuan', 'Kategori')->where('status', 5)->get();
        }
        for ($i = 0; $i < count($data_pengeluaran); $i++) {
            $data_pengeluaran[$i]->pengajuan = Pengajuan::select('kode')->where('id', $data_pengeluaran[$i]->pemasukan)->get();
            $data_pengeluaran[$i]->nama_kategori = Kategori::select('nama_kategori')->where('id', $data_pengeluaran[$i]->kategori)->get();
            $data_pengeluaran[$i]->nama_pembebanan = Pembebanan::select('nama_pembebanan')->where('id', $data_pengeluaran[$i]->pembebanan)->get();
            $data_pengeluaran[$i]->divisi = Divisi::select('name')->where('id', $data_pengeluaran[$i]->User->level)->get();
            $data_pengeluaran[$i]->user = $data_pengeluaran[$i]->User->username;
        }

        if (!$data_pengeluaran) {
            return false;
        }
        return (new KasKecilExport($data_pengeluaran))->download("Laporan_Kas_Kecil" . ".xlsx");
    }
}
