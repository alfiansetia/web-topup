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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->string('name');                        // YouTube Premium, Netflix, Spotify
            $table->string('slug')->unique();              // youtube-premium, netflix
            $table->text('description')->nullable();       // Deskripsi produk
            $table->text('features')->nullable();          // Fitur-fitur (JSON array)
            $table->string('checkout_instruction')->nullable();
            $table->string('image')->nullable();           // Gambar produk
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
        Schema::dropIfExists('products');
    }
};
