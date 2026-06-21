<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('warkah_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('permintaan_id')->constrained('permintaan_warkah')->onDelete('cascade');
            $table->foreignId('permintaan_item_id')->nullable()->constrained('permintaan_items')->nullOnDelete();
            $table->string('nama_file');
            $table->string('file_path');
            $table->string('file_type', 20);
            $table->bigInteger('file_size')->default(0);
            $table->foreignId('uploaded_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('warkah_files');
    }
};
