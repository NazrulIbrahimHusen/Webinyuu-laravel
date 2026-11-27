<?php

namespace App\Http\Controllers\Pub;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Enums\ShipmentStatus;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\OrderStatusService;
use Illuminate\Http\Request;

class OrderTrackController extends Controller
{
    public function __construct(private OrderStatusService $status) {}

    private function find(string $token): Order
    {
        return Order::withoutGlobalScopes()->with(['items', 'payments', 'shipment', 'histories', 'store'])
            ->where('access_token', $token)->firstOrFail();
    }

    public function show(string $token)
    {
        $order = $this->find($token);

        return view('store.order', [
            'order' => $order, 'store' => $order->store,
            'methods' => $order->store->paymentMethods()->where('is_active', true)->get(),
        ]);
    }

    public function uploadProof(Request $request, string $token)
    {
        $order = $this->find($token);
        abort_if($order->isCancelled(), 422);
        abort_unless(in_array($order->payment_status, [PaymentStatus::WaitingPayment, PaymentStatus::Rejected], true), 422);

        $d = $request->validate([
            'payment_method_id' => 'required|integer',
            'proof' => 'required|file|mimes:jpg,jpeg,png,webp,pdf|max:4096',
        ]);
        $method = $order->store->paymentMethods()->where('is_active', true)->findOrFail($d['payment_method_id']);

        $order->payments()->create([
            'payment_method_id' => $method->id, 'method_label' => $method->label,
            'amount' => $order->transfer_amount,
            'proof_path' => $request->file('proof')->store('payment-proofs', 'local'),
            'status' => PaymentStatus::WaitingVerification,
        ]);
        $this->status->payment($order, PaymentStatus::WaitingVerification, null, 'Bukti pembayaran diunggah');

        return back()->with('status', 'Bukti pembayaran terkirim. Menunggu verifikasi penjual.');
    }

    public function received(string $token)
    {
        $order = $this->find($token);
        abort_unless($order->order_status === OrderStatus::Shipped, 422);

        $this->status->shipping($order, ShipmentStatus::Received, null, 'Dikonfirmasi pembeli');
        $this->status->order($order, OrderStatus::Completed, null, 'Pesanan diterima pembeli');

        return back()->with('status', 'Terima kasih! Pesanan ditandai selesai.');
    }
}
