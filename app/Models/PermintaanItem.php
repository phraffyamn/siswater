<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PermintaanItem extends Model
{
    protected $table = 'permintaan_items';

    protected $fillable = [
        'permintaan_id', 'nama_warkah', 'nomor_hak',
        'nama_pemegang_hak', 'lokasi', 'keterangan',
    ];

    public function permintaan(): BelongsTo
    {
        return $this->belongsTo(PermintaanWarkah::class, 'permintaan_id');
    }

    public function files(): HasMany
    {
        return $this->hasMany(WarkahFile::class, 'permintaan_item_id');
    }
}
