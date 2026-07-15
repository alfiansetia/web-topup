<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ProductItem;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AdminVariantController extends Controller
{
    public function index(Product $product)
    {
        $product->load(['category', 'variants' => function ($q) {
            $q->withCount(['items as available_count' => function ($q) {
                $q->where('status', 'available');
            }])->orderBy('sort_order');
        }]);

        return Inertia::render('Admin/Variants/Index', [
            'product' => $product,
        ]);
    }

    public function create(Product $product)
    {
        return Inertia::render('Admin/Variants/Create', [
            'product' => $product,
        ]);
    }

    public function edit(Product $product, ProductVariant $variant)
    {
        return Inertia::render('Admin/Variants/Edit', [
            'product' => $product,
            'variant' => $variant,
        ]);
    }

    public function store(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'discount_price' => 'nullable|numeric|min:0',
            'is_active' => 'boolean',
            'sort_order' => 'integer|min:0',
        ]);

        $product->variants()->create($validated);

        return back()->with('success', 'Varian berhasil ditambahkan.');
    }

    public function update(Request $request, Product $product, ProductVariant $variant)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'discount_price' => 'nullable|numeric|min:0',
            'is_active' => 'boolean',
            'sort_order' => 'integer|min:0',
        ]);

        $variant->update($validated);

        return back()->with('success', 'Varian berhasil diperbarui.');
    }

    public function destroy(Product $product, ProductVariant $variant)
    {
        // Cek apakah ada order_items yang mereferensikan varian ini
        if ($variant->orderItems()->exists()) {
            return back()->with('error', 'Varian tidak bisa dihapus karena masih digunakan di pesanan.');
        }

        if ($variant->items()->where('status', 'sold')->exists()) {
            return back()->with('error', 'Varian memiliki item yang sudah terjual.');
        }

        try {
            $variant->delete();
        } catch (\Exception $e) {
            return back()->with('error', 'Varian tidak bisa dihapus karena masih digunakan data lain.');
        }

        return back()->with('success', 'Varian berhasil dihapus.');
    }

    // ── Product Items (Stock) ──

    public function items(Product $product, ProductVariant $variant)
    {
        $variant->load('items');

        return Inertia::render('Admin/Variants/Items', [
            'product' => $product,
            'variant' => $variant,
        ]);
    }

    public function storeItem(Request $request, Product $product, ProductVariant $variant)
    {
        $validated = $request->validate([
            'content' => 'required|string|max:2000',
        ]);

        $content = trim($validated['content']);

        // Skip duplikat
        $exists = ProductItem::where('variant_id', $variant->id)
            ->where('content', $content)
            ->exists();

        if ($exists) {
            return back()->with('error', 'Item dengan isi yang sama sudah ada.');
        }

        ProductItem::create([
            'product_id' => $product->id,
            'variant_id' => $variant->id,
            'content' => $content,
            'status' => 'available',
        ]);

        $variant->recalculateStock();

        return back()->with('success', 'Stok berhasil ditambahkan.');
    }

    public function updateItem(Request $request, Product $product, ProductVariant $variant, ProductItem $item)
    {
        $validated = $request->validate([
            'content' => 'required|string|max:2000',
        ]);

        $item->update(['content' => trim($validated['content'])]);

        return back()->with('success', 'Item berhasil diperbarui.');
    }

    public function destroyItem(Product $product, ProductVariant $variant, ProductItem $item)
    {
        if ($item->status === 'sold') {
            return back()->with('error', 'Item sudah terjual, tidak bisa dihapus.');
        }

        $item->delete();
        $variant->recalculateStock();

        return back()->with('success', 'Item berhasil dihapus.');
    }

    public function updateItemStatus(Request $request, Product $product, ProductVariant $variant, ProductItem $item)
    {
        $validated = $request->validate([
            'status' => 'required|in:available,sold,reserved',
        ]);

        $item->update(['status' => $validated['status']]);
        $variant->recalculateStock();

        $labels = [
            'available' => 'Tersedia',
            'sold' => 'Terjual',
            'reserved' => 'Direservasi',
        ];

        return back()->with('success', "Status diubah ke {$labels[$validated['status']]}.");
    }
}
