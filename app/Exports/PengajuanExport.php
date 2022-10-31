<?php

namespace App\Exports;

use App\Models\Pengajuan;
use App\Models\Sumber;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\FromQuery;

class PengajuanExport implements FromCollection, WithHeadings, WithMapping
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


    public function map($pengajuan) : array
    {
        return [
            $pengajuan['kode'],
            \Carbon\Carbon::parse($pengajuan['tanggal'])->format('d/m/Y'),
            $pengajuan['user'],
            $pengajuan['divisi'],
            $pengajuan['deskripsi'],
            number_format($pengajuan['jumlah'], 2, ",", "."),
            substr($pengajuan['nama_sumber'],17,-3),
        ];
    }


    public function headings(): array{
        return [
        'Kode',
        'Tanggal Pengajuan',
        'User',
        'Divisi',
        'Keterangan',
        'Jumlah Pengajuan', 
        'Sumber Dana'
        ];
    }



    
}