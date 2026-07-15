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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete(); // nullable = guest checkout
            $table->string('order_number')->unique();       // INV-20260714-XXXXX

            // ── Customer Info (guest / user) ──
            $table->string('customer_name');                // Nama pembeli
            $table->string('customer_email');               // Email pembeli (untuk guest lookup)
            $table->string('customer_phone')->nullable();   // No WhatsApp / HP

            // ── Order Detail ──
            $table->decimal('total_amount', 12, 2);         // Total harga
            $table->enum('status', [
                'pending',      // Menunggu pembayaran
                'paid',         // Sudah bayar, auto-detect dari callback Pakasir
                'completed',    // Selesai, akun sudah dikirim
                'cancelled',    // Dibatalkan / expired
                'refunded',     // Refund
            ])->default('pending');

            // ── Pakasir Payment Gateway ──
            $table->string('payment_ref')->nullable();      // Ref ID dari Pakasir (unique per transaksi)
            $table->string('payment_method')->nullable();   // Metode dari Pakasir: QRIS, DANA, OVO, dll
            $table->string('payment_url')->nullable();      // Link checkout Pakasir (redirect user ke sini)
            $table->string('payment_channel')->nullable();  // Channel pembayaran (bca, bni, gopay, dll)
            $table->decimal('payment_fee', 12, 2)->default(0); // Fee dari payment gateway
            $table->string('payment_gateway_status')->nullable(); // Status dari Pakasir: success, pending, failed
            $table->text('payment_gateway_response')->nullable(); // Raw response Pakasir (JSON, untuk debug)

            // ── Admin / Manual ──
            $table->string('payment_proof')->nullable();    // Bukti bayar manual (opsional, kalau manual transfer)
            $table->text('notes')->nullable();              // Catatan admin
            $table->timestamp('paid_at')->nullable();       // Waktu pembayaran confirmed
            $table->timestamp('completed_at')->nullable();  // Waktu selesai
            $table->timestamp('canceled_at')->nullable();  // Waktu Dibatalkan
            $table->timestamps();

            $table->index('status');
            $table->index('payment_ref');
            $table->index('customer_email');
            $table->index(['user_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
