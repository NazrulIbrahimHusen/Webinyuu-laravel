<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Models\Order;
use App\Models\Product;
use App\Models\ShippingZone;
use App\Models\Store;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CheckoutService
{
    /**
     * Buat order dari isi keranjang. Harga dan nama disalin (snapshot) ke order_items.
     *
     * @param  Collection  $items  hasil CartService::items()
     * @param  array  $data  customer_name, customer_phone, customer_email, customer_note, shipping_address
     */
    public function place(Store $store, Collection $items, array $data, string $fulfillment, ?ShippingZone $zone): Order
    {
        return DB::transaction(function () use ($store, $items, $data, $fulfillment, $zone) {
            $products = Product::withoutGlobalScopes()
                ->whereIn('id', $items->pluck('product.id'))->lockForUpdate()->get()->keyBy('id');

            $subtotal = 0;
            $lines = [];
            foreach ($items as $row) {
                $p = $products[$row['product']->id];
                $qty = $row['qty'];
                if ($p->track_stock && $p->stock < $qty) {
                    throw ValidationException::withMessages(['cart' => "Stok {$p->name} tidak mencukupi."]);
                }
                $lines[] = [$p, $qty];
                $subtotal += $p->price * $qty;
            }

            $shipping = $fulfillment === 'delivery' ? (int) ($zone?->fee ?? 0) : 0;

            $order = Order::create([
                'store_id' => $store->id,
                'invoice_number' => $this->invoiceNumber(),
                'access_token' => Str::random(40),
                'customer_name' => $data['customer_name'],
                'customer_phone' => $data['customer_phone'],
                'customer_email' => $data['customer_email'] ?? null,
                'fulfillment' => $fulfillment,
                'shipping_address' => $data['shipping_address'] ?? null,
                'shipping_area' => $zone?->name,
                'customer_note' => $data['customer_note'] ?? null,
                'subtotal' => $subtotal,
                'discount_total' => 0,
                'shipping_cost' => $shipping,
                'total' => $subtotal + $shipping,
                'order_status' => OrderStatus::New,
                'payment_status' => PaymentStatus::WaitingPayment,
            ]);

            foreach ($lines as [$p, $qty]) {
                $order->items()->create([
                    'product_id' => $p->id, 'product_name' => $p->name, 'unit_price' => $p->price,
                    'qty' => $qty, 'line_total' => $p->price * $qty, 'weight_gram' => $p->weight_gram * $qty,
                ]);
                if ($p->track_stock) {
                    $p->decrement('stock', $qty);
                }
            }

            $order->histories()->create(['type' => 'order', 'from_status' => null, 'to_status' => OrderStatus::New->value, 'note' => 'Pesanan dibuat']);

            return $order;
        });
    }

    private function invoiceNumber(): string
    {
        do {
            $n = 'INV-'.now()->format('ymd').'-'.strtoupper(Str::random(5));
        } while (Order::withoutGlobalScopes()->where('invoice_number', $n)->exists());

        return $n;
    }
}
