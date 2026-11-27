<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Store;
use Illuminate\Support\Collection;

/** Keranjang berbasis session, satu keranjang per toko. */
class CartService
{
    public function __construct(private PlanService $plans) {}

    private function key(Store $s): string { return 'cart.'.$s->id; }

    private function raw(Store $s): array { return session($this->key($s), []); }

    private function save(Store $s, array $cart): void { session([$this->key($s) => $cart]); }

    private function product(Store $s, int $id): ?Product
    {
        return $this->plans->visibleProductsQuery($s)->with('images')->find($id);
    }

    public function add(Store $s, int $productId, int $qty = 1): void
    {
        $this->set($s, $productId, ($this->raw($s)[$productId] ?? 0) + max(1, $qty));
    }

    public function set(Store $s, int $productId, int $qty): void
    {
        $cart = $this->raw($s);
        $p = $this->product($s, $productId);

        if (! $p || $qty <= 0) {
            unset($cart[$productId]);
        } else {
            $max = $p->track_stock ? max(0, (int) $p->stock) : 99;
            $qty = min($qty, $max);
            $qty > 0 ? $cart[$productId] = $qty : $cart = array_diff_key($cart, [$productId => 1]);
        }
        $this->save($s, $cart);
    }

    public function remove(Store $s, int $productId): void { $this->set($s, $productId, 0); }

    public function clear(Store $s): void { session()->forget($this->key($s)); }

    public function count(Store $s): int { return array_sum($this->raw($s)); }

    /** @return Collection<int, array{product: Product, qty: int, line: int}> */
    public function items(Store $s): Collection
    {
        $cart = $this->raw($s);
        if (! $cart) {
            return collect();
        }
        $products = $this->plans->visibleProductsQuery($s)->with('images')->whereIn('id', array_keys($cart))->get()->keyBy('id');

        return collect($cart)->map(function ($qty, $id) use ($products) {
            $p = $products->get($id);

            return $p ? ['product' => $p, 'qty' => $qty, 'line' => $p->price * $qty] : null;
        })->filter()->values();
    }

    public function subtotal(Store $s): int { return (int) $this->items($s)->sum('line'); }
}
