<?php

namespace App\Http\Middleware;

use App\Models\Store;
use Closure;
use Illuminate\Http\Request;

/** Pemakaian: ->middleware('feature:checkout'). Store dari route {store} atau seller yang login. */
class EnsureFeature
{
    public function handle(Request $request, Closure $next, string $feature)
    {
        $store = $request->route('store');
        if (is_string($store)) {
            $store = Store::where('slug', $store)->first();
        }
        $store ??= $request->attributes->get('store');

        abort_unless($store, 404);
        abort_unless($store->hasFeature($feature), 403, 'Fitur ini tidak tersedia di paket toko ini.');

        return $next($request);
    }
}
