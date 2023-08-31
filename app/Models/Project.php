<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    use HasFactory;

    protected $table = 'project';
    protected $primaryKey = 'project_id';

    public function pengeluaran()
    {
        return $this->hasMany(Pengeluaran::class, 'project_id', 'project_id');
    }

    public function bkk_header() : HasMany {
        return $this->setConnection('mysql2')->hasMany(BKKHeader::class, 'project_id','project_id');
    }

    public function company() : BelongsTo {
        return $this->belongsTo(Company::class, 'project_company_id','project_company_id');
    }
}
