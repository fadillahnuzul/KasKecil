<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class COP extends Model
{
    use HasFactory;
    protected $table = 'cop';
    protected $primaryKey = 'cop_id';

    public function spk() : BelongsTo {
        return $this->belongsTo(SPK::class, 'spk_id', 'spk_id');
    }
}
