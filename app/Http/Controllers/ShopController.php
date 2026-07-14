<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class ShopController extends Controller
{
    // Homepage: kategori + produk populer
    public function home()
    {
        $categories = Category::where('is_active', true)
            ->orderBy('sort_order')
            ->withCount(['products' => function ($q) {
                $q->where('is_active', true);
            }])
            ->get();

        $popularProducts = Product::where('is_active', true)
            ->with(['category', 'variants' => function ($q) {
                $q->where('is_active', true);
            }])
            ->withCount(['items as available_count' => function ($q) {
                $q->where('status', 'available');
            }])
            ->orderBy('sort_order')
            ->limit(4)
            ->get();

        return Inertia::render('Shop/Home', [
            'categories' => $categories,
            'products' => $popularProducts,
        ]);
    }

    // Semua produk dengan search
    public function products(Request $request)
    {
        $query = Product::where('is_active', true)
            ->with(['category', 'variants' => function ($q) {
                $q->where('is_active', true);
            }])
            ->withCount(['items as available_count' => function ($q) {
                $q->where('status', 'available');
            }]);

        // Search
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter by category
        if ($request->filled('category')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('slug', $request->input('category'));
            });
        }

        $products = $query->orderBy('sort_order')->paginate(12);

        $categories = Category::where('is_active', true)
            ->orderBy('sort_order')
            ->get(['id', 'name', 'slug']);

        return Inertia::render('Shop/Products', [
            'products' => $products,
            'categories' => $categories,
            'filters' => $request->only(['search', 'category']),
        ]);
    }

    // Produk per kategori
    public function category($slug)
    {
        $category = Category::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        $products = Product::where('category_id', $category->id)
            ->where('is_active', true)
            ->with(['category', 'variants' => function ($q) {
                $q->where('is_active', true);
            }])
            ->withCount(['items as available_count' => function ($q) {
                $q->where('status', 'available');
            }])
            ->orderBy('sort_order')
            ->get();

        return Inertia::render('Shop/Category', [
            'category' => $category,
            'products' => $products,
        ]);
    }

    // Detail produk
    public function product($slug)
    {
        $product = Product::where('slug', $slug)
            ->where('is_active', true)
            ->with(['category', 'variants' => function ($q) {
                $q->where('is_active', true)->orderBy('sort_order');
            }])
            ->withCount(['items as available_count' => function ($q) {
                $q->where('status', 'available');
            }])
            ->firstOrFail();

        return Inertia::render('Shop/Product', [
            'product' => $product,
        ]);
    }

    // Checkout - simpan order
    public function checkout(Request $request)
    {
        // Base validation
        $rules = [
            'variant_id' => 'required|exists:product_variants,id',
            'quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string|max:1000',
        ];

        // Guest wajib isi data diri
        if (!auth()->check()) {
            $rules['customer_name'] = 'required|string|max:255';
            $rules['customer_email'] = 'required|email|max:255';
            $rules['customer_phone'] = 'nullable|string|max:20';
        }

        $validated = $request->validate($rules);

        // Ambil data customer dari auth atau input
        if (auth()->check()) {
            $customerName = auth()->user()->name;
            $customerEmail = auth()->user()->email;
            $customerPhone = null;
        } else {
            $customerName = $validated['customer_name'];
            $customerEmail = $validated['customer_email'];
            $customerPhone = $validated['customer_phone'] ?? null;
        }

        $variant = ProductVariant::with('product')->findOrFail($validated['variant_id']);

        // Cek stok
        $availableItems = $variant->items()
            ->where('status', 'available')
            ->limit($validated['quantity'])
            ->get();

        if ($availableItems->count() < $validated['quantity']) {
            return back()->withErrors([
                'quantity' => 'Stok tidak cukup. Tersedia: ' . $availableItems->count(),
            ]);
        }

        $unitPrice = $variant->effective_price;
        $totalAmount = $unitPrice * $validated['quantity'];

        DB::beginTransaction();
        try {
            // Buat order
            $order = Order::create([
                'order_number' => Order::generateOrderNumber(),
                'user_id' => auth()->id(),
                'customer_name' => $customerName,
                'customer_email' => $customerEmail,
                'customer_phone' => $customerPhone,
                'total_amount' => $totalAmount,
                'status' => 'pending',
                'notes' => $validated['notes'] ?? null,
            ]);

            // Buat order items & reserve stock
            foreach ($availableItems as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $variant->product_id,
                    'variant_id' => $variant->id,
                    'product_item_id' => $item->id,
                    'product_name' => $variant->product->name,
                    'variant_name' => $variant->name,
                    'price' => $unitPrice,
                    'quantity' => 1,
                    'subtotal' => $unitPrice,
                ]);

                // Mark item as reserved
                $item->update([
                    'status' => 'reserved',
                    'order_id' => $order->id,
                ]);
            }

            // Recalculate stock
            $variant->recalculateStock();

            DB::commit();

            // TODO: Integrate payment gateway (Pakasir)
            // For now, redirect to order detail
            return redirect()->route('shop.order', $order->order_number)
                ->with('success', 'Pesanan berhasil dibuat! Silakan lakukan pembayaran.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors([
                'checkout' => 'Terjadi kesalahan saat memproses pesanan. Silakan coba lagi.',
            ]);
        }
    }

    // Status pesanan
    public function order($orderNumber)
    {
        $order = Order::where('order_number', $orderNumber)
            ->with(['items', 'items.product', 'items.variant'])
            ->firstOrFail();

        return Inertia::render('Shop/Order', [
            'order' => $order,
        ]);
    }

    // Cari pesanan by email + order number (untuk guest)
    public function trackOrder(Request $request)
    {
        $validated = $request->validate([
            'order_number' => 'required|string',
            'customer_email' => 'required|email',
        ]);

        $order = Order::where('order_number', $validated['order_number'])
            ->where('customer_email', $validated['customer_email'])
            ->with(['items'])
            ->first();

        if (!$order) {
            return back()->withErrors([
                'order_number' => 'Pesanan tidak ditemukan. Periksa nomor pesanan dan email Anda.',
            ]);
        }

        return redirect()->route('shop.order', $order->order_number);
    }
}
