<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $table = 'project';
    protected $primaryKey = 'project_id';

    public function pengeluaran()
    {
        return $this->hasMany(Pengeluaran::class, 'project_id', 'project_id');
    }
}
