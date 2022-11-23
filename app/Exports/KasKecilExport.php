<?php

namespace App\Exports;

use App\Models\Pengeluaran;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithMapping;


class KasKecilExport implements FromCollection, WithHeadings, WithMapping
{
    use Exportable;

    protected $data;
    protected $startDate;
    protected $endDate; 

    public function __construct($data = NULL, $startDate = NULL, $endDate = NULL)
    {
        $this->data = $data;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    } 

    public function collection()
    {
        return $this->data;
    }

    public function map($pengeluaran) : array
    {
        return [
            \Carbon\Carbon::parse($pengeluaran['tanggal'])->format('d/m/Y'),
            substr($pengeluaran['coa'],10,-3),
            $pengeluaran['user'],
            substr($pengeluaran['divisi'],10,-3),
            $pengeluaran['deskripsi'],
            number_format($pengeluaran['jumlah'], 2, ",", "."),
            str_ireplace('\/','/',substr($pengeluaran['pengajuan'],10,-3)),
            \Carbon\Carbon::parse($pengeluaran['tanggal_respon'])->format('d/m/Y'),
            substr($pengeluaran['nama_pembebanan'],10,-3),
        ];
    }

    public function headings(): array{
        return [
        'Tanggal',
        'COA',
        'User',
        'Divisi',
        'Keterangan',
        'Nominal', 
        'Kode Pengajuan',
        'Tanggal Responsi',
        'Pembebanan',
        ];
    }

    
}
