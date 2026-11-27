<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Enums\ShipmentStatus;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/** Semua perubahan status wajib lewat sini supaya riwayat selalu tercatat. */
class OrderStatusService
{
    public function order(Order $o, OrderStatus $to, ?User $by = null, ?string $note = null): void
    {
        if ($o->order_status === $to) {
            return;
        }
        DB::transaction(function () use ($o, $to, $by, $note) {
            $from = $o->order_status;
            $o->order_status = $to;
            if ($to === OrderStatus::Completed) {
                $o->completed_at = now();
            }
            $o->save();
            $this->log($o, 'order', $from->value, $to->value, $by, $note);

            if ($to === OrderStatus::Cancelled) {
                $this->restock($o);
                if (! in_array($o->payment_status, [PaymentStatus::Paid], true)) {
                    $this->payment($o, PaymentStatus::Cancelled, $by, 'Pesanan dibatalkan');
                }
            }
        });
    }

    public function payment(Order $o, PaymentStatus $to, ?User $by = null, ?string $note = null): void
    {
        if ($o->payment_status === $to) {
            return;
        }
        $from = $o->payment_status;
        $o->payment_status = $to;
        $o->save();
        $this->log($o, 'payment', $from->value, $to->value, $by, $note);
    }

    public function shipping(Order $o, ShipmentStatus $to, ?User $by = null, ?string $note = null): void
    {
        $shipment = $o->shipment;
        if (! $shipment || $shipment->status === $to) {
            return;
        }
        $from = $shipment->status;
        $shipment->update(['status' => $to]);
        $this->log($o, 'shipping', $from->value, $to->value, $by, $note);
    }

    private function restock(Order $o): void
    {
        foreach ($o->items()->whereNotNull('product_id')->get() as $item) {
            $p = Product::withoutGlobalScopes()->withTrashed()->find($item->product_id);
            if ($p && $p->track_stock) {
                $p->increment('stock', $item->qty);
            }
        }
    }

    private function log(Order $o, string $type, ?string $from, string $to, ?User $by, ?string $note): void
    {
        $o->histories()->create([
            'type' => $type, 'from_status' => $from, 'to_status' => $to,
            'changed_by' => $by?->id, 'note' => $note,
        ]);
    }
}
