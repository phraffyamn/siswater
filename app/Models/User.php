<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'role', 'nip', 'jabatan', 'is_active',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function isAdmin(): bool { return $this->role === 'admin'; }
    public function isPPS(): bool { return $this->role === 'pps'; }
    public function isPHPT(): bool { return $this->role === 'phpt'; }
    public function isTU(): bool { return $this->role === 'tu'; }

    public function getRoleLabelAttribute(): string
    {
        return match($this->role) {
            'admin' => 'Administrator',
            'pps'   => 'Pengendalian & Penanganan Sengketa',
            'phpt'  => 'Penetapan Hak & Pendaftaran Tanah',
            'tu'    => 'Tata Usaha',
            default => $this->role,
        };
    }

    public function permintaanSebagaiPemohon()
    {
        return $this->hasMany(PermintaanWarkah::class, 'pemohon_id');
    }

    public function permintaanDiverifikasiTU()
    {
        return $this->hasMany(PermintaanWarkah::class, 'approved_by_tu');
    }

    public function permintaanDiprosesPhpt()
    {
        return $this->hasMany(PermintaanWarkah::class, 'processed_by_phpt');
    }
}
