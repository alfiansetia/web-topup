<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\PakasirService;
use App\Services\TelegramService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PakasirWebhookController extends Controller
{
    public function __construct(
        protected PakasirService $pakasir,
        protected TelegramService $telegram,
    ) {}

    /**
     * Handle Pakasir webhook callback.
     * Pakasir POSTs: { order_id, amount, status, method }
     * status = "paid" when payment is successful.
     */
    public function handle(Request $request)
    {
        Log::info('Pakasir webhook received', $request->all());

        $orderId = $request->input('order_id');
        $amount  = $request->input('amount');
        $status  = $request->input('status'); // "paid"

        if (!$orderId || !$amount) {
            return response()->json(['error' => 'Missing required fields'], 400);
        }

        $order = Order::where('order_number', $orderId)->first();

        if (!$order) {
            Log::warning('Pakasir webhook: order not found', ['order_id' => $orderId]);
            return response()->json(['error' => 'Order not found'], 404);
        }

        // Verify amount matches
        if ((int) $order->total_amount !== (int) $amount) {
            Log::warning('Pakasir webhook: amount mismatch', [
                'order_id'        => $orderId,
                'order_amount'    => $order->total_amount,
                'webhook_amount'  => $amount,
            ]);
            // Still process — Pakasir amount may include fee
        }

        if ($status === 'paid' && $order->status === 'pending') {
            $order->update([
                'status'                 => 'paid',
                'paid_at'                => now(),
                'payment_gateway_status' => 'paid',
            ]);

            // Release reserved items → sold
            $order->items()->each(function ($item) {
                $item->productItem()->update([
                    'status'     => 'sold',
                    'sold_at'    => now(),
                ]);
            });

            // Telegram notification
            $this->telegram->notifyPaid($order);

            Log::info('Order paid via Pakasir', ['order_number' => $order->order_number]);
        }

        return response()->json(['success' => true]);
    }
}
