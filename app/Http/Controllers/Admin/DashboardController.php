<?php

namespace App\Http\Controllers\Admin;

use App\Enums\SubscriptionPaymentStatus;
use App\Enums\SubscriptionStatus;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\Store;
use App\Models\Subscription;
use App\Models\SubscriptionPayment;
use App\Models\User;

class DashboardController extends Controller
{
    public function __invoke()
    {
        $active = Subscription::current()->where('starts_at', '<=', now())->with('plan')->get()->unique('user_id');

        return view('admin.dashboard', [
            'sellers' => User::where('role', UserRole::Seller->value)->count(),
            'stores' => Store::count(),
            'toVerify' => SubscriptionPayment::where('status', SubscriptionPaymentStatus::WaitingVerification->value)->count(),
            'revenueMonth' => (int) SubscriptionPayment::where('status', SubscriptionPaymentStatus::Paid->value)
                ->where('verified_at', '>=', now()->startOfMonth())->sum('total_amount'),
            'byPlan' => $active->groupBy(fn ($s) => $s->plan->name)->map->count(),
            'graceCount' => $active->where('status', SubscriptionStatus::Grace)->count(),
            'expiring' => Subscription::with(['user.store', 'plan'])->where('status', SubscriptionStatus::Active->value)
                ->whereBetween('ends_at', [now(), now()->addDays(7)])->orderBy('ends_at')->limit(10)->get(),
        ]);
    }
}
