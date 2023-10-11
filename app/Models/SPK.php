<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SPK extends Model
{
    use HasFactory;
    protected $table = 'spk';
    protected $primaryKey = 'spk_id';

    public function cop() : HasMany {
        return $this->hasMany(COP::class, 'spk_id', 'spk_id');
    }
}
