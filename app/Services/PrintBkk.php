<?php

namespace App\Services;

use Carbon\Carbon;
use Uwaiscode\Laravelterbilang\Converter;

/**
 * Class PrintBkk
 * @package App\Services
 */
class PrintBkk extends Barcode
{
    public $pdf;
    public $total_payment;
    public $y;
    public $x;

    public function __construct()
    {
        $this->pdf = new Barcode('L', 'mm', 'A5');
    }

    public function printBkk($bkk_header, $detail_bkk, $tipe)
    {
        $this->total_payment = $detail_bkk->sum('payment');
        $this->pdf->AddPage('L', 'A5');
        $this->pdf->SetMargins(5, 5, 5);
        $this->pdf->SetAutoPageBreak(false);

        $this->setHeader($bkk_header);
        $y = $this->pdf->GetY() + 5;
        $this->setBarcode($bkk_header, $tipe);
        $this->setContent($detail_bkk, $bkk_header, $tipe, $y);
        // $this->setFooter($bkk_header, $this->pdf->GetY());
        $this->pdf->Output('D', 'BKK.pdf');
    }

    public function setHeader($bkk_header): void
    {
        $y = $this->pdf->getY() + 5;
        $x = $this->pdf->getX();
        $this->pdf->SetY($y);
        // $this->SetDrawColor(255,0,0);
        $this->pdf->SetFont('Times', 'B', 16);
        $this->pdf->SetDrawColor(187, 53, 197);
        $this->pdf->Cell(45, 14, ' ', 'LTRB', 0, 'C', 0);
        $this->pdf->Cell(100, 14, ' ', 'LTRB', 0, 'C', 0);
        $this->pdf->Cell(55, 7, ' ', 'LTRB', 0, 'C', 0);
        $this->pdf->SetXY($x + 45 + 95, $y + 7);
        $this->pdf->Cell(55, 7, ' ', 'LTRB', 0, 'C', 0);

        $this->pdf->SetY($y);
        // if (isset($project->cop_header)) {
        //     if ($project->cop_header != '') {
        //         $b = '../teknik/master/master_company/cop_header/' . $project->cop_header;
        //         $this->pdf->Image($b, 10, $y + 2, 30, 10);
        //     }
        // }
        $this->pdf->Ln(2);

        $this->pdf->SetFont('Times', 'B', 15);
        // $this->SetTextColor(255,0,0);
        $this->pdf->SetTextColor(187, 53, 197);
        $this->pdf->Cell(46, 14, '', 0, 0, 'L');
        $this->pdf->Cell(98, 14, 'BUKTI PENGELUARAN KAS / BANK', 0, 0, 'C');
        $this->pdf->SetFont('Times', '', 9);
        $this->pdf->SetXY($x + 32 + 108, $y);
        $this->pdf->Cell(6, 7, 'No : ', 0, 0, 'L');
        $this->pdf->SetTextColor(255, 0, 0);
        $this->pdf->SetFont('Times', '', 9);

        if ($bkk_header->name == '' || $bkk_header->name == '0' || $bkk_header->name == '/\s/') {
            $name = "BK/" . substr($bkk_header->bank->initial, 0, 4) . "/" . $bkk_header->project->initial . "/____/____/____";
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

        $this->pdf->SetFont('Times', '', 11);
        $y = $this->pdf->getY();
        $this->pdf->SetY($y);
        $this->pdf->Cell(200, 33, ' ', 'LTRB', 0, 'C', 0);
        $this->pdf->Ln(1);
        $this->pdf->Cell(35, 5, 'Kas ', 'B', 1, 'L');
        $this->pdf->Cell(35, 5, 'Bank ', 0, 0, 'L');
        $this->pdf->Cell(5, 5, ': ', 0, 0, 'L');
        $this->pdf->SetTextColor(0, 0, 0);
        $this->pdf->Cell(55, 5, $bkk_header->bank->name . ' - ' . $bkk_header->bank->rekening, 0, 0, 'L');

        $this->pdf->SetTextColor(187, 53, 197);
        $this->pdf->ln(5);
        $this->pdf->Cell(95, 5, 'Disetorkan / Dibayarkan kepada : ', 0, 0, 'R');
        $this->pdf->SetTextColor(0, 0, 0);
        $this->pdf->Cell(50, 5, $bkk_header->partner, 0, 0, 'L');
        $this->pdf->ln(5);

        $this->pdf->Cell(40, 5, '', 0, 0, 'L');
        // $this->SetTextColor(255,0,0);
        $this->pdf->SetTextColor(187, 53, 197);
        $this->pdf->Cell(25, 5, 'Jumlah : ', 0, 0, 'L');
        $this->pdf->SetTextColor(0, 0, 0);
        $this->pdf->Cell(55, 5, 'Rp ' . number_format($this->total_payment, 0, ',', '.'), 0, 1, 'L');

        $this->pdf->ln(2);
        $this->pdf->Cell(40, 5, '', 0, 0, 'L');
        $this->pdf->SetTextColor(0, 0, 0);
        $this->pdf->MultiCell(155, 5, $this->moneyFormatter($this->total_payment), 0, 'L');
    }

    public function setFooter($bkk_header, $y): void
    {
        // $y = $this->pdf->getY() + 65;
        $x = $this->pdf->getX();
        $this->pdf->SetFont('Times', '', 9);
        $this->pdf->SetTextColor(187, 53, 197);
        // $this->SetTextColor(255,0,0);
        $this->pdf->SetX($x - 5);
        $this->pdf->SetY($y);
        $this->pdf->Cell(80, 5, 'Catatan : ', 'LTR', 0, 'L', 0);
        $this->pdf->Cell(30, 5, 'Disetujui ', 1, 0, 'C', 0);
        $this->pdf->Cell(30, 5, 'Diperiksa ', 1, 0, 'C', 0);
        $this->pdf->Cell(30, 5, 'Dibukukan ', 1, 0, 'C', 0);
        $this->pdf->Cell(30, 5, 'Penerima ', 1, 0, 'C', 0);

        $this->pdf->SetTextColor(0, 0, 0);
        $this->pdf->MultiCell(80, 5, $bkk_header->uraian, 0, 'L');
        $this->pdf->SetTextColor(187, 53, 197);
        $this->pdf->Cell(80, 20, '', 'LTRB', 0, 'C', 0);
        $this->pdf->Cell(30, 20, '', 'LTRB', 0, 'C', 0);
        if ($bkk_header->otorisasi == 0) {
            $this->pdf->Cell(30, 20, "NOT", 'LTRB', 0, 'C', 0);
        } else {
            $this->pdf->Cell(30, 20, $bkk_header->otorisator->username, 'LTRB', 0, 'C', 0);
        }

        $this->pdf->Cell(30, 20, $bkk_header->creator->username, 'LTRB', 0, 'C', 0);
        $this->pdf->Cell(30, 20, ' ', 'LTRB', 0, 'C', 0);

        $this->pdf->Ln(4);

        $this->pdf->Cell(80, 20, ' ', '', 0, 'C', 0);
        $this->pdf->Cell(30, 20, '', '', 0, 'C', 0);
        if ($bkk_header->otorisasi == 0) {
            $this->pdf->Cell(30, 20, 'AUTHORIZED', '', 0, 'C', 0);
        } else {
            $this->pdf->Cell(30, 20, Carbon::parse($bkk_header->otorisasi_tanggal)->format('d F Y'), '', 0, 'C', 0);
        }

        $this->pdf->Cell(30, 20, Carbon::parse($bkk_header->created_at)->format('d F Y'), '', 0, 'C', 0);
        $this->pdf->Cell(30, 20, ' ', '', 0, 'C', 0);

        $this->pdf->Ln(4);

        $this->pdf->Cell(80, 20, ' ', '', 0, 'C', 0);
        $this->pdf->Cell(30, 20, '', '', 0, 'C', 0);
        if ($bkk_header->otorisasi == 0) {
            $this->pdf->Cell(30, 20, '', '', 0, 'C', 0);
        } else {
            $this->pdf->Cell(30, 20, Carbon::parse($bkk_header->otorisasi_tanggal)->format('H:i'), '', 0, 'C', 0);
        }
        $this->pdf->Cell(30, 20, Carbon::parse($bkk_header->created_at)->format('H:i'), '', 0, 'C', 0);
        $this->pdf->Cell(30, 20, ' ', '', 0, 'C', 0);
    }

    public function setBarcode($bkk_header, $tipe): void
    {
        $this->pdf->SetTextColor(0, 0, 0);
        $code = $tipe . "-" . $bkk_header->id;
        $this->pdf->Code128(155, 1, $code, 50, 10);
        // $this->Code39(140,1,$code,1,9);
        $this->pdf->SetFont('Times', '', 9);
        $this->pdf->SetXY(155, 11);
        $this->pdf->Cell(50, 5, $code, 0, 0, 'C', 0);
    }

    public function setContent($detail_bkk, $bkk_header, $tipe, $y): void
    {
        $jumlah_data = count($detail_bkk);
        if ($jumlah_data < 5) {
            $this->onePageContent($detail_bkk, $bkk_header, $y);
            $y = $this->pdf->GetY();
        } else {
            $detail_bkk_split = $detail_bkk->split($jumlah_data / 7);
            $totalPage = count($detail_bkk_split);
            $page = 1;
            foreach ($detail_bkk_split as $detail) {
                $this->multiplePageContent($detail, $bkk_header, $detail_bkk, $tipe, $page, $totalPage, $y);
                $y = $this->pdf->GetY() + 10;
                $page++;
            }
        }
    }

    public function moneyFormatter($nominal): string
    {
        return Converter::getConversion($nominal) . "Rupiah";
    }

    public function onePageContent($detail_bkk, $bkk_header, $y = null): void
    {
        $y = ($y) ?? $this->pdf->GetY();
        // $y = $this->pdf->GetY() + 33;
        $x = $this->pdf->GetX();
        $this->pdf->SetY($y);
        // $this->pdf->SetTextColor(255,0,0);
        $this->pdf->SetFont('Times', '', 11);
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
        foreach ($detail_bkk as $detail_bkk) {
            $this->pdf->SetY($y1);
            $x_row = $this->pdf->getX();
            $y_row = $this->pdf->getY();

            $jumlah = $detail_bkk->dpp + $detail_bkk->ppn - $detail_bkk->pph;

            $this->pdf->SetFont('Times', '', 8);

            //$this->pdf->MultiCell(15,3,$detail_bkk['code'].' ('.$detail_bkk['init_layer'].')',0,'L');
            $this->pdf->MultiCell(15, 4, $detail_bkk->coa->code . '  =' . $detail_bkk->init_layer . '=', 1, 'L');

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
            $this->pdf->Cell(20, 4, $detail_bkk->pph_tipe, 'B', 0, 'R');

            $this->pdf->SetXY($x_row + 55 + 60 + 23 + 19 + 20, $y_row);

            $this->pdf->SetFont('times', '', 9);
            $this->pdf->Cell(23, 8, number_format($detail_bkk->payment, 0, ',', '.'), 'B', 1, 'R');

            $total_dpp += $detail_bkk->dpp;
            $total_ppn += $detail_bkk->ppn;
            $total_pph += $detail_bkk->pph;
            $total_jumlah += $detail_bkk->payment;
        }

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

        $ket = "PAGE " . 1 . " / " . 1;
        $this->pdf->SetXY(5, 5);
        $this->pdf->Cell(10, 5, $ket, 0, 0, 'L', 0);

        $this->setFooter($bkk_header, $this->pdf->GetY());
    }

    public function multiplePageContent($detail_bkk, $bkk_header, $detail_bkk_awal , $tipe, $page, $totalPage, $y): void
    {
        $this->pdf->SetY($y);
        $this->pdf->SetTextColor(187, 53, 197);
        $this->pdf->Cell(15, 7, 'Account', 1, 0, 'C');
        $this->pdf->Cell(40, 7, 'SPK', 1, 0, 'C');
        $this->pdf->Cell(60, 7, 'Uraian', 1, 0, 'C');
        $this->pdf->Cell(23, 7, 'DPP', 1, 0, 'C');
        $this->pdf->Cell(19, 7, 'PPN', 1, 0, 'C');
        // $this->Cell(28,7,'NO PPN',1,0,'C');
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
        foreach ($detail_bkk as $detail_bkk) {
            $this->pdf->SetY($y1);
            $x_row = $this->pdf->getX();
            $y_row = $this->pdf->getY();

            $jumlah = $detail_bkk->dpp + $detail_bkk->ppn - $detail_bkk->pph;

            $this->pdf->SetFont('Times', '', 8);


            //$this->pdf->MultiCell(15,3,$detail_bkk['code'].' ('.$detail_bkk['init_layer'].')',0,'L');
            $this->pdf->MultiCell(15, 4, $detail_bkk->coa->code . '  =' . $detail_bkk->init_layer . '=', 'LBR', 'L');

            $this->pdf->SetXY($x_row + 15, $y_row);

            $this->pdf->Cell(40, 4, ($detail_bkk->cop) ? $detail_bkk->cop->spk->name : '', 'RL', 1, 'L');
            $this->pdf->SetX($x_row + 15);
            $this->pdf->Cell(40, 4, ($detail_bkk->cop) ? $detail_bkk->cop->name : '', 'RLB', 0, 'L');
            $y1 = $this->pdf->getY() + 4;

            $height_multi = 8;
            //$type_of_work = substr($serial.' '.$detail_bkk['pekerjaan'].' '.$kavling,0,80);
            $type_of_work = substr(trim(preg_replace('/\s+/', ' ', $detail_bkk->pekerjaan)), 0, 80);
            if (strlen($type_of_work) > 35) {
                $height_multi = 4;
            }

            $this->pdf->SetFont('Times', '', 9);
            $this->pdf->SetXY($x_row + 55, $y_row);
            $this->pdf->MultiCell(60, $height_multi, $type_of_work, 0, 'L');

            $this->pdf->SetXY($x_row + 55, $y_row);
            $this->pdf->Cell(60, 8, '', 'B', 0, 'L');

            $this->pdf->SetXY($x_row + 55 + 60, $y_row);
            $this->pdf->Cell(23, 8, number_format($detail_bkk->dpp * 1, 0, ',', '.'), 1, 0, 'R');

            $this->pdf->Cell(19, 8, number_format($detail_bkk->ppn * 1, 0, ',', '.'), 1, 0, 'R');

            $this->pdf->SetFont('times', '', 9);
            $this->pdf->SetXY($x_row + 55 + 60 + 23 + 19, $y_row);
            $this->pdf->Cell(20, 4, number_format($detail_bkk->pph * 1, 0, ',', '.'), '', 1, 'R');

            $this->pdf->SetX($x_row + 55 + 60 + 23 + 19);
            $this->pdf->SetFont('Times', '', 8);
            $this->pdf->Cell(20, 4, $detail_bkk->pph_tipe, 'B', 0, 'R');

            $this->pdf->SetXY($x_row + 55 + 60 + 23 + 19 + 20, $y_row);

            $this->pdf->SetFont('times', '', 9);
            $this->pdf->Cell(23, 8, number_format($detail_bkk->payment, 0, ',', '.'), 1, 1, 'R');

            $total_dpp += $detail_bkk->dpp;
            $total_ppn += $detail_bkk->ppn;
            $total_pph += $detail_bkk->pph;
            $total_jumlah += $jumlah;
        }

        if ($page == $totalPage) {
            $total_dpp = $detail_bkk_awal->sum('dpp');
            $total_ppn = $detail_bkk_awal->sum('ppn');
            $total_pph = $detail_bkk_awal->sum('pph');
            $total_jumlah = $detail_bkk_awal->sum('payment');
            // dd($detail_bkk_awal->sum('dpp'));
            $this->pdf->SetFont('Times', 'B', 9);
            $this->pdf->Cell(115, 5, 'Total', 'LRB', 0, 'R');
            $this->pdf->Cell(23, 5, number_format($total_dpp, 0, ',', '.'), 'LRB', 0, 'R');
            $this->pdf->Cell(19, 5, number_format($total_ppn, 0, ',', '.'), 'LRB', 0, 'R');
            // $this->pdf->Cell(28,5,'','LR',0,'R');
            $this->pdf->Cell(20, 5, number_format($total_pph, 0, ',', '.'), 'LRB', 0, 'R');
            $this->pdf->Cell(23, 5, number_format($total_jumlah, 0, ',', '.'), 'LRB', 1, 'R');
            $y = $this->pdf->GetY();
        }

        // barcode
        $this->pdf->SetTextColor(0, 0, 0);
        $code = $tipe . "-" . $bkk_header->id;
        $this->pdf->Code128(155, 1, $code, 50, 10);
        $ket = "PAGE " . $page . " / " . $totalPage;
        $this->pdf->SetXY(5, 5);
        $this->pdf->Cell(10, 5, $ket, 0, 0, 'L', 0);
        $this->pdf->SetXY(155, 11);
        $this->pdf->Cell(50, 5, $code, 0, 0, 'C', 0);

        if ($page != $totalPage) {
            $this->pdf->AddPage();
        }

        if ($page == $totalPage) {
            $this->setFooter($bkk_header, $y+1);
        }
    }
}
