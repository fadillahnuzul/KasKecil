<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $table = 'project';
    protected $primaryKey = 'project_id';

    public function pengajuan()
    {
        return $this->hasMany(Pengajuan::class, 'project', 'project_id');
    }
}
