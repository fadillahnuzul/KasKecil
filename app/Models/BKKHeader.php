<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BKKHeader extends Model
{
    use HasFactory;
    protected $connection = 'mysql2';
    protected $table = 'bkk_header';
    public $timestamps = false;
}
