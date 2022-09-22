<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sumber extends Model
{
    use HasFactory;
    protected $table = 'sumber';

    public function kas()
    {
        return $this->hasMany(Kas::class, 'sumber', 'id');
    }

    public function pengajuan()
    {
        return $this->hasMany(Pengajuan::class, 'sumber', 'id');
    }
}
