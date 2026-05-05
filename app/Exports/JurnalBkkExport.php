<?php

namespace App\Exports;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class JurnalBkkExport implements FromCollection, ShouldAutoSize, WithEvents
{
    protected $dataJurnal, $companyName, $startDate, $endDate, $saldoAwal;

    public function __construct($dataJurnal, $companyName, $startDate, $endDate, $saldoAwal)
    {
        $this->dataJurnal = $dataJurnal;
        $this->companyName = $companyName;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->saldoAwal = $saldoAwal;
    }

    public function collection()
    {
        $rows = collect();
        $saldo = $this->saldoAwal;

        // HEADER
        $rows->push([$this->companyName]);
        $rows->push(['Mutasi Kas Kecil']);
        $rows->push(['PERIODE ' . Carbon::parse($this->startDate)->format('d-m-Y') . ' s/d ' . Carbon::parse($this->endDate)->format('d-m-Y')]);
        $rows->push([]);

        // HEADER TABLE (PERHATIKAN STRUKTUR KOLOM)
        $rows->push(['Tanggal', 'Keterangan', 'Debet', '', 'Kredit', 'Saldo']);

        // SALDO AWAL
        $rows->push(['', 'Saldo Awal', '', '', '', $saldo]);

        foreach ($this->dataJurnal as $item) {

            if ($item->jenis == 0) {
                $debet = $item->nominal;
                $kredit = '';
                $saldo += $item->nominal;
            } elseif ($item->jenis == 1) {
                $debet = '';
                $kredit = $item->nominal;
                $saldo -= $item->nominal;
            }

            $rows->push([
                Carbon::parse($item->tanggal)->translatedFormat('d-M-y'),
                $item->keterangan,
                $debet,
                '',
                $kredit,
                $saldo,
            ]);
        }

        return $rows;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {

                $sheet = $event->sheet->getDelegate();
                $lastRow = $sheet->getHighestRow();

                // ======================
                // MERGE HEADER ATAS
                // ======================
                $sheet->mergeCells('A1:F1');
                $sheet->mergeCells('A2:F2');
                $sheet->mergeCells('A3:F3');

                // ======================
                // MERGE KOLOM DEBET
                // ======================
                $sheet->mergeCells('C5:D5');

                // ======================
                // STYLE HEADER ATAS
                // ======================
                $sheet->getStyle('A1:A3')->getFont()->setBold(true)->setSize(12);

                // ======================
                // HEADER TABLE
                // ======================
                $sheet->getStyle('A4:F4')->getFont()->setBold(true);
                $sheet->getStyle('A4:F4')->getAlignment()->setHorizontal('center');

                // BACKGROUND HEADER
                $sheet->getStyle('A4:F4')->getFill()
                    ->setFillType('solid')
                    ->getStartColor()->setARGB('D9D9D9');

                // ======================
                // BORDER LUAR TEBAL
                // ======================
                $sheet->getStyle("A4:F{$lastRow}")
                    ->getBorders()
                    ->getOutline()
                    ->setBorderStyle('medium');

                // BORDER DALAM TIPIS
                $sheet->getStyle("A4:F{$lastRow}")
                    ->getBorders()
                    ->getVertical()
                    ->setBorderStyle('thin');

                // ======================
                // FORMAT ANGKA
                // ======================
                $sheet->getStyle("C5:C{$lastRow}")
                    ->getNumberFormat()
                    ->setFormatCode('#,##0');

                $sheet->getStyle("E5:E{$lastRow}")
                    ->getNumberFormat()
                    ->setFormatCode('#,##0');

                $sheet->getStyle("F5:F{$lastRow}")
                    ->getNumberFormat()
                    ->setFormatCode('#,##0');

                // ======================
                // ALIGNMENT
                // ======================
                $sheet->getStyle("C5:F{$lastRow}")
                    ->getAlignment()
                    ->setHorizontal('right');

                // ======================
                // SALDO AWAL BOLD
                // ======================
                $sheet->getStyle('B5:F5')->getFont()->setBold(true);

                // ======================
                // AUTO HEIGHT
                // ======================
                foreach (range(1, $lastRow) as $row) {
                    $sheet->getRowDimension($row)->setRowHeight(-1);
                }
            }
        ];
    }
}