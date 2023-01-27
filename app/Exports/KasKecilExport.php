<?php

namespace App\Exports;

use App\Models\Pengeluaran;
use App\Models\Pengajuan;
use App\Models\Pembebanan;
use App\Models\Divisi;
use App\Models\Saldo;
use App\Models\COA;
use App\Models\Company;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Events\BeforeSheet;
use Carbon\Carbon;

class KasKecilExport implements FromView, WithHeadings, WithMapping, WithStyles
{
    use Exportable;

    protected $data;
    protected $startDate;
    protected $endDate;
    protected $company = null;

    public function __construct($startDate, $endDate, $company=null)
    {
        // $this->data = $data;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->company = $company;
    }

    // public function collection()
    // {
    //     return $this->data;
    // }

    public function view(): View
    {
        $dateNow = Carbon::now()->format('d-m-Y');
        $startDate = $this->startDate;
        $endDate = $this->endDate;
        $company = $this->company;
        if (Auth::user()->kk_access == 1) {
            if ($startDate and $endDate) {
                if ($company) {
                    $data_pengeluaran = Pengeluaran::with('User', 'pengajuan', 'Kategori')->where('pembebanan',$company)->where('status', 8)->whereBetween('tanggal_set_bkk', [$startDate, $endDate])
                                        ->where('deskripsi','!=','PENGEMBALIAN SALDO PENGAJUAN')->get();
                    $Company = Company::find($company);
                    $company = $Company->name;
                } else {
                    $data_pengeluaran = Pengeluaran::with('User', 'pengajuan', 'Kategori')->where('status', 8)->whereBetween('tanggal_set_bkk', [$startDate, $endDate])
                    ->where('deskripsi','!=','PENGEMBALIAN SALDO PENGAJUAN')->get();
                }
                $pengajuan_klaim = Pengeluaran::with('User', 'pengajuan', 'Kategori')->where('status', 4)->whereBetween('tanggal_set_bkk', [$startDate, $endDate])->get();
            } else {
                if ($company) {
                    $data_pengeluaran = Pengeluaran::with('User', 'pengajuan', 'Kategori')->where('pembebanan',$company)->where('status', 8)
                    ->where('deskripsi','!=','PENGEMBALIAN SALDO PENGAJUAN')->get();
                    $Company = Company::find($company);
                    $company = $Company->name;
                } else {
                    $data_pengeluaran = Pengeluaran::with('User', 'pengajuan', 'Kategori')->where('status', 8)
                    ->where('deskripsi','!=','PENGEMBALIAN SALDO PENGAJUAN')->get();
                }
                $pengajuan_klaim = Pengeluaran::with('User', 'pengajuan', 'Kategori')->where('status', 4)->get();
            }
            $pengajuan = Pengajuan::with('User')->where('status','!=',3)->where('status','!=',6)->where('status','!=',1)->get();
            $data_pengajuan = $pengajuan->filter(function($item, $key){
                return $item->User->kk_access != '1';
            });
        } elseif (Auth::user()->kk_access == 2){
            if ($startDate and $endDate) {
                if ($company) {
                    $data_pengeluaran = Pengeluaran::with('User', 'pengajuan', 'Kategori')->where('pembebanan',$company)->where('user_id', Auth::user()->id)->where('status','!=',6)->whereBetween('tanggal_set_bkk', [$startDate, $endDate])
                    ->where('deskripsi','!=','PENGEMBALIAN SALDO PENGAJUAN')->get();
                    $Company = Company::find($company);
                    $company = $Company->name;
                } else {
                    $data_pengeluaran = Pengeluaran::with('User', 'pengajuan', 'Kategori')->where('user_id', Auth::user()->id)->where('status','!=',6)->whereBetween('tanggal_set_bkk', [$startDate, $endDate])
                    ->where('deskripsi','!=','PENGEMBALIAN SALDO PENGAJUAN')->get();
                }
                $pengajuan_klaim = Pengeluaran::with('User', 'pengajuan', 'Kategori')->where('user_id', Auth::user()->id)->where('status', 4)->whereBetween('tanggal_set_bkk', [$startDate, $endDate])->get();
            } else {
                if ($company) {
                    $data_pengeluaran = Pengeluaran::with('User', 'pengajuan', 'Kategori')->where('pembebanan',$company)->where('user_id', Auth::user()->id)->where('status','!=',6)
                    ->where('deskripsi','!=','PENGEMBALIAN SALDO PENGAJUAN')->get();
                    $Company = Company::find($company);
                    $company = $Company->name;
                } else {
                    $data_pengeluaran = Pengeluaran::with('User', 'pengajuan', 'Kategori')->where('user_id', Auth::user()->id)->where('status','!=',6)
                    ->where('deskripsi','!=','PENGEMBALIAN SALDO PENGAJUAN')->get();
                }
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
        $data_pengeluaran->total_all = $data_pengeluaran->saldo + $data_pengeluaran->total + $data_pengeluaran->sisa + $data_pengeluaran->belum_diklaim;
        return view('export_kaskecil', compact('data_pengeluaran','startDate','endDate','dateNow','company'));
    }

    public function map($pengeluaran): array
    {
        return [
            \Carbon\Carbon::parse($pengeluaran['tanggal'])->format('d/m/Y'),
            str_ireplace('\/', '/', substr($pengeluaran['pengajuan'], 10, -3)),
            substr($pengeluaran['coa'], 10, -3),
            substr($pengeluaran['nama_coa'], 10, -3),
            $pengeluaran['user'],
            substr($pengeluaran['divisi'], 10, -3),
            $pengeluaran['tujuan'],
            $pengeluaran['deskripsi'],
            number_format($pengeluaran['jumlah'], 2, ",", "."),
            // \Carbon\Carbon::parse($pengeluaran['tanggal_respon'])->format('d/m/Y'),
            substr($pengeluaran['nama_pembebanan'], 10, -3),
        ];
    }

    public function headings(): array
    {
        return [
            'Tanggal',
            'Kode Pengajuan',
            'COA',
            'Nama COA',
            'Oleh',
            'Divisi',
            'Dibayarkan kepada',
            'Keterangan',
            'Nominal',
            // 'Tanggal Responsi',
            'Pembebanan',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $array = $sheet->getRowIterator();
        $returnData = array();
    }

    public static function beforeSheet(BeforeSheet $event)
    {
        $event->sheet->appendRows(array('test1', 'test1', 'test1', 'test1', 'test1', 'test1', 'test1', 'test1', 'test1', 'test1',), $event);
    }

    public static function afterSheet(AfterSheet $event)
    {
        $event->sheet->appendRows(array('test1', 'test1', 'test1', 'test1', 'test1', 'test1', 'test1', 'test1', 'test1', 'test1',), $event);
    }
}
