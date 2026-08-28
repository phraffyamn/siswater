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

    public function bisaDipratinjau(): bool
    {
        return in_array(strtolower($this->file_type), ['pdf', 'jpg', 'jpeg', 'png'], true);
    }

    public function getMimeTypeAttribute(): string
    {
        return match (strtolower($this->file_type)) {
            'pdf'         => 'application/pdf',
            'png'         => 'image/png',
            'jpg', 'jpeg' => 'image/jpeg',
            default       => 'application/octet-stream',
        };
    }

    /** Berkas gambar dirender dengan <img>, PDF dengan <iframe>. */
    public function isGambar(): bool
    {
        return in_array(strtolower($this->file_type), ['jpg', 'jpeg', 'png'], true);
    }

    public function getIkonAttribute(): string
    {
        return match (strtolower($this->file_type)) {
            'pdf'         => 'bi-file-earmark-pdf-fill',
            'zip'         => 'bi-file-earmark-zip-fill',
            'jpg', 'jpeg', 'png' => 'bi-file-earmark-image-fill',
            default       => 'bi-file-earmark-fill',
        };
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
