<?php

namespace App\Http\Controllers\Seller;

use App\Enums\ProductStatus;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Services\PlanService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $store = $request->attributes->get('store');
        $q = Product::with(['category', 'images'])->latest();
        if ($s = $request->query('q')) {
            $q->where('name', 'like', "%{$s}%");
        }

        return view('seller.products.index', [
            'products' => $q->paginate(15)->withQueryString(),
            'plan' => $store->effectivePlan(),
            'canAdd' => app(PlanService::class)->canAddProduct($store),
            'total' => Product::count(),
        ]);
    }

    public function create(Request $request)
    {
        if (! app(PlanService::class)->canAddProduct($request->attributes->get('store'))) {
            return redirect()->route('seller.products.index')->withErrors(['limit' => 'Batas produk paket Anda sudah tercapai.']);
        }

        return view('seller.products.form', ['product' => null, 'categories' => Category::orderBy('name')->get()]);
    }

    public function store(Request $request)
    {
        $store = $request->attributes->get('store');
        if (! app(PlanService::class)->canAddProduct($store)) {
            return back()->withErrors(['limit' => 'Batas produk paket Anda sudah tercapai.']);
        }

        $data = $this->validated($request, $store->id);
        $product = Product::create($data + ['slug' => $this->uniqueSlug($store->id, $data['name'])]);
        $this->saveImages($request, $product);

        return redirect()->route('seller.products.index')->with('status', 'Produk ditambahkan.');
    }

    public function edit(string $id)
    {
        return view('seller.products.form', [
            'product' => Product::with('images')->findOrFail($id),
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, string $id)
    {
        $store = $request->attributes->get('store');
        $product = Product::findOrFail($id);
        $product->update($this->validated($request, $store->id));

        foreach ($product->images()->whereIn('id', (array) $request->input('remove_images', []))->get() as $img) {
            Storage::disk('public')->delete($img->path);
            $img->delete();
        }
        $this->saveImages($request, $product);

        return redirect()->route('seller.products.index')->with('status', 'Produk diperbarui.');
    }

    public function destroy(string $id)
    {
        Product::findOrFail($id)->delete(); // soft delete, order lama aman karena snapshot

        return back()->with('status', 'Produk dihapus.');
    }

    private function validated(Request $request, int $storeId): array
    {
        $data = $request->validate([
            'name' => 'required|string|max:150',
            'category_id' => ['nullable', Rule::exists('categories', 'id')->where('store_id', $storeId)],
            'description' => 'nullable|string|max:5000',
            'price' => 'required|integer|min:0|max:1000000000',
            'weight_gram' => 'nullable|integer|min:0|max:1000000',
            'status' => ['required', Rule::enum(ProductStatus::class)],
            'track_stock' => 'nullable|boolean',
            'stock' => 'nullable|integer|min:0|required_if:track_stock,1',
            'images' => 'nullable|array|max:5',
            'images.*' => 'image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);
        unset($data['images']);
        $data['weight_gram'] = $data['weight_gram'] ?? 0;
        $data['track_stock'] = $request->boolean('track_stock');
        $data['stock'] = $data['track_stock'] ? ($data['stock'] ?? 0) : null;

        return $data;
    }

    private function saveImages(Request $request, Product $product): void
    {
        $hasPrimary = $product->images()->where('is_primary', true)->exists();
        $order = (int) $product->images()->max('sort_order');
        foreach ((array) $request->file('images', []) as $file) {
            ProductImage::create([
                'product_id' => $product->id,
                'path' => $file->store('products', 'public'),
                'sort_order' => ++$order,
                'is_primary' => ! $hasPrimary,
            ]);
            $hasPrimary = true;
        }
        if (! $product->images()->where('is_primary', true)->exists() && ($first = $product->images()->first())) {
            $first->update(['is_primary' => true]);
        }
    }

    private function uniqueSlug(int $storeId, string $name): string
    {
        $base = Str::slug($name) ?: 'produk';
        $slug = $base;
        $i = 2;
        while (Product::withoutGlobalScopes()->withTrashed()->where('store_id', $storeId)->where('slug', $slug)->exists()) {
            $slug = $base.'-'.$i++;
        }

        return $slug;
    }
}
