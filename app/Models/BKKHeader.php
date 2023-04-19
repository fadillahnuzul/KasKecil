<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BKKHeader extends Model
{
    use HasFactory;
    protected $table = 'bkk_header';
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
    ];

    public function bkk()
    {
        return $this->hasMany(BKK::class, 'bkk_header_id', 'id');
    }
}
