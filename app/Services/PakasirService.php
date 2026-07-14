<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PakasirService
{
    protected string $baseUrl = 'https://app.pakasir.com/api';
    protected string $slug;
    protected string $apiKey;

    public function __construct()
    {
        $this->slug    = config('payment.pakasir_slug');
        $this->apiKey  = config('payment.pakasir_secret_key');
    }

    /**
     * Create a QRIS transaction.
     *
     * @return array{payment_number: string, amount: int, fee: int, total_payment: int, expired_at: string}|null
     */
    public function createQrisTransaction(string $orderId, int $amount): ?array
    {
        $response = Http::timeout(15)->post("{$this->baseUrl}/transactioncreate/qris", [
            'project'    => $this->slug,
            'order_id'   => $orderId,
            'amount'     => $amount,
            'api_key'    => $this->apiKey,
            'expired_in' => 60, // 1 jam
        ]);

        if ($response->successful() && $response->json('payment')) {
            return $response->json('payment');
        }

        Log::error('Pakasir createQrisTransaction failed', [
            'order_id' => $orderId,
            'amount'   => $amount,
            'status'   => $response->status(),
            'body'     => $response->body(),
        ]);

        return null;
    }

    /**
     * Get transaction detail from Pakasir.
     */
    public function getTransactionDetail(string $orderId, int $amount): ?array
    {
        $response = Http::timeout(15)->get("{$this->baseUrl}/transactiondetail", [
            'project' => $this->slug,
            'order_id' => $orderId,
            'amount'   => $amount,
            'api_key'  => $this->apiKey,
        ]);

        if ($response->successful() && $response->json('transaction')) {
            return $response->json('transaction');
        }

        Log::error('Pakasir getTransactionDetail failed', [
            'order_id' => $orderId,
            'status'   => $response->status(),
            'body'     => $response->body(),
        ]);

        return null;
    }

    /**
     * Cancel a transaction.
     */
    public function cancelTransaction(string $orderId, int $amount): bool
    {
        $response = Http::timeout(15)->post("{$this->baseUrl}/transactioncancel", [
            'project' => $this->slug,
            'order_id' => $orderId,
            'amount'   => $amount,
            'api_key'  => $this->apiKey,
        ]);

        return $response->successful();
    }

    /**
     * Build a direct payment URL (fallback / link approach).
     */
    public function getPaymentUrl(string $orderId, int $amount): string
    {
        return "https://app.pakasir.com/pay/{$this->slug}/{$amount}?order_id={$orderId}&qris_only=1";
    }
}
