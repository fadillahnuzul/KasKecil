<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengajuan extends Model
{
    use HasFactory;
    protected $table = 'pengajuan';

    public function pengeluaran()
    {
        return $this->hasMany(Pengeluaran::class);
    }

    public function Sumber() 
    {
        return $this->belongsTo(Sumber::class, 'sumber', 'id');
    }

    public function Divisi() 
    {
        return $this->belongsTo(Divisi::class, 'divisi_id', 'id');
    }

    public function Status() 
    {
        return $this->belongsTo(Status::class, 'status', 'id');
    }
}
