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
            $data_pengeluaran = Pengeluaran::with('User','pengajuan')->statusSetBKK()
                ->BukanPengembalianSaldo()
                ->searchByCompany($company)
                ->SearchByDateRange($startDate, $endDate)->get();
            $Company = ($company) ? Company::find($company) : null;
            $company = ($Company) ? $Company->name : null; 
            $pengajuan_klaim = Pengeluaran::with('User', 'pengajuan')->statusProgress()->SearchByDateRange($startDate, $endDate)->get();
            $pengajuan = Pengajuan::with('User')->whereNotIn('status',[1,3,6])->get();
            $data_pengajuan = $pengajuan->filter(function($item, $key){
                return $item->User->kk_access != '1';
            });
        } elseif (Auth::user()->kk_access == 2){
            $data_pengeluaran = Pengeluaran::with('User','pengajuan')->searchByUser(Auth::user()->id)
                ->statusSetBKK()
                ->BukanPengembalianSaldo()
                ->searchByCompany($company)
                ->SearchByDateRange($startDate, $endDate)->get();
            $Company = ($company) ? Company::find($company) : null;
            $company = $Company->name;
            $pengajuan_klaim = Pengeluaran::with('User', 'pengajuan')->statusProgress()->SearchByDateRange($startDate, $endDate)->get();
            $data_pengajuan = Pengajuan::with('User')->whereNotIn('status',[1,3,6])->where('user_id',Auth::user()->id)->get();
        }

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
