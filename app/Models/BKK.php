<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BKK extends Model
{
    use HasFactory;
    protected $connection = 'mysql2';
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
    ];
}
