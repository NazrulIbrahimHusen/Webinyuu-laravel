<?php

namespace App\Http\Controllers\Pub;

use App\Http\Controllers\Controller;
use App\Models\Store;
use App\Services\CartService;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function __construct(private CartService $cart) {}

    public function show(Store $store)
    {
        return view('store.cart', [
            'store' => $store, 'items' => $this->cart->items($store),
            'subtotal' => $this->cart->subtotal($store), 'cartCount' => $this->cart->count($store),
        ]);
    }

    public function add(Request $request, Store $store)
    {
        $d = $request->validate(['product_id' => 'required|integer', 'qty' => 'nullable|integer|min:1|max:99']);
        $this->cart->add($store, $d['product_id'], $d['qty'] ?? 1);

        return redirect()->route('store.cart', $store)->with('status', 'Produk ditambahkan ke keranjang.');
    }

    public function update(Request $request, Store $store)
    {
        $d = $request->validate(['product_id' => 'required|integer', 'qty' => 'required|integer|min:0|max:99']);
        $this->cart->set($store, $d['product_id'], $d['qty']);

        return back();
    }

    public function remove(Store $store, int $product)
    {
        $this->cart->remove($store, $product);

        return back();
    }
}
