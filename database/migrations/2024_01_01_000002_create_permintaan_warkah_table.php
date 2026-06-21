<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('permintaan_warkah', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_nota')->unique();
            $table->foreignId('pemohon_id')->constrained('users')->onDelete('cascade');
            $table->string('perihal');
            $table->text('keterangan')->nullable();
            $table->enum('status', [
                'menunggu_tu',
                'disetujui_tu',
                'ditolak_tu',
                'diproses_phpt',
                'warkah_tersedia',
                'dikembalikan',
                'selesai'
            ])->default('menunggu_tu');
            $table->timestamp('tanggal_permintaan')->useCurrent();
            $table->timestamp('deadline')->nullable();
            $table->foreignId('approved_by_tu')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at_tu')->nullable();
            $table->text('catatan_tu')->nullable();
            $table->foreignId('processed_by_phpt')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('processed_at_phpt')->nullable();
            $table->text('catatan_phpt')->nullable();
            $table->timestamp('dikembalikan_at')->nullable();
            $table->text('catatan_pengembalian')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('permintaan_warkah');
    }
};
