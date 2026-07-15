<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;

class AdminProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with('category')
            ->withCount('variants');

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $products = $query->latest()->paginate(15)->withQueryString();

        return Inertia::render('Admin/Products/Index', [
            'products' => $products,
            'categories' => Category::orderBy('name')->get(),
            'filters' => $request->only('search', 'category_id'),
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Products/Create', [
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function edit(Product $product)
    {
        return Inertia::render('Admin/Products/Edit', [
            'product' => $product,
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function show(Product $product)
    {
        $product->load(['category', 'variants' => function ($q) {
            $q->withCount('items')->orderBy('sort_order');
        }]);

        return Inertia::render('Admin/Products/Show', [
            'product' => $product,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'instruction_use' => 'nullable|string',
            'checkout_instruction' => 'nullable|string|max:255',
            'features' => 'nullable|array',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp,svg|max:2048',
            'is_active' => 'boolean',
            'sort_order' => 'integer|min:0',
        ]);

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
            $file->move(storage_path('app/public/product'), $filename);
            $validated['image'] = $filename;
        }

        Product::create($validated);

        return redirect()->route('admin.products.index')
            ->with('success', 'Produk berhasil ditambahkan.');
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'instruction_use' => 'nullable|string',
            'checkout_instruction' => 'nullable|string|max:255',
            'features' => 'nullable|array',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp,svg|max:2048',
            'remove_image' => 'boolean',
            'is_active' => 'boolean',
            'sort_order' => 'integer|min:0',
        ]);

        if ($request->boolean('remove_image') && $product->image) {
            Storage::disk('public')->delete('product/' . $product->image);
            $validated['image'] = null;
        } elseif ($request->hasFile('image')) {
            if ($product->image) {
                Storage::disk('public')->delete('product/' . $product->image);
            }
            $file = $request->file('image');
            $filename = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
            $file->move(storage_path('app/public/product'), $filename);
            $validated['image'] = $filename;
        } else {
            unset($validated['image']);
        }

        unset($validated['remove_image']);

        $product->update($validated);

        return redirect()->route('admin.products.index')
            ->with('success', 'Produk berhasil diperbarui.');
    }

    public function destroy(Product $product)
    {
        // Cek apakah ada order_items yang mereferensikan produk ini
        if ($product->orderItems()->exists()) {
            return back()->with('error', 'Produk tidak bisa dihapus karena masih digunakan di pesanan.');
        }

        if ($product->variants()->whereHas('items', function ($q) {
            $q->where('status', 'sold');
        })->exists()) {
            return back()->with('error', 'Produk memiliki item yang sudah terjual.');
        }

        if ($product->image) {
            Storage::disk('public')->delete('product/' . $product->image);
        }

        try {
            $product->delete();
        } catch (\Exception $e) {
            return back()->with('error', 'Produk tidak bisa dihapus karena masih digunakan data lain (varian/item/pesanan).');
        }

        return redirect()->route('admin.products.index')
            ->with('success', 'Produk berhasil dihapus.');
    }
}
