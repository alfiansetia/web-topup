<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Services\PakasirService;
use App\Services\TelegramService;
use App\Mail\OrderPendingPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;

class ShopController extends Controller
{
    public function __construct(
        protected PakasirService $pakasir,
        protected TelegramService $telegram,
    ) {}

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
        $validated = $request->validate([
            'variant_id' => 'required|exists:product_variants,id',
            'quantity' => 'required|integer|min:1|max:10',
            'notes' => 'nullable|string|max:1000',
        ]);

        $user = Auth::user();

        $customerName = $user->name;
        $customerEmail = $user->email;
        $customerPhone = null;

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
                'user_id' => $user->id,
                'customer_name' => $customerName,
                'customer_email' => $customerEmail,
                'customer_phone' => $customerPhone,
                'total_amount' => $totalAmount,
                'status' => 'pending',
                'notes' => $validated['notes'] ?? null,
            ]);

            // Buat 1 order item per variant (grouped by qty)
            $orderItem = OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $variant->product_id,
                'variant_id' => $variant->id,
                'product_name' => $variant->product->name,
                'variant_name' => $variant->name,
                'price' => $unitPrice,
                'quantity' => $validated['quantity'],
                'subtotal' => $totalAmount,
            ]);

            // Reserve stock & assign ke order item
            foreach ($availableItems as $item) {
                $item->update([
                    'status' => 'reserved',
                    'order_id' => $order->id,
                    'order_item_id' => $orderItem->id,
                ]);
            }

            // Recalculate stock
            $variant->recalculateStock();

            // Create QRIS payment via Pakasir (inside transaction!)
            $payment = $this->pakasir->createQrisTransaction($order->order_number, (int) $totalAmount);

            if (!$payment) {
                throw new \Exception('Gagal membuat pembayaran QRIS. Toko belum ready menerima pembayarn.');
            }

            $order->update([
                'payment_ref'               => $payment['payment_number'] ?? null,
                'payment_method'             => $payment['payment_method'] ?? 'qris',
                'payment_url'                => $this->pakasir->getPaymentUrl($order->order_number, (int) $totalAmount),
                'payment_channel'            => 'qris',
                'payment_fee'                => $payment['fee'] ?? 0,
                'payment_gateway_status'     => 'pending',
                'payment_gateway_response'   => $payment,
            ]);

            DB::commit();

            // Telegram notification (non-blocking, after commit)
            $this->telegram->notifyNewOrder($order);

            // Email notification (queued)
            Mail::to($order->customer_email)->queue(new OrderPendingPayment($order));

            return redirect()->route('shop.order', $order->order_number)
                ->with('success', 'Pesanan berhasil dibuat! Silakan lakukan pembayaran.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Checkout failed', [
                'error' => $e->getMessage(),
            ]);
            return back()->withErrors([
                'checkout' => $e->getMessage(),
            ]);
        }
    }

    // Status pesanan
    public function order($orderNumber)
    {
        $order = Order::where('order_number', $orderNumber)
            ->with(['items', 'items.product', 'items.variant', 'items.assignedItems'])
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
        ]);

        $order = Order::where('order_number', $validated['order_number'])
            ->with(['items'])
            ->first();

        if (!$order) {
            return back()->withErrors([
                'order_number' => 'Pesanan tidak ditemukan. Periksa nomor pesanan Anda.',
            ]);
        }

        return redirect()->route('shop.order', $order->order_number);
    }
}
