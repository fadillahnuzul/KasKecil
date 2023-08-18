<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BKKHeader_SPK extends Model
{
    use HasFactory;
    protected $table = 'bkk_header';
    public $timestamps = false;
    protected $fillable = [
        'bank_id',
        'name',
        'tanggal',
        'periode',
        'partner',
        'otorisasi',
        'project_id',
        'layer_cashflow_id',
        'created_by',
        'created_at',
        'status',
        'uraian',
        'status_jurnal',
        'giro_nomor',
        'giro_tanggal',
        'giro_jatuh_tempo',
        'otorisasi_tanggal',
        'otorisasi_by',
        'giro_cair',
        'reference',
        'flag_sudah_rekon',
        'tanggal_rekon'
    ];

    public function bkk()
    {
        return $this->hasMany(BKK_SPK::class, 'bkk_header_id', 'id');
    }
}
