<?php

namespace App\Http\Controllers\Pub;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\LandingPage;
use App\Services\LandingService;
use App\Models\Store;
use App\Services\CartService;
use App\Services\PlanService;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    public function __construct(private PlanService $plans, private CartService $cart) {}

    /** `/{slug}`: landing page jika paket mendukung dan sudah dipublikasikan, selain itu katalog. */
    public function home(Request $request, Store $store)
    {
        $page = $store->hasFeature('landing_page')
            ? LandingPage::withoutGlobalScopes()->where('store_id', $store->id)->where('status', 'published')->first()
            : null;

        if ($page) {
            return view('store.landing', app(LandingService::class)->data($store, $page) + [
                'store' => $store, 'cartCount' => $this->cart->count($store), 'preview' => false,
            ]);
        }

        return $this->show($request, $store);
    }

    /** `/{slug}/katalog`: selalu katalog. */
    public function show(Request $request, Store $store)
    {
        $q = $this->plans->visibleProductsQuery($store)->with('images');

        $categories = Category::withoutGlobalScopes()->where('store_id', $store->id)->orderBy('sort_order')->orderBy('name')->get();
        if ($slug = $request->query('kategori')) {
            $cat = $categories->firstWhere('slug', $slug);
            $cat ? $q->where('category_id', $cat->id) : $q->whereRaw('1 = 0');
        }
        if ($s = trim((string) $request->query('q'))) {
            $q->where('name', 'like', "%{$s}%");
        }

        return view('store.catalog', [
            'store' => $store,
            'products' => $q->latest()->paginate(12)->withQueryString(),
            'categories' => $categories,
            'cartCount' => $this->cart->count($store),
        ]);
    }

    public function product(Store $store, string $productSlug)
    {
        $product = $this->plans->visibleProductsQuery($store)->with(['images', 'category'])->where('slug', $productSlug)->firstOrFail();

        return view('store.product', ['store' => $store, 'product' => $product, 'cartCount' => $this->cart->count($store)]);
    }
}
