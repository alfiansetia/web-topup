<?php

namespace App\Commands;

use App\Mail\OrderCancelled;
use App\Models\Order;
use App\Models\ProductVariant;
use App\Services\PakasirService;
use App\Services\TelegramBotService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class CancelExpiredOrders extends Command
{
    protected $signature = 'orders:cancel-expired';
    protected $description = 'Batalkan otomatis pesanan pending yang sudah kedaluwarsa (>1 jam)';

    public function handle(PakasirService $pakasir): int
    {
        $expiredOrders = Order::with('items.assignedItems')
            ->where('status', 'pending')
            ->where('created_at', '<', now()->subHour())
            ->get();

        if ($expiredOrders->isEmpty()) {
            $this->info('Tidak ada pesanan kedaluwarsa.');
            return self::SUCCESS;
        }

        $count = 0;

        foreach ($expiredOrders as $order) {
            DB::beginTransaction();
            try {
                // Try to cancel on Pakasir side
                try {
                    $pakasir->cancelTransaction($order->order_number, (int) $order->total_amount);
                } catch (\Exception $e) {
                    Log::warning('Failed to cancel Pakasir transaction', [
                        'order_number' => $order->order_number,
                        'error'        => $e->getMessage(),
                    ]);
                }

                // Release reserved stock
                $variantIds = [];
                foreach ($order->items as $item) {
                    $item->assignedItems()->update([
                        'status'        => 'available',
                        'order_id'      => null,
                        'order_item_id' => null,
                    ]);
                    if (!in_array($item->variant_id, $variantIds)) {
                        $variantIds[] = $item->variant_id;
                    }
                }

                // Recalculate stock for affected variants
                foreach ($variantIds as $vid) {
                    ProductVariant::find($vid)?->recalculateStock();
                }

                $order->update([
                    'status'                 => 'cancelled',
                    'canceled_at'            => now(),
                    'payment_gateway_status' => 'expired',
                ]);

                // Email notification (queued)
                Mail::to($order->customer_email)->queue(new OrderCancelled($order));

                // Telegram notification ke user
                app(TelegramBotService::class)->notifyOrderStatus($order);

                DB::commit();
                $count++;
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Failed to cancel expired order', [
                    'order_number' => $order->order_number,
                    'error'        => $e->getMessage(),
                ]);
            }
        }

        $this->info("Berhasil membatalkan {$count} pesanan kedaluwarsa.");
        return self::SUCCESS;
    }
}
