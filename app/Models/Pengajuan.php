<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Alfa6661\AutoNumber\AutoNumberTrait;
use Carbon\Carbon;

class Pengajuan extends Model
{
    use HasFactory;
    protected $table = 'pettycash_pengajuan';
    protected $fillable = ['status'];
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

    public function Company() 
    {
        return $this->belongsTo(Company::class, 'company', 'project_company_id');
    }

    public function Project() 
    {
        return $this->belongsTo(Project::class, 'project', 'project_id');
    }

    public function scopeSearchByUser($query, string|null $id_user)
    {
        return ($id_user) ? $query->where('user_id', $id_user) : $query;
    }

    public function scopeSearchByDateRange($query, string|null $start = null, string|null $end = null)
    {
        $start = ($start) ? $start : Carbon::now()->firstOfYear()->format('Y-m-d');
        $end = ($end) ? $end : Carbon::now()->endOfYear()->format('Y-m-d');
        return $query->whereBetween('tanggal', [$start,$end]);
    }

    public function scopeNoUsernameUser($query)
    {
        return $query->where('id', '!=', 23);
    }

    public function scopeStatusProgressAndApproved($query) 
    {
        return $query->whereIn('status', [2,4]);
    }

    public function scopeIsDone($query)
    {
        return $query->where('status', 5);
    }
}
