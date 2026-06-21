<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PermintaanWarkah extends Model
{
    protected $table = 'permintaan_warkah';

    protected $fillable = [
        'nomor_nota', 'pemohon_id', 'perihal', 'keterangan', 'status',
        'tanggal_permintaan', 'deadline',
        'approved_by_tu', 'approved_at_tu', 'catatan_tu',
        'processed_by_phpt', 'processed_at_phpt', 'catatan_phpt',
        'dikembalikan_at', 'catatan_pengembalian',
    ];

    protected $casts = [
        'tanggal_permintaan' => 'datetime',
        'deadline'           => 'datetime',
        'approved_at_tu'     => 'datetime',
        'processed_at_phpt'  => 'datetime',
        'dikembalikan_at'    => 'datetime',
    ];

    public static function generateNomorNota(): string
    {
        $year  = now()->format('Y');
        $month = now()->format('m');
        $count = self::whereYear('created_at', $year)->whereMonth('created_at', $month)->count() + 1;
        return sprintf('ND-PPS/%s/%s/%04d', $month, $year, $count);
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'menunggu_tu'      => 'Menunggu Persetujuan TU',
            'disetujui_tu'     => 'Disetujui TU',
            'ditolak_tu'       => 'Ditolak TU',
            'diproses_phpt'    => 'Diproses PHPT',
            'warkah_tersedia'  => 'Warkah Tersedia',
            'dikembalikan'     => 'Dikembalikan',
            'selesai'          => 'Selesai',
            default            => $this->status,
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'menunggu_tu'      => 'warning',
            'disetujui_tu'     => 'info',
            'ditolak_tu'       => 'danger',
            'diproses_phpt'    => 'primary',
            'warkah_tersedia'  => 'success',
            'dikembalikan'     => 'secondary',
            'selesai'          => 'dark',
            default            => 'secondary',
        };
    }

    public function isOverdue(): bool
    {
        return $this->deadline && now()->isAfter($this->deadline)
            && !in_array($this->status, ['warkah_tersedia', 'dikembalikan', 'selesai']);
    }

    public function pemohon(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pemohon_id');
    }

    public function approvedByTu(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by_tu');
    }

    public function processedByPhpt(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by_phpt');
    }

    public function items(): HasMany
    {
        return $this->hasMany(PermintaanItem::class, 'permintaan_id');
    }

    public function files(): HasMany
    {
        return $this->hasMany(WarkahFile::class, 'permintaan_id');
    }

    public function logs(): HasMany
    {
        return $this->hasMany(AktivitasLog::class, 'permintaan_id')->orderBy('created_at', 'desc');
    }
}
