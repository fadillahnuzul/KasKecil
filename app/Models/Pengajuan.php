<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Alfa6661\AutoNumber\AutoNumberTrait;

class Pengajuan extends Model
{
    use HasFactory;
    protected $table = 'pettycash_pengajuan';
    use AutoNumberTrait;
    
    /**
     * Return the autonumber configuration array for this model.
     *
     * @return array
     */
    public function getAutoNumberOptions()
    {
        return [
            'kode' => [
                'format' => function () {
                    return 'KKc' . '/' . $this->divisi . '/?'; 
                }, // Format kode yang akan digunakan.
                'length' => 5 // Jumlah digit yang akan digunakan sebagai nomor urut
            ]
        ];
    }

    public function pengeluaran()
    {
        return $this->hasMany(Pengeluaran::class, 'pemasukan', 'id');
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
    
    public function User() 
    {
        return $this->belongsTo(User::class);
    }
    
    public function Saldo() 
    {
        return $this->belongsTo(Saldo::class, 'user_id', 'id');
    }

    public function COA() 
    {
        return $this->belongsTo(COA::class, 'coa', 'coa_id');
    }

    public function Company() 
    {
        return $this->belongsTo(Company::class, 'company', 'project_company_id');
    }

    public function Project() 
    {
        return $this->belongsTo(Project::class, 'project', 'project_id');
    }
}
