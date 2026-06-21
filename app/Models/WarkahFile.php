<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WarkahFile extends Model
{
    protected $table = 'warkah_files';

    protected $fillable = [
        'permintaan_id', 'permintaan_item_id', 'nama_file',
        'file_path', 'file_type', 'file_size', 'uploaded_by',
    ];

    public function getFileSizeHumanAttribute(): string
    {
        $bytes = $this->file_size;
        if ($bytes >= 1048576) return round($bytes / 1048576, 2) . ' MB';
        if ($bytes >= 1024)    return round($bytes / 1024, 2) . ' KB';
        return $bytes . ' B';
    }

    public function permintaan(): BelongsTo
    {
        return $this->belongsTo(PermintaanWarkah::class, 'permintaan_id');
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(PermintaanItem::class, 'permintaan_item_id');
    }

    public function uploadedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
