<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class COP_SPI extends Model
{
    use HasFactory;
    protected $table = 'cop';
    protected $primaryKey = 'cop_id';
    protected $connection = 'mysql2';

    public function spk() : BelongsTo {
        return $this->belongsTo(SPK_SPI::class, 'spk_id', 'spk_id');
    }
}
