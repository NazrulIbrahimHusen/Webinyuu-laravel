<?php

namespace App\Services;

use App\Enums\ProductStatus;
use App\Models\Plan;
use App\Models\Product;
use App\Models\Store;
use App\Models\Subscription;

class PlanService
{
    /** Langganan yang sedang berlaku (sudah mulai, status active/grace), yang terbaru. */
    public function currentSubscription(int $userId): ?Subscription
    {
        return Subscription::current()
            ->where('user_id', $userId)
            ->where('starts_at', '<=', now())
            ->with('plan')
            ->latest('starts_at')
            ->first();
    }

    /**
     * Paket efektif: langganan berlaku, selain itu Basic.
     * Dihitung saat request, jadi tidak bergantung pada scheduler.
     */
    public function effectivePlan(Store $store): Plan
    {
        return $this->currentSubscription($store->user_id)?->plan ?? Plan::basic();
    }

    public function canAddProduct(Store $store): bool
    {
        $max = $store->effectivePlan()->max_products;

        return $max === null || Product::withoutGlobalScopes()->where('store_id', $store->id)->count() < $max;
    }

    /**
     * Produk yang boleh tampil di sisi publik. Saat paket turun, hanya N produk
     * tertua (tanggal dibuat) yang tampil. Data tidak dihapus.
     */
    public function visibleProductsQuery(Store $store)
    {
        $q = Product::withoutGlobalScopes()
            ->where('store_id', $store->id)
            ->where('status', ProductStatus::Published->value);

        $max = $store->effectivePlan()->max_products;
        if ($max !== null) {
            $ids = (clone $q)->orderBy('created_at')->orderBy('id')->limit($max)->pluck('id');
            $q->whereIn('id', $ids);
        }

        return $q;
    }
}
