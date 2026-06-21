<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AktivitasLog extends Model
{
    protected $table = 'aktivitas_logs';

    protected $fillable = [
        'permintaan_id', 'user_id', 'aksi',
        'catatan', 'status_sebelum', 'status_sesudah',
    ];

    public function permintaan(): BelongsTo
    {
        return $this->belongsTo(PermintaanWarkah::class, 'permintaan_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
