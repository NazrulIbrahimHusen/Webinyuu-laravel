<?php

namespace App\Http\Controllers\Pub;

use App\Enums\BusinessType;
use App\Http\Controllers\Controller;
use App\Models\Store;
use App\Services\CartService;
use App\Services\CheckoutService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CheckoutController extends Controller
{
    public function __construct(private CartService $cart, private CheckoutService $checkout) {}

    public function show(Store $store)
    {
        $items = $this->cart->items($store);
        if ($items->isEmpty()) {
            return redirect()->route('store.cart', $store);
        }

        return view('store.checkout', [
            'store' => $store, 'items' => $items, 'subtotal' => $this->cart->subtotal($store),
            'zones' => $store->shippingZones()->get(),
            'hasPayment' => $store->paymentMethods()->where('is_active', true)->exists(),
            'cartCount' => $this->cart->count($store),
        ]);
    }

    public function place(Request $request, Store $store)
    {
        $items = $this->cart->items($store);
        if ($items->isEmpty()) {
            return redirect()->route('store.cart', $store);
        }
        abort_unless($store->paymentMethods()->where('is_active', true)->exists(), 422, 'Toko belum mengatur metode pembayaran.');

        $type = $store->business_type;
        $zones = $store->shippingZones()->get();

        $fulfillment = match ($type) {
            BusinessType::Fnb => $request->input('fulfillment') === 'delivery' ? 'delivery' : 'pickup',
            BusinessType::Physical => 'delivery',
            BusinessType::Digital => 'digital',
            BusinessType::Service => 'service',
        };

        $rules = [
            'customer_name' => 'required|string|max:100',
            'customer_phone' => ['required', 'regex:/^[0-9+\-\s]{8,20}$/'],
            'customer_email' => in_array($type, [BusinessType::Digital, BusinessType::Service]) ? 'required|email|max:150' : 'nullable|email|max:150',
            'customer_note' => $type === BusinessType::Service ? 'required|string|max:1000' : 'nullable|string|max:1000',
        ];
        if ($fulfillment === 'delivery') {
            $rules['shipping_address'] = 'required|string|max:500';
            if ($zones->isNotEmpty()) {
                $rules['shipping_zone_id'] = ['required', Rule::in($zones->pluck('id')->all())];
            }
        }
        $data = $request->validate($rules, ['customer_phone.regex' => 'Nomor telepon tidak valid.']);

        $zone = $fulfillment === 'delivery' ? $zones->firstWhere('id', (int) ($data['shipping_zone_id'] ?? 0)) : null;
        $order = $this->checkout->place($store, $items, $data, $fulfillment, $zone);
        $this->cart->clear($store);

        return redirect()->route('order.show', $order->access_token)->with('status', 'Pesanan berhasil dibuat. Silakan lakukan pembayaran.');
    }
}
