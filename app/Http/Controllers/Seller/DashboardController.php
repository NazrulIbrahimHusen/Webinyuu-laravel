<?php

namespace App\Http\Controllers\Seller;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        $store = $request->attributes->get('store');
        $plan = $store->effectivePlan();

        return view('seller.dashboard', [
            'store' => $store,
            'plan' => $plan,
            'productCount' => Product::count(),
            'newOrders' => Order::where('order_status', OrderStatus::New->value)->count(),
            'toVerify' => Order::where('payment_status', PaymentStatus::WaitingVerification->value)->count(),
            'hasOrders' => $store->hasFeature('orders'),
        ]);
    }
}
