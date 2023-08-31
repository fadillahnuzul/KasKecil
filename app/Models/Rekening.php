<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Rekening extends Model
{
    use HasFactory;

    protected $table = 'bank';
    protected $primaryKey = 'bank_id';

    public function bkkHeader() : HasMany {
        return $this->setConnection('mysql2')->hasMany(BKKHeader::class, 'bank_id', 'bank_id');
    }
}
