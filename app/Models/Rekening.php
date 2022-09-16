<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rekening extends Model
{
    use HasFactory;

    protected $table = 'rekening';

    public function kas()
    {
        return $this->hasMany(Kas::class, 'mutasi', 'id');
    }
}
