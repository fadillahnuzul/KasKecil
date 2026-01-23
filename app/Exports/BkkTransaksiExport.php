<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class BkkTransaksiExport implements FromCollection, WithStyles, WithColumnFormatting, ShouldAutoSize
{
    protected $groupedData;
    protected $bkk;
    protected $companyName;
    protected $projectName;

    public function __construct(Collection $groupedData, $bkk)
    {
        $this->groupedData = $groupedData;
        $this->bkk = $bkk;
        $this->companyName = $this->groupedData->first()->first()->Pembebanan->name;
        $this->projectName = $this->groupedData->first()->first()->Project->name;
    }


    public function collection()
    {
        $rows = collect();
        $grandTotal = 0;

        // ======================
        // HEADER BKK
        // ======================
        $rows->push([
            $this->bkk->id
        ]);

        $rows->push([
            'Tanggal Dibuat = ' . Carbon::parse($this->bkk->tanggal)->format('d/m/Y')
        ]);

        $rows->push([
            'Rekening = ' . $this->bkk->bank->name
                . ' - ' . $this->bkk->bank->rekening
        ]);

        // SPASI
        $rows->push([' ']);

        $rows->push([$this->companyName . ' - ' . $this->projectName]);
        $rows->push([' ']);

        foreach ($this->groupedData as $items) {

            $coa = $items->first()->coa;
            $subtotal = $items->sum('jumlah');
            $grandTotal += $subtotal;

            // HEADER COA
            $rows->push([
                'COA ' . $coa->code . ' - ' . $coa->name
            ]);

            // HEADER TABEL
            $rows->push([
                'Tanggal',
                'Keterangan',
                'User',
                'Divisi',
                'PIC',
                'Nota Tujuan',
                'Nominal'
            ]);

            // DATA
            foreach ($items as $item) {
                $rows->push([
                    Carbon::parse($item->tanggal)->format('d/m/Y'),
                    $item->deskripsi,
                    $item->User->username,
                    $item->Divisi->name,
                    $item->pic,
                    $item->tujuan,
                    (float) $item->jumlah,
                ]);
            }

            // SUBTOTAL
            $rows->push([
                '',
                '',
                '',
                '',
                '',
                'Subtotal',
                (float) $subtotal
            ]);

            $rows->push([]);
        }

        // GRAND TOTAL
        $rows->push([
            '',
            '',
            '',
            '',
            '',
            'GRAND TOTAL',
            (float) $grandTotal
        ]);

        return $rows;
    }

    // ======================
    // FORMAT ANGKA
    // ======================
    public function columnFormats(): array
    {
        return [
            'G' => '[$Rp-421] #,##0',
        ];
    }

    // ======================
    // STYLE & MERGE
    // ======================
    public function styles(Worksheet $sheet)
    {
        $highestRow = $sheet->getHighestRow();

        for ($row = 1; $row <= $highestRow; $row++) {

            $valueA = (string) $sheet->getCell("A{$row}")->getValue();
            $valueF = (string) $sheet->getCell("F{$row}")->getValue();

            // HEADER BKK
            if (
                str_starts_with($valueA, $this->bkk->id) ||
                str_starts_with($valueA, 'Tanggal Dibuat =') ||
                str_starts_with($valueA, 'Rekening =')
            ) {
                $sheet->mergeCells("A{$row}:G{$row}");
                $sheet->getStyle("A{$row}")
                    ->getFont()
                    ->setBold(true);
            }
            if (str_starts_with($valueA, $this->bkk->id)) {
                $sheet->getStyle("A{$row}")
                    ->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            }

            // COMPANY NAME
            if ($valueA === $this->companyName . ' - ' . $this->projectName) {

                $sheet->mergeCells("A{$row}:G{$row}");

                $sheet->getStyle("A{$row}")
                    ->getFont()
                    ->setBold(true)
                    ->setSize(13);

                $sheet->getStyle("A{$row}")
                    ->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER);
            }

            // HEADER COA
            if (str_starts_with($valueA, 'COA ')) {
                $sheet->mergeCells("A{$row}:G{$row}");
                $sheet->getStyle("A{$row}")
                    ->getFont()
                    ->setBold(true);
            }

            // HEADER TABEL
            if ($valueA === 'Tanggal') {
                $sheet->getStyle("A{$row}:G{$row}")
                    ->getFont()
                    ->setBold(true);
            }

            // SUBTOTAL
            if ($valueF === 'Subtotal') {
                $sheet->getStyle("A{$row}:G{$row}")
                    ->getFont()
                    ->setBold(true);
            }

            // GRAND TOTAL
            if ($valueF === 'GRAND TOTAL') {
                $sheet->getStyle("A{$row}:G{$row}")
                    ->getFont()
                    ->setBold(true);
            }
        }
    }
}
