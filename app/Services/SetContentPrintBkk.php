<?php

namespace App\Services;

use Codedge\Fpdf\Fpdf\Fpdf;
use Illuminate\Database\Eloquent\Collection;

/**
 * Class SetContentPrintBkk
 * @package App\Services
 */
class SetContentPrintBkk extends Barcode
{
    public $bkk_detail;
    public $pdf;
    public function __construct(Collection $bkk_detail)
    {
        $this->bkk_detail = $bkk_detail;
        $this->pdf = new Barcode('L', 'mm', 'A5');
        $this->pdf->AddPage('L', 'A5');
        $this->pdf->SetMargins(5, 5, 5);
        $this->pdf->SetAutoPageBreak(false);
    }

    public function onePageContent(): void
    {
        $y = $this->pdf->GetY() + 33;
        $x = $this->pdf->GetX();
        $this->pdf->SetY($y);
        // $this->pdf->SetTextColor(255,0,0);
        $this->pdf->SetFont('Times','',11);
        $this->pdf->SetTextColor(187, 53, 197);
        $this->pdf->Cell(15, 7, 'Account', 1, 0, 'C');
        $this->pdf->Cell(40, 7, 'SPK', 1, 0, 'C');
        $this->pdf->Cell(60, 7, 'Uraian', 1, 0, 'C');
        $this->pdf->Cell(23, 7, 'DPP', 1, 0, 'C');
        $this->pdf->Cell(19, 7, 'PPN', 1, 0, 'C');
        // $this->pdf->Cell(28,7,'NO PPN',1,0,'C');
        $this->pdf->Cell(20, 7, 'PPH', 1, 0, 'C');
        $this->pdf->Cell(23, 7, 'Jumlah', 1, 1, 'C');

        $y = $this->pdf->getY();

        $this->pdf->SetFont('times', '', 9);
        $this->pdf->SetTextColor(0, 0, 0);

        $total_dpp = 0;
        $total_ppn = 0;
        $total_pph = 0;
        $total_jumlah = 0;
        $y1 = $y;
        foreach ($this->bkk_detail as $detail_bkk) {
            $this->pdf->SetY($y1);
            $x_row = $this->pdf->getX();
            $y_row = $this->pdf->getY();

            $jumlah = $detail_bkk->dpp + $detail_bkk->ppn - $detail_bkk->pph;

            $this->pdf->SetFont('Times', '', 8);


            //$this->pdf->MultiCell(15,3,$detail_bkk['code'].' ('.$detail_bkk['init_layer'].')',0,'L');
            $this->pdf->MultiCell(15, 4, $detail_bkk->code . '  =' . $detail_bkk->init_layer . '=', 1, 'L');

            //$this->pdf->SetX($x_row+80);
            //$y1 = $this->pdf->getY()+50;
            //$this->pdf->Cell($y1,8,$detail_bkk['init_layer'],'B',0,'L');	
            //$y1 = $this->pdf->getY()+50;

            $this->pdf->SetXY($x_row + 15, $y_row);

            $this->pdf->Cell(40, 4, ($detail_bkk->cop) ? $detail_bkk->cop->spk->name : '', 0, 1, 'L');
            $this->pdf->SetX($x_row + 15);
            $this->pdf->Cell(40, 4, ($detail_bkk->cop) ? $detail_bkk->cop->name : '', 'B', 0, 'L');
            $y1 = $this->pdf->getY() + 4;

            $height_multi = 8;
            //$type_of_work = substr($serial.' '.$detail_bkk['pekerjaan'].' '.$kavling,0,80);
            $type_of_work = substr(trim(preg_replace('/\s+/', ' ', $detail_bkk->pekerjaan)), 0, 80);
            if (strlen($type_of_work) > 35) {
                $height_multi = 4;
            }

            $this->pdf->SetFont('times', '', 9);
            $this->pdf->SetXY($x_row + 55, $y_row);
            $this->pdf->MultiCell(60, $height_multi, $type_of_work, 0, 'L');

            $this->pdf->SetXY($x_row + 55, $y_row);
            $this->pdf->Cell(60, 8, '', 'B', 0, 'L');

            $this->pdf->SetXY($x_row + 55 + 60, $y_row);
            $this->pdf->Cell(23, 8, number_format($detail_bkk->dpp * 1, 0, ',', '.'), 'B', 0, 'R');

            $this->pdf->Cell(19, 8, number_format($detail_bkk->ppn * 1, 0, ',', '.'), 'B', 0, 'R');

            $this->pdf->SetFont('times', '', 9);
            $this->pdf->SetXY($x_row + 55 + 60 + 23 + 19, $y_row);
            $this->pdf->Cell(20, 4, number_format($detail_bkk->pph * 1, 0, ',', '.'), '', 1, 'R');

            $this->pdf->SetX($x_row + 55 + 60 + 23 + 19);
            $this->pdf->SetFont('times', '', 8);
            $this->pdf->Cell(20, 4, $detail_bkk['pph_tipe'], 'B', 0, 'R');

            $this->pdf->SetXY($x_row + 55 + 60 + 23 + 19 + 20, $y_row);

            $this->pdf->SetFont('times', '', 9);
            $this->pdf->Cell(23, 8, number_format($jumlah, 0, ',', '.'), 'B', 1, 'R');
        }

        $total_dpp = $detail_bkk->sum('dpp');
        $total_ppn = $detail_bkk->sum('ppn');
        $total_pph = $detail_bkk->sum('pph');
        $total_jumlah = $total_dpp + $total_ppn - $total_pph;

        $this->pdf->SetY($y + 45);
        $this->pdf->SetFont('Times', 'B', 9);
        $this->pdf->Cell(115, 5, 'Total', 'LR', 0, 'R');
        $this->pdf->Cell(23, 5, number_format($total_dpp, 0, ',', '.'), 'LR', 0, 'R');
        $this->pdf->Cell(19, 5, number_format($total_ppn, 0, ',', '.'), 'LR', 0, 'R');
        // $this->pdf->Cell(28,5,'','LR',0,'R');
        $this->pdf->Cell(20, 5, number_format($total_pph, 0, ',', '.'), 'LR', 0, 'R');
        $this->pdf->Cell(23, 5, number_format($total_jumlah, 0, ',', '.'), 'LR', 1, 'R');

        // detail
        $this->pdf->SetY($y);
        $this->pdf->Cell(15, 50, ' ', 'LTRB', 0, 'C', 0);
        $this->pdf->Cell(40, 50, ' ', 'LTRB', 0, 'C', 0);
        $this->pdf->Cell(60, 50, ' ', 'LTRB', 0, 'C', 0);
        $this->pdf->Cell(23, 50, ' ', 'LTRB', 0, 'C', 0);
        $this->pdf->Cell(19, 50, ' ', 'LTRB', 0, 'C', 0);
        // $this->pdf->Cell(28,50,' ','LTRB',0,'C',0);
        $this->pdf->Cell(20, 50, ' ', 'LTRB', 0, 'C', 0);
        $this->pdf->Cell(23, 50, ' ', 'LTRB', 1, 'C', 0);

        $this->pdf->SetFont('Times', '', 11);
        $this->pdf->SetTextColor(0, 0, 0);
        $this->pdf->Ln(1);
        $this->pdf->SetX($x - 5);
        $this->pdf->Cell(40, 5, '', 0, 0, 'L');
    }

    public function multiplePageContent(): void
    {
    }
}
