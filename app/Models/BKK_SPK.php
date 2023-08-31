<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BKK_SPK extends Model
{
    use HasFactory;
    protected $table = 'bkk';
    public $timestamps = false;
    protected $fillable = [
        'bkk_header_id',
        'ppn',
        'pph',
        'coa_id',
        'pekerjaan',
        'status_jurnal',
        'status',
        'otorisasi',
        'payment',
        'dpp',
        'action',
        'action_by',
        'action_date',
        'layer_cashflow_id',
        'using_budget',
        'name',
        'tanggal',
        'ppn_nomor',
        'ppn_tanggal',
        'bank_id',
        'cop_id',
        'partner',
        'uraian',
        'giro_nomor',
        'giro_tanggal',
        'giro_jatuh_tempo',
        'otorisasi_tanggal',
        'otorisasi_by',
    ];

    public function bkkHeader()
    {
        return $this->belongsTo(BKKHeader_SPK::class, 'bkk_header_id', 'id');
    }
}