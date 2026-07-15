<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\OrderCancelled;
use App\Mail\OrderCompleted;
use App\Mail\OrderPaymentSuccess;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ProductItem;
use App\Models\ProductVariant;
use App\Services\PakasirService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;

class AdminOrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with('items')->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('order_number', 'like', '%' . $request->search . '%')
                    ->orWhere('customer_name', 'like', '%' . $request->search . '%')
                    ->orWhere('customer_email', 'like', '%' . $request->search . '%');
            });
        }

        $orders = $query->paginate(20)->withQueryString();

        return Inertia::render('Admin/Orders/Index', [
            'orders' => $orders,
            'filters' => $request->only('search', 'status'),
        ]);
    }

    public function show(Order $order)
    {
        $order->load(['items.variant', 'items.assignedItems', 'user']);

        // Ambil available stock + yang sudah di-reserve untuk order ini
        $availableStock = [];
        if ($order->status === 'paid') {
            $variantIds = $order->items->pluck('variant_id')->unique();
            $reservedItemIds = $order->items->flatMap->assignedItems->pluck('id');

            foreach ($variantIds as $vid) {
                $availableStock[$vid] = ProductItem::where('variant_id', $vid)
                    ->where(function ($q) use ($reservedItemIds) {
                        $q->where('status', 'available')
                            ->orWhere(function ($q2) use ($reservedItemIds) {
                                $q2->where('status', 'reserved')->whereIn('id', $reservedItemIds);
                            });
                    })
                    ->get(['id', 'content']);
            }
        }

        return Inertia::render('Admin/Orders/Show', [
            'order' => $order,
            'availableStock' => $availableStock,
        ]);
    }

    // Simpan assign akun ke order items
    public function assignItems(Request $request, Order $order)
    {
        if ($order->status !== 'paid') {
            return back()->with('error', 'Hanya order yang sudah dibayar yang bisa di-assign.');
        }

        $validated = $request->validate([
            'assignments' => 'required|array',
            'assignments.*.order_item_id' => 'required|exists:order_items,id',
            'assignments.*.product_item_ids' => 'required|array',
            'assignments.*.product_item_ids.*' => 'exists:product_items,id',
        ]);

        // Kumpulkan semua product_item_ids global untuk cek duplikat
        $allItemIds = collect($validated['assignments'])->flatMap->product_item_ids;
        if ($allItemIds->duplicates()->isNotEmpty()) {
            return back()->with('error', 'Tidak boleh memilih akun yang sama untuk lebih dari satu item.');
        }

        DB::beginTransaction();

        try {
            foreach ($validated['assignments'] as $assignment) {
                $orderItem = OrderItem::where('id', $assignment['order_item_id'])
                    ->where('order_id', $order->id)
                    ->firstOrFail();

                $newItemIds = collect($assignment['product_item_ids']);

                // Release item lama yang tidak dipilih lagi
                $currentAssigned = $orderItem->assignedItems()->pluck('product_items.id');
                $toRelease = $currentAssigned->diff($newItemIds);
                if ($toRelease->isNotEmpty()) {
                    ProductItem::whereIn('id', $toRelease)
                        ->update(['status' => 'available', 'order_id' => null, 'order_item_id' => null]);
                }

                // Assign item baru
                $toAssign = $newItemIds->diff($currentAssigned);
                if ($toAssign->isNotEmpty()) {
                    ProductItem::whereIn('id', $toAssign)
                        ->update(['status' => 'reserved', 'order_id' => $order->id, 'order_item_id' => $orderItem->id]);
                }
            }

            // Recalculate stock
            $variantIds = $order->items->pluck('variant_id')->unique();
            foreach ($variantIds as $vid) {
                ProductVariant::find($vid)?->recalculateStock();
            }

            DB::commit();

            return back()->with('success', 'Akun berhasil disimpan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyimpan akun: ' . $e->getMessage());
        }
    }

    // Selesaikan order (finalize) — items harus sudah di-assign sesuai qty
    public function complete(Order $order)
    {
        if ($order->status !== 'paid') {
            return back()->with('error', 'Hanya order yang sudah dibayar yang bisa diselesaikan.');
        }

        // Cek setiap order item sudah assign sesuai quantity
        foreach ($order->items as $item) {
            $assigned = $item->assignedItems()->count();
            if ($assigned < $item->quantity) {
                return back()->with('error', "\"{$item->product_name} ({$item->variant_name})\" baru assign {$assigned}/{$item->quantity} akun.");
            }
        }

        DB::beginTransaction();

        try {
            // Mark semua reserved → sold
            foreach ($order->items as $item) {
                $item->assignedItems()->update(['status' => 'sold']);
            }

            // Recalculate stock
            $variantIds = $order->items->pluck('variant_id')->unique();
            foreach ($variantIds as $vid) {
                ProductVariant::find($vid)?->recalculateStock();
            }

            $order->update([
                'status' => 'completed',
                'completed_at' => now(),
            ]);

            DB::commit();

            // Email notification (queued)
            Mail::to($order->customer_email)->queue(new OrderCompleted($order));

            return back()->with('success', 'Order berhasil diselesaikan & akun dikirim.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyelesaikan order: ' . $e->getMessage());
        }
    }

    // Verifikasi pembayaran manual
    public function verifyPayment(Order $order)
    {
        if ($order->status !== 'pending') {
            return back()->with('error', 'Order tidak dalam status pending.');
        }

        $order->update([
            'status' => 'paid',
            'paid_at' => now(),
        ]);

        // Email notification (queued)
        Mail::to($order->customer_email)->queue(new OrderPaymentSuccess($order));

        return back()->with('success', 'Pembayaran berhasil diverifikasi.');
    }

    // Kirim ulang email notifikasi sesuai status order
    public function resendEmail(Order $order)
    {
        try {
            $order->load('items.assignedItems');

            if ($order->status === 'completed') {
                Mail::to($order->customer_email)->queue(new OrderCompleted($order));
            } elseif ($order->status === 'paid') {
                Mail::to($order->customer_email)->queue(new OrderPaymentSuccess($order));
            } elseif ($order->status === 'pending') {
                Mail::to($order->customer_email)->queue(new \App\Mail\OrderPendingPayment($order));
            } elseif ($order->status === 'cancelled') {
                Mail::to($order->customer_email)->queue(new OrderCancelled($order));
            } else {
                return back()->with('error', 'Status order tidak mendukung pengiriman email.');
            }

            return back()->with('success', 'Email notifikasi berhasil dikirim ulang ke ' . $order->customer_email);
        } catch (\Exception $e) {
            Log::error('Gagal kirim ulang email', ['order' => $order->order_number, 'error' => $e->getMessage()]);
            return back()->with('error', 'Gagal mengirim email: ' . $e->getMessage());
        }
    }

    // Update catatan admin
    public function updateNotes(Request $request, Order $order)
    {
        $validated = $request->validate([
            'notes' => 'nullable|string',
        ]);

        $order->update($validated);

        return back()->with('success', 'Catatan berhasil diperbarui.');
    }

    // Cancel order
    public function cancel(Order $order, PakasirService $pakasir)
    {
        if (!in_array($order->status, ['pending', 'paid'])) {
            return back()->with('error', 'Order tidak bisa dibatalkan.');
        }

        DB::beginTransaction();

        try {
            // Cancel di Pakasir
            if ($order->status === 'pending' && $order->payment_gateway_status !== 'expired') {
                try {
                    $pakasir->cancelTransaction($order->id, $order->total_amount);
                } catch (\Exception $e) {
                    Log::warning("Failed to cancel Pakasir for order {$order->order_number}: {$e->getMessage()}");
                }
            }

            // Kembalikan stok + hitung ulang
            $variantIds = $order->items->pluck('variant_id')->unique();

            foreach ($order->items as $item) {
                $item->assignedItems()->update([
                    'status' => 'available',
                    'order_id' => null,
                    'order_item_id' => null,
                ]);
            }

            foreach ($variantIds as $vid) {
                ProductVariant::find($vid)?->recalculateStock();
            }

            $order->update([
                'status' => 'cancelled',
                'canceled_at' => now(),
            ]);

            DB::commit();

            // Kirim email
            Mail::to($order->customer_email)->queue(new OrderCancelled($order));

            return back()->with('success', 'Order berhasil dibatalkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Cancel order failed: {$e->getMessage()}");
            return back()->with('error', 'Gagal membatalkan order.');
        }
    }
}
