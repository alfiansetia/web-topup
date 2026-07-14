<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $orders = $user->orders()->latest()->get();

        return Inertia::render('Dashboard/Index', [
            'stats' => [
                'total' => $orders->count(),
                'pending' => $orders->where('status', 'pending')->count(),
                'completed' => $orders->whereIn('status', ['completed', 'delivered'])->count(),
                'total_spent' => $orders->whereIn('status', ['completed', 'delivered'])->sum('total_amount'),
            ],
            'recentOrders' => $orders->take(5)->map(function ($order) {
                return [
                    'order_number' => $order->order_number,
                    'total_amount' => $order->total_amount,
                    'status' => $order->status,
                    'created_at' => $order->created_at->format('d M Y H:i'),
                    'items' => $order->items->map(function ($item) {
                        return [
                            'product_name' => $item->product_name,
                            'variant_name' => $item->variant_name,
                            'quantity' => $item->quantity,
                            'subtotal' => $item->subtotal,
                        ];
                    }),
                ];
            }),
        ]);
    }

    public function orders()
    {
        $user = Auth::user();
        $orders = $user->orders()->with('items')->latest()->paginate(10);

        return Inertia::render('Dashboard/Orders', [
            'orders' => $orders->through(function ($order) {
                return [
                    'id' => $order->id,
                    'order_number' => $order->order_number,
                    'customer_name' => $order->customer_name,
                    'customer_email' => $order->customer_email,
                    'customer_phone' => $order->customer_phone,
                    'notes' => $order->notes,
                    'total_amount' => $order->total_amount,
                    'status' => $order->status,
                    'created_at' => $order->created_at->format('d M Y H:i'),
                    'items' => $order->items->map(function ($item) {
                        return [
                            'product_name' => $item->product_name,
                            'variant_name' => $item->variant_name,
                            'quantity' => $item->quantity,
                            'subtotal' => $item->subtotal,
                        ];
                    }),
                ];
            }),
        ]);
    }

    public function profile()
    {
        return Inertia::render('Dashboard/Profile');
    }

    public function updateProfile(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'telegram_id' => 'nullable|string|max:100',
        ]);

        $user = Auth::user();
        $user->update([
            'name' => $request->name,
            'telegram_id' => $request->telegram_id,
        ]);

        return back()->with('success', 'Profil berhasil diperbarui.');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = Auth::user();

        $user->update([
            'password' => bcrypt($request->password),
        ]);

        return back()->with('success', 'Password berhasil diubah.');
    }
}
