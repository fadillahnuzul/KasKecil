<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BKM extends Model
{
    use HasFactory;
    protected $table = 'bkm';
    public $timestamps = false;
    protected $fillable = [
        'bkm_header_id',
        'ppn',
        'pph',
        'coa_id',
        'pekerjaan',
        'status_jurnal',
        'status',
        'otorisasi',
        'payment',
        'dpp',
        'layer_cashflow_id',
        'ppn_nomor',
        'ppn_tanggal',
        'kwitansi_id',
        'partner',
        'uraian',
        'otorisasi_tanggal',
        'otorisasi_by'
    ];
}
