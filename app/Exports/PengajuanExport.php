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
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PengajuanExport implements FromCollection, WithHeadings, WithMapping, WithStyles
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
            $pengajuan['jumlah'],
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

    public function styles(Worksheet $sheet)
    {
        $array = $sheet->getRowIterator();
        $returnData = array();

        foreach ($array as $key => $value) {
            $cell = $sheet->getCell("D" . $key);

            if ($key == 1) {
                $returnData[$key] =  ['font' => ['bold' => true]];
            }
        }
        return $returnData;
    }

    
}