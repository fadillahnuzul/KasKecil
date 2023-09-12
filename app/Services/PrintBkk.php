<?php

namespace App\Services;

use Codedge\Fpdf\Fpdf\Fpdf;
use App\Services\Barcode;

/**
 * Class PrintBkk
 * @package App\Services
 */
class PrintBkk extends Fpdf
{
    public $pdf;
    public function __construct() {
        $this->pdf = new FPDF('L','mm','A5');
    }

    public function printBkk($project, $bkk_header, $detail_bkk, $tipe)
    {
        $this->pdf->AddPage('L', 'A5'); 
        $this->setHeader($project, $bkk_header, $detail_bkk, $tipe);
        $this->pdf->Output();
    }

    public function setHeader($project, $bkk_header, $detail_bkk, $tipe): void
    {
        // $bulan = array('Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember');

        $y = $this->pdf->getY() + 5;
        $x = $this->pdf->getX();
        $this->pdf->SetY($y);
        // $this->SetDrawColor(255,0,0);
        $this->pdf->SetFont('times', 'B', 16);
        $this->pdf->SetDrawColor(187, 53, 197);
        $this->pdf->Cell(45, 14, ' ', 'LTRB', 0, 'C', 0);
        $this->pdf->Cell(100, 14, ' ', 'LTRB', 0, 'C', 0);
        $this->pdf->Cell(55, 7, ' ', 'LTRB', 0, 'C', 0);
        $this->pdf->SetXY($x + 45 + 95, $y + 7);
        $this->pdf->Cell(55, 7, ' ', 'LTRB', 0, 'C', 0);

        $this->pdf->SetY($y);
        if (isset($project->cop_header)) {
            if ($project->cop_header != '') {
                $b = '../teknik/master/master_company/cop_header/' . $project->cop_header;
                $this->pdf->Image($b, 10, $y + 2, 30, 10);
            }
        }
        $this->pdf->Ln(2);

        $this->pdf->SetFont('times', 'B', 15);
        // $this->SetTextColor(255,0,0);
        $this->pdf->SetTextColor(187, 53, 197);
        $this->pdf->Cell(46, 14, '', 0, 0, 'L');
        $this->pdf->Cell(98, 14, 'BUKTI PENGELUARAN KAS / BANK', 0, 0, 'C');
        $this->pdf->SetFont('times', '', 9);
        $this->pdf->SetXY($x + 32 + 108, $y);
        $this->pdf->Cell(6, 7, 'No : ', 0, 0, 'L');
        $this->pdf->SetTextColor(255, 0, 0);
        $this->pdf->SetFont('times', '', 9);

        if ($bkk_header->name == '' || $bkk_header->name == '0' || $bkk_header->name == '/\s/') {
            $name = "BK/" . substr($bkk_header->bank_initial, 0, 4) . "/" . $bkk_header->initial . "/____/____/____";
        } else {
            $name = $bkk_header->name;
        }
        $this->pdf->Cell(48, 7, $name, 0, 1, 'L');
        $this->pdf->SetTextColor(0, 0, 0);
        $this->pdf->SetXY($x + 32 + 108, $y + 7);
        $this->pdf->SetTextColor(187, 53, 197);
        // $this->SetTextColor(255,0,0);
        $this->pdf->Cell(8, 7, 'Tgl : ', 0, 0, 'L');
        $this->pdf->SetTextColor(0, 0, 0);

        if ($bkk_header->tanggal == '' || $bkk_header->tanggal == '0') {
            $tanggal = '';
        } else {
            $tanggal = date_create($bkk_header->tanggal);
            $tanggal = date_format($tanggal, "d-m-Y");
        }

        $this->pdf->Cell(48, 7, $tanggal, 0, 1, 'L');

        $this->pdf->SetTextColor(187, 53, 197);
        // $this->SetTextColor(255,0,0);

        $this->pdf->SetFont('times', '', 11);
        $y = $this->pdf->getY();
        $this->pdf->SetY($y);
        $this->pdf->Cell(200, 33, ' ', 'LTRB', 0, 'C', 0);
        $this->pdf->Ln(1);
        $this->pdf->Cell(35, 5, 'Kas ', 'B', 1, 'L');
        $this->pdf->Cell(35, 5, 'Bank ', 0, 0, 'L');
        $this->pdf->Cell(5, 5, ': ', 0, 0, 'L');
        $this->pdf->SetTextColor(0, 0, 0);
        $this->pdf->Cell(55, 5, $bkk_header->bank_name . ' - ' . $bkk_header->rekening, 0, 0, 'L');

    }

    public function setFooter(): void
    {
    }

    public function setContent(): void
    {
    }
}
