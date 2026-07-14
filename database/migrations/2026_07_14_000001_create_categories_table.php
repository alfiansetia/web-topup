<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');                        // Nama kategori: Streaming, Gaming, dll
            $table->string('slug')->unique();              // URL-friendly: streaming, gaming
            $table->string('icon')->nullable();            // Icon class atau emoji
            $table->text('description')->nullable();       // Deskripsi singkat
            $table->boolean('is_active')->default(true);   // Aktif/nonaktif
            $table->integer('sort_order')->default(0);     // Urutan tampil
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
