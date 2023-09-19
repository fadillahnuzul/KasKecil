<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SPK_SPI extends Model
{
    use HasFactory;
    protected $table = 'spk';
    protected $primaryKey = 'spk_id';
    protected $connection = 'mysql2';

    public function cop() : HasMany {
        return $this->hasMany(COP_SPI::class, 'spk_id', 'spk_id');
    }
}
