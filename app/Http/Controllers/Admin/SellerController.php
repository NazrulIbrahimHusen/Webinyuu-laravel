<?php

namespace App\Http\Controllers\Admin;

use App\Enums\SubscriptionPeriod;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\Store;
use App\Models\User;
use App\Services\PlanService;
use App\Services\SubscriptionService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SellerController extends Controller
{
    public function index(Request $request, PlanService $plans)
    {
        $q = User::with('store')->where('role', UserRole::Seller->value)->latest();
        if ($s = trim((string) $request->query('q'))) {
            $q->where(fn ($w) => $w->where('name', 'like', "%{$s}%")->orWhere('email', 'like', "%{$s}%")
                ->orWhereHas('store', fn ($t) => $t->where('name', 'like', "%{$s}%")->orWhere('slug', 'like', "%{$s}%")));
        }
        $users = $q->paginate(20)->withQueryString();
        $subs = $users->getCollection()->mapWithKeys(fn ($u) => [$u->id => $plans->currentSubscription($u->id)]);

        return view('admin.sellers.index', [
            'users' => $users, 'subs' => $subs,
            'plans' => Plan::where('price_monthly', '>', 0)->orderBy('price_monthly')->get(),
        ]);
    }

    /** Beri langganan tanpa pembayaran (promo, kompensasi, atau uji coba). */
    public function grant(Request $request, string $id, SubscriptionService $subs)
    {
        $user = User::where('role', UserRole::Seller->value)->findOrFail($id);
        $d = $request->validate(['plan_id' => 'required|exists:plans,id', 'period' => ['required', Rule::enum(SubscriptionPeriod::class)]]);
        $subs->activate($user, Plan::findOrFail($d['plan_id']), SubscriptionPeriod::from($d['period']), $request->user());

        return back()->with('status', "Langganan diberikan ke {$user->name}.");
    }

    public function toggleStore(string $id)
    {
        $store = Store::where('user_id', $id)->firstOrFail();
        $store->update(['is_active' => ! $store->is_active]);

        return back()->with('status', $store->is_active ? 'Katalog diaktifkan.' : 'Katalog dinonaktifkan.');
    }
}
