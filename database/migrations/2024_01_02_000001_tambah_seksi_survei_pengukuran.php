<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // role semula enum. Diubah menjadi string biasa supaya penambahan
        // seksi berikutnya tidak lagi memerlukan migrasi skema.
        Schema::table('users', function (Blueprint $table) {
            $table->string('role', 20)->default('pps')->change();
        });

        // Menentukan seksi mana yang harus menyiapkan warkah: PHPT atau SP.
        Schema::table('permintaan_warkah', function (Blueprint $table) {
            $table->string('seksi_tujuan', 10)->default('phpt')->after('perihal');
        });
    }

    public function down(): void
    {
        Schema::table('permintaan_warkah', function (Blueprint $table) {
            $table->dropColumn('seksi_tujuan');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['admin', 'pps', 'phpt', 'tu'])->default('pps')->change();
        });
    }
};
