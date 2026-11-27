<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index()
    {
        return view('seller.categories.index', ['categories' => Category::withCount('products')->orderBy('sort_order')->orderBy('name')->get()]);
    }

    public function store(Request $request)
    {
        $store = $request->attributes->get('store');
        $data = $request->validate(['name' => 'required|string|max:80', 'sort_order' => 'nullable|integer|min:0']);

        Category::create([
            'name' => $data['name'],
            'slug' => $this->uniqueSlug($store->id, $data['name']),
            'sort_order' => $data['sort_order'] ?? 0,
        ]);

        return back()->with('status', 'Kategori ditambahkan.');
    }

    public function update(Request $request, string $id)
    {
        $category = Category::findOrFail($id);
        $data = $request->validate(['name' => 'required|string|max:80', 'sort_order' => 'nullable|integer|min:0']);
        $category->update(['name' => $data['name'], 'sort_order' => $data['sort_order'] ?? 0]);

        return back()->with('status', 'Kategori diperbarui.');
    }

    public function destroy(string $id)
    {
        $category = Category::findOrFail($id);
        $category->products()->update(['category_id' => null]);
        $category->delete();

        return back()->with('status', 'Kategori dihapus.');
    }

    private function uniqueSlug(int $storeId, string $name): string
    {
        $base = Str::slug($name) ?: 'kategori';
        $slug = $base;
        $i = 2;
        while (Category::withoutGlobalScopes()->where('store_id', $storeId)->where('slug', $slug)->exists()) {
            $slug = $base.'-'.$i++;
        }

        return $slug;
    }
}
