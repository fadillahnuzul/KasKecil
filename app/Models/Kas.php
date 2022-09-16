<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kas extends Model
{
    use HasFactory;

    protected $table = 'kas';
    public $timestamps = false;

    public function Rekening() 
    {
        return $this->belongsTo(Rekening::class, 'mutasi', 'id');
    }
}
