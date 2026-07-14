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
        Schema::create('product_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('variant_id')->constrained('product_variants')->cascadeOnDelete();
            $table->text('content');                                // Isi akun: email:password atau data lainnya
            $table->enum('status', ['available', 'sold', 'reserved'])->default('available');
            $table->foreignId('order_id')->nullable();              // Terkait order saat terjual
            $table->foreignId('order_item_id')->nullable();
            $table->timestamps();

            $table->index('status');                                // Index untuk query cepat cek stok
            $table->index(['product_id', 'variant_id', 'status']);  // Index untuk cek stok per variant
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_items');
    }
};
