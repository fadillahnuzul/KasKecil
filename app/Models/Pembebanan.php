<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pembebanan extends Model
{
    use HasFactory;

    protected $table = 'pembebanan';

    public function pengeluaran()
    {
        return $this->hasMany(Pengeluaran::class, 'pembebanan', 'id');
    }
}
