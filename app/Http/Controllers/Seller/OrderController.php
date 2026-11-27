<?php

namespace App\Http\Controllers\Seller;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Enums\ShipmentStatus;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use App\Services\OrderStatusService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class OrderController extends Controller
{
    public function __construct(private OrderStatusService $status) {}

    public function index(Request $request)
    {
        $q = Order::latest();
        if ($s = trim((string) $request->query('q'))) {
            $q->where(fn ($w) => $w->where('invoice_number', 'like', "%{$s}%")->orWhere('customer_name', 'like', "%{$s}%"));
        }
        if ($request->filled('order_status')) {
            $q->where('order_status', $request->query('order_status'));
        }
        if ($request->filled('payment_status')) {
            $q->where('payment_status', $request->query('payment_status'));
        }

        return view('seller.orders.index', [
            'orders' => $q->paginate(15)->withQueryString(),
            'orderStatuses' => OrderStatus::cases(),
            'paymentStatuses' => PaymentStatus::cases(),
        ]);
    }

    public function show(string $id)
    {
        return view('seller.orders.show', ['order' => Order::with(['items', 'payments', 'shipment', 'histories'])->findOrFail($id)]);
    }

    public function verifyPayment(Request $request, string $id, string $paymentId)
    {
        $order = Order::findOrFail($id);
        $payment = $order->payments()->findOrFail($paymentId);
        abort_unless($payment->status === PaymentStatus::WaitingVerification, 422);

        $payment->update(['status' => PaymentStatus::Paid, 'verified_by' => $request->user()->id, 'verified_at' => now()]);
        $this->status->payment($order, PaymentStatus::Paid, $request->user(), 'Pembayaran diverifikasi');
        if ($order->order_status === OrderStatus::New) {
            $this->status->order($order, OrderStatus::Processing, $request->user(), 'Otomatis setelah pembayaran terverifikasi');
        }

        return back()->with('status', 'Pembayaran diverifikasi.');
    }

    public function rejectPayment(Request $request, string $id, string $paymentId)
    {
        $order = Order::findOrFail($id);
        $payment = $order->payments()->findOrFail($paymentId);
        abort_unless($payment->status === PaymentStatus::WaitingVerification, 422);
        $data = $request->validate(['reject_reason' => 'required|string|max:500']);

        $payment->update(['status' => PaymentStatus::Rejected, 'reject_reason' => $data['reject_reason'], 'verified_by' => $request->user()->id, 'verified_at' => now()]);
        $this->status->payment($order, PaymentStatus::Rejected, $request->user(), $data['reject_reason']);

        return back()->with('status', 'Pembayaran ditolak, pembeli dapat mengunggah ulang.');
    }

    public function updateStatus(Request $request, string $id)
    {
        $order = Order::findOrFail($id);
        $data = $request->validate(['order_status' => 'required|in:processing,ready_to_ship,cancelled', 'note' => 'nullable|string|max:500']);
        $to = OrderStatus::from($data['order_status']);

        abort_if(in_array($order->order_status, [OrderStatus::Completed, OrderStatus::Cancelled], true), 422, 'Pesanan sudah final.');
        if ($to !== OrderStatus::Cancelled && $order->payment_status !== PaymentStatus::Paid) {
            return back()->withErrors(['order_status' => 'Pembayaran produk harus terverifikasi dulu.']);
        }

        $this->status->order($order, $to, $request->user(), $data['note'] ?? null);

        return back()->with('status', 'Status pesanan diperbarui.');
    }

    public function saveShipment(Request $request, string $id)
    {
        $order = Order::findOrFail($id);
        abort_if($order->isCancelled() || $order->order_status === OrderStatus::Completed, 422);
        if ($order->payment_status !== PaymentStatus::Paid) {
            return back()->withErrors(['courier' => 'Pembayaran produk harus terverifikasi dulu.']);
        }
        $data = $request->validate([
            'courier' => 'required|string|max:60', 'service' => 'nullable|string|max:60',
            'tracking_number' => 'required|string|max:60', 'shipped_at' => 'nullable|date',
        ]);

        $shipment = $order->shipment()->firstOrNew([]);
        $shipment->fill($data + ['shipped_at' => $data['shipped_at'] ?? now()]);
        $isNew = ! $shipment->exists;
        $shipment->status = $shipment->status ?? ShipmentStatus::NotShipped;
        $shipment->save();
        $order->setRelation('shipment', $shipment);

        $this->status->shipping($order, ShipmentStatus::Shipped, $request->user(), 'Resi: '.$data['tracking_number']);
        $this->status->order($order, OrderStatus::Shipped, $request->user(), $isNew ? 'Resi dimasukkan' : 'Resi diperbarui');

        return back()->with('status', 'Informasi pengiriman disimpan.');
    }

    public function proof(string $id, string $paymentId)
    {
        $order = Order::findOrFail($id);
        $payment = $order->payments()->findOrFail($paymentId);
        abort_unless($payment->proof_path && Storage::disk('local')->exists($payment->proof_path), 404);

        return Storage::disk('local')->response($payment->proof_path);
    }
}
