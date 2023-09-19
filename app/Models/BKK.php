<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BKK extends Model
{
    use HasFactory;
    protected $table = 'bkk';
    protected $connection = 'mysql2';
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
        return $this->belongsTo(BKKHeader::class, 'bkk_header_id', 'id');
    }

    public function coa() : BelongsTo {
        return $this->setConnection('mysql')->belongsTo(Coa::class, 'coa_id','coa_id');
    }

    public function cop() : BelongsTo {
        return $this->belongsTo(COP_SPI::class, 'cop_id','cop_id');
    }
}
