<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BKKHeader extends Model
{
    use HasFactory;
    protected $table = 'bkk_header';
    protected $connection = 'mysql2';
    public $timestamps = false;
    protected $fillable = [
        'bank_id',
        'name',
        'tanggal',
        'periode',
        'partner',
        'otorisasi',
        'project_id',
        'layer_cashflow_id',
        'created_by',
        'created_at',
        'status',
        'uraian',
        'status_jurnal',
        'giro_nomor',
        'giro_tanggal',
        'giro_jatuh_tempo',
        'otorisasi_tanggal',
        'otorisasi_by',
        'giro_cair',
        'reference',
        'flag_sudah_rekon',
        'tanggal_rekon'
    ];

    public function bkk()
    {
        return $this->hasMany(BKK::class, 'bkk_header_id', 'id');
    }

    public function project() : BelongsTo {
        return $this->setConnection('mysql')->belongsTo(Project::class, 'project_id','project_id');
    }

    public function bank() : BelongsTo {
        return $this->setConnection('mysql')->belongsTo(Rekening::class, 'bank_id', 'bank_id');
    }

    public function otorisator() : BelongsTo {
        return $this->setConnection('mysql')->belongsTo(User::class, 'otorisasi_by', 'id');
    }

    public function creator() : BelongsTo {
        return $this->setConnection('mysql')->belongsTo(User::class, 'created_by', 'id');
    }

    public function scopeSearchByBarcode($query, string|null $barcode)
    {
        return ($barcode) ? $query->where('id', $barcode) : $query;
    }

    public function scopeSearchByCompany($query, string|null $company)
    {
        return ($company) ? $query->whereRelation('project', 'project_company_id', $company) : $query;
    }

    public function scopeNotPribadi($query)
    {
        return $query->whereNotIn('project_id', [111,112]);
    }

    public function scopeIsPribadi($query)
    {
        return $query->whereIn('project_id', [111,112]);
    }
}
