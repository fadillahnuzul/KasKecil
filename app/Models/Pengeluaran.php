<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengeluaran extends Model
{
    use HasFactory;
    protected $table = 'pettycash_pengeluaran';
    protected $fillable = [
        'tanggal',
        'deskripsi',
        'jumlah',
        'divisi_id',
        'status',
        'coa',
        'pic',
        'pembebanan',
        'tujuan',
        'user_id',
        'in_budget',
        'project_id',
        'tanggal_uang_kembali'
    ];
    protected $dates = [
        'tanggal'
    ];

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
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'divisi_id', 'id');
    }

    public function coa()
    {
        return $this->belongsTo(Coa::class, 'coa', 'coa_id');
    }

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id', 'project_id');
    }

    public function bkkHeader()
    {
        return $this->belongsTo(BKKHeader::class, 'bkk_header_id', 'id');
    }

    public function bkk()
    {
        return $this->belongsTo(BKK::class, 'id_bkk', 'id');
    }

    public function scopeBukanPengembalianSaldo($query)
    {
        return $query->where('deskripsi', '!=', "PENGEMBALIAN SALDO PENGAJUAN");
    }

    public function scopeStatusProgressAndKlaim($query)
    {
        return $query->whereIn('status', [4, 7]);
    }

    public function scopeStatusProgress($query)
    {
        return $query->whereIn('status', [4]);
    }

    public function scopeStatusKlaimAndSetBKK($query)
    {
        return $query->whereIn('status', [7, 8]);
    }

    public function scopeStatusKlaim($query)
    {
        return $query->whereIn('status', [7]);
    }

    public function scopeStatusSetBKK($query)
    {
        return $query->whereIn('status', [8]);
    }

    public function scopeGetUserId($query)
    {
        return $query->select('user_id')->groupBy('user_id');
    }

    public function scopeGetCoaId($query)
    {
        return $query->select('coa')->groupBy('coa');
    }

    public function scopeSearchByDateRange($query, string|null $start = null, string|null $end = null)
    {
        // $start = ($start) ? $start : Carbon::now()->firstOfYear()->format('Y-m-d');
        // $end = ($end) ? $end : Carbon::now()->endOfYear()->format('Y-m-d');
        return ($start or $end) ? $query->whereBetween('tanggal', [$start, $end]) : $query;
    }

    public function scopeSearchByCompany($query, string|null $company)
    {
        return ($company) ? $query->where('pembebanan', $company) : $query;
    }

    public function scopeSearchByStatus($query, string|null $status)
    {
        return ($status) ? $query->where('status', $status) : $query;
    }

    public function scopeSearchByUser($query, string|null $id_user)
    {
        return ($id_user) ? $query->where('user_id', $id_user) : $query;
    }

    public function scopeSearchByUnit($query, string|null $unit)
    {
        return ($unit) ? $query->where('divisi_id', $unit) : $query;
    }

    public function scopeSearchByCoa($query, string|null $coa)
    {
        return ($coa) ? $query->where('coa', $coa) : $query;
    }

    public function scopeSearchByProject($query, string|null $project)
    {
        return ($project) ? $query->where('project_id', $project) : $query;
    }

    public function scopeNotDisabled($query)
    {
        return $query->where('status', '!=', 6);
    }

    public function scopeNotPribadi($query)
    {
        return $query->whereNotIn('project_id', [111,112])->orWhereNotIn('pembebanan',[28,29,30]);
    }

    public function scopeIsPribadi($query)
    {
        return $query->whereIn('project_id', [111,112])->orWhereIn('pembebanan',[28,29,30]);
    }
}
