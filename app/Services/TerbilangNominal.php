<?php

namespace App\Services;

use App\Enums\Terbilang;
use Uwaiscode\Laravelterbilang\Converter;

/**
 * Class TerbilangNominal
 * @package App\Services
 */
class TerbilangNominal
{
    public $angkaHuruf = [
        "",
        "satu",
        "dua",
        "tiga",
        "empat",
        "lima",
        "enam",
        "tujuh",
        "delapan",
        "sembilan"
    ];

    public function getEjaanNominal(int $nominal): string
    {
        return Converter::getConversion($nominal)."Rupiah";
        // $arrayEjaan = [];
        // $count = 0;
        // $loop = $nominal;
        // while ($loop > 0) {
        //     $angka = $this->getAngka($nominal, $count);
        //     $satuan = $this->getSatuan($loop);
        //     $ejaan = $this->setAngkaSatuan($angka, $satuan);
        //     array_push($arrayEjaan, $ejaan);
        //     $loop = (int) ($loop / 10);
        //     $count++;
        // }
        // $terbilang = implode(' ',$arrayEjaan);
        // dd($terbilang);
        // return $terbilang." Rupiah";
    }

    // public function setAngkaSatuan(string $angka, string $satuan) : string {
    //     if (!$angka) {
    //         return "";
    //     }

    //     if ($angka == "satu" && $satuan) {
    //         return "se" . $satuan;
    //     }

    //     return $angka . " " . $satuan;
    // }

    // public function getAngka(int $nominal, int $position): string
    // {
    //     $arrayAngka = str_split((string)$nominal);
    //     $angka = $this->angkaHuruf[$arrayAngka[$position]];
    //     return $angka;
    // }

    // public function getSatuan(int $nominal): string
    // {
    //     $satuan = "";
    //     $len = strlen((string) $nominal);
    //     if ($len > 9) {
    //         $satuan = Terbilang::MILYAR;
    //     } elseif ($len > 6) {
    //         $len = $len % 6;
    //         if($len == 3) {
    //             $satuan = Terbilang::RATUS;
    //         } elseif ($len == 2) {
    //             $satuan = Terbilang::PULUH;
    //         } else {
    //             $satuan = Terbilang::JUTA;
    //         }
    //     } elseif ($len > 3) {
    //         $len = $len % 3;
    //         if($len == 2) {
    //             $satuan = Terbilang::PULUH;
    //         } elseif ($len == 1) {
    //             $satuan = Terbilang::RIBU;
    //         } else {
    //             $satuan = Terbilang::RATUS;
    //         }
    //     } elseif ($len > 2) {
    //         $satuan = Terbilang::RATUS;
    //     } elseif ($len == 2 && $nominal > 10) {
    //         $satuan = Terbilang::PULUH;
    //     } elseif ($len == 2 && $nominal <= 10) {
    //         $satuan = Terbilang::BELAS;
    //     }

    //     return ($satuan) ? ($satuan->value) : $satuan;
    // }
}
