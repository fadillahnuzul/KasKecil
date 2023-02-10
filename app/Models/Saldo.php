<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Saldo extends Model
{
    use HasFactory;

    protected $table = 'pettycash_saldo';

    public function User()
    {
        return $this->belongsTo(User::class);
    }

    public function pengajuan()
    {
        return $this->hasMany(Pengajuan::class);
    }

    public function pengeluaran()
    {
        return $this->hasMany(Pengeluaran::class);
    }

    public function scopeNoUsernameUser($query)
    {
        return $query->where('id', '!=', 23);
    }

    public function scopeSearchByUserId($query, int|null $id)
    {
        return ($id) ? $query->where('id', $id) : $query;
    }
}
