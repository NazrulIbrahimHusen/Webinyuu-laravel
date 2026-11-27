<?php

namespace App\Http\Middleware;

use App\Models\Store;
use Closure;
use Illuminate\Http\Request;

class EnsureStoreActive
{
    public function handle(Request $request, Closure $next)
    {
        $store = $request->route('store');
        if (is_string($store)) {
            $store = Store::where('slug', $store)->first();
        }
        abort_unless($store && $store->is_active, 404);

        return $next($request);
    }
}
