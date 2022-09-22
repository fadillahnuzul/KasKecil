<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengeluaran extends Model
{
    use HasFactory;
    protected $table = 'pengeluaran';

    public function pengajuan() 
    {
        return $this->belongsTo(Pengajuan::class, 'pemasukan', 'id');
    }
}
