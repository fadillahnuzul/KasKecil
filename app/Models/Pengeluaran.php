<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengeluaran extends Model
{
    use HasFactory;
    protected $table = 'pengeluaran';

    public function pengajuan() 
    {
        return $this->belongsTo(Pengajuan::class, 'pemasukan', 'id');
    }

    public function Divisi() 
    {
        return $this->belongsTo(Divisi::class, 'divisi_id', 'id');
    }

    public function Status() 
    {
        return $this->belongsTo(Status::class, 'status', 'id');
    }

    public function Kategori() 
    {
        return $this->belongsTo(Kategori::class, 'kategori', 'id');
    }

    public function Pembebanan() 
    {
        return $this->belongsTo(Pembebanan::class, 'pembebanan', 'id');
    }
}
