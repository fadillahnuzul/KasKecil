<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengeluaran extends Model
{
    use HasFactory;
    protected $table = 'pettycash_pengeluaran';

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

    public function Pembebanan()
    {
        return $this->belongsTo(Company::class, 'pembebanan', 'project_company_id');
    }

    public function User()
    {
        return $this->belongsTo(User::class);
    }

    public function COA() 
    {
        return $this->belongsTo(COA::class, 'coa', 'coa_id');
    }
}
