<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ProductItem;
use Illuminate\Http\Request;
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
        $order->load(['items.product', 'items.variant', 'items.productItem', 'user']);

        return Inertia::render('Admin/Orders/Show', [
            'order' => $order,
        ]);
    }

    // Tandai sebagai paid + kirim akun ke user
    public function complete(Order $order)
    {
        if ($order->status !== 'paid') {
            return back()->with('error', 'Hanya order yang sudah dibayar yang bisa diselesaikan.');
        }

        $order->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        return back()->with('success', 'Order berhasil diselesaikan.');
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

        return back()->with('success', 'Pembayaran berhasil diverifikasi.');
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
    public function cancel(Order $order)
    {
        if (!in_array($order->status, ['pending', 'paid'])) {
            return back()->with('error', 'Order tidak bisa dibatalkan.');
        }

        // Kembalikan stok
        foreach ($order->items as $item) {
            if ($item->product_item_id) {
                ProductItem::where('id', $item->product_item_id)
                    ->update(['status' => 'available', 'order_id' => null]);
            }
        }

        $order->update(['status' => 'cancelled']);

        return back()->with('success', 'Order berhasil dibatalkan.');
    }
}
