<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['admin', 'pps', 'phpt', 'tu'])->default('pps')->after('email');
            $table->string('nip', 20)->nullable()->after('role');
            $table->string('jabatan')->nullable()->after('nip');
            $table->boolean('is_active')->default(true)->after('jabatan');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'nip', 'jabatan', 'is_active']);
        });
    }
};
