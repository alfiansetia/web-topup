<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\PakasirService;
use App\Services\TelegramService;
use App\Mail\OrderPaymentSuccess;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

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

        if (($status === 'paid' || $status === 'completed') && $order->status === 'pending') {
            $order->update([
                'status'                 => 'paid',
                'paid_at'                => now(),
                'payment_gateway_status' => 'paid',
            ]);

            // Items tetap 'reserved' — admin akan assign & mark sold saat selesaikan order

            // Telegram notification
            $this->telegram->notifyPaid($order);

            // Email notification (queued)
            Mail::to($order->customer_email)->queue(new OrderPaymentSuccess($order));

            Log::info('Order paid via Pakasir', ['order_number' => $order->order_number]);
        }

        // Handle cancelled / expired from payment gateway
        if (($status === 'cancelled' || $status === 'expired' || $status === 'failed') && $order->status === 'pending') {
            $order->update([
                'status'                 => 'cancelled',
                'canceled_at'            => now(),
                'payment_gateway_status' => $status,
            ]);

            Log::info('Order cancelled via Pakasir callback', [
                'order_number' => $order->order_number,
                'pg_status'    => $status,
            ]);
        }

        return response()->json(['success' => true]);
    }
}
