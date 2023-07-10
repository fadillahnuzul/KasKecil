<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Divisi extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'user_level';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'initial',
        'status',
        'created_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'update_at' => 'datetime',
    ];

    /**
     * Get the user that owns the Divisi
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id', 'id');
    }

    public function kas()
    {
        return $this->hasMany(Kas::class, 'divisi_id', 'id');
    }
    public function pengajuan()
    {
        return $this->hasMany(Pengajuan::class, 'divisi_id', 'id');
    }
}
