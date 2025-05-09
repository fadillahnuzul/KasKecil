<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coa extends Model
{
    use HasFactory;

    protected $table = 'coa';
    protected $primaryKey = 'coa_id';
    protected $fillable = ['code','name','status_budget'];

    public function pengajuan()
    {
        return $this->hasMany(Pengajuan::class, 'coa', 'coa_id');
    }

    public function pengeluaran()
    {
        return $this->hasMany(Pengeluaran::class, 'coa', 'coa_id');
    }

    public function budgets()
    {
        return $this->hasMany(Coa::class, 'kode_coa', 'coa_id');
    }

    public function bkkDetail()
    {
        return $this->setConnection('mysql2')->hasMany(BKK::class, 'coa_id', 'coa_id');
    }

    public static function getCoa($id_coa)
    {
        return self::find($id_coa);
    }

    public function scopeSearchCoa($query, string|null $search)
    {
        ($search) ? $query->where('name','like','%'.$search.'%')->orWhere('code','like','%'.$search.'%')->orWhere('code', 'like', '2.120.000') : $query;
    }
}
