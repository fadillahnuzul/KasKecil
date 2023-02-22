<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coa extends Model
{
    use HasFactory;

    protected $table = 'coa';
    protected $primaryKey = 'coa_id';

    public function pengajuan()
    {
        return $this->hasMany(Pengajuan::class, 'coa', 'coa_id');
    }

    public function pengeluaran()
    {
        return $this->hasMany(Pengeluaran::class, 'coa', 'coa_id');
    }

}
