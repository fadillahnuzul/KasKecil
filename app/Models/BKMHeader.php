<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BKMHeader extends Model
{
    use HasFactory;
    protected $table = 'bkm_header';
    public $timestamps = false;
    protected $fillable = [
        'bank_id',
        'name',
        'tanggal',
        'partner',
        'otorisasi',
        'project_id',
        'layer_cashflow_id',
        'created_by',
        'created_at',
        'status',
        'uraian',
        'status_jurnal',
        'otorisasi_tanggal',
        'otorisasi_by',
        'giro_nomor',
        'giro_cair',
        'giro_tanggal',
        'giro_jatuh_tempo',
        'index_date',
    ];
}
