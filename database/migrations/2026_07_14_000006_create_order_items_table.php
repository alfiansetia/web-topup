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
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained();
            $table->foreignId('variant_id')->constrained('product_variants');
            $table->foreignId('product_item_id')->nullable();  // Akun yang diberikan ke user
            $table->string('product_name');                     // Snapshot: "YouTube Premium"
            $table->string('variant_name');                     // Snapshot: "Private 1 Bulan"
            $table->decimal('price', 12, 2);                    // Snapshot harga saat beli
            $table->integer('quantity')->default(1);            // Jumlah
            $table->decimal('subtotal', 12, 2);                 // price * quantity
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
