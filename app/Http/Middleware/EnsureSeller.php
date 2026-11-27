<?php

namespace App\Http\Middleware;

use App\Scopes\StoreScope;
use Closure;
use Illuminate\Http\Request;

class EnsureSeller
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();
        abort_unless($user, 403);

        // Admin yang login diarahkan ke panel admin.
        if ($user->role->value === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        // Seller baru belum punya toko: arahkan ke onboarding.
        $store = $user->store;
        if (! $store && ! $request->routeIs('seller.onboarding*')) {
            return redirect()->route('seller.onboarding');
        }

        StoreScope::$storeId = $store?->id;
        $request->attributes->set('store', $store);

        return $next($request);
    }
}
