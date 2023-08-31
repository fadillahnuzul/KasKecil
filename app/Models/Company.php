<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Company extends Model
{
    use HasFactory;

    protected $table = 'project_company';
    protected $primaryKey = 'project_company_id';

    public function pengajuan()
    {
        return $this->hasMany(Pengajuan::class, 'company', 'project_company_id');
    }

    public function pengeluaran()
    {
        return $this->hasMany(Pengeluaran::class, 'company', 'project_company_id');
    }

    public function project() : HasMany {
        return $this->hasMany(Project::class, 'project_company_id','project_company_id');
    }
}
