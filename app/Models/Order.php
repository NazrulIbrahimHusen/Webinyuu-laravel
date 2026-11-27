<?php

namespace App\Models;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Models\Concerns\BelongsToStore;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use BelongsToStore;

    protected $guarded = [];

    protected $casts = [
        'order_status' => OrderStatus::class,
        'payment_status' => PaymentStatus::class,
        'completed_at' => 'datetime',
    ];

    public function items() { return $this->hasMany(OrderItem::class); }
    public function payments() { return $this->hasMany(Payment::class)->latest(); }
    public function shipment() { return $this->hasOne(Shipment::class); }
    public function histories() { return $this->hasMany(OrderStatusHistory::class)->latest(); }

    /** Nominal yang ditransfer ke seller (ongkir dibayar COD ke kurir). */
    public function getTransferAmountAttribute(): int
    {
        return $this->subtotal - $this->discount_total;
    }

    public function isCancelled(): bool { return $this->order_status === OrderStatus::Cancelled; }
}
