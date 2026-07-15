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
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->restrictOnDelete();
            $table->string('name');                        // Private 1 Bulan, Shared 3 Bulan, dll
            $table->string('slug')->nullable();            // private-1-bulan (opsional, untuk URL)
            $table->text('description')->nullable();       // Keterangan variant
            $table->decimal('price', 12, 2);               // Harga asli
            $table->decimal('discount_price', 12, 2)->nullable(); // Harga diskon
            $table->integer('stock_count')->default(0);    // Jumlah stok tersedia (auto-calculated)
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
        Schema::dropIfExists('product_variants');
    }
};
