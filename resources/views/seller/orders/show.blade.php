@extends('layouts.seller')
@section('content')
<a href="{{ route('seller.orders.index') }}" class="text-sm text-gray-500">&larr; Semua pesanan</a>
<div class="flex flex-wrap items-center gap-2 my-2">
    <h1 class="text-xl font-semibold">{{ $order->invoice_number }}</h1>
    <span class="text-xs rounded bg-gray-200 px-2 py-1">{{ $order->order_status->label() }}</span>
    <span class="text-xs rounded bg-amber-100 px-2 py-1">{{ $order->payment_status->label() }}</span>
    <a class="ml-auto text-green-700 text-sm" target="_blank" href="{{ \App\Support\Phone::wa($order->customer_phone, 'Halo '.$order->customer_name.', terkait pesanan '.$order->invoice_number) }}">Chat pembeli</a>
</div>
<div class="grid md:grid-cols-3 gap-4">
<div class="md:col-span-2 space-y-4">
    <div class="bg-white rounded p-4 text-sm">
        <div class="font-semibold mb-2">Pembeli</div>
        {{ $order->customer_name }} · {{ $order->customer_phone }} @if($order->customer_email) · {{ $order->customer_email }} @endif
        <div class="text-gray-500">{{ ucfirst($order->fulfillment) }} @if($order->shipping_area)· {{ $order->shipping_area }}@endif</div>
        @if($order->shipping_address)<div class="mt-1">{{ $order->shipping_address }}</div>@endif
        @if($order->customer_note)<div class="mt-1 italic">Catatan: {{ $order->customer_note }}</div>@endif
    </div>
    <div class="bg-white rounded p-4 text-sm">
        <div class="font-semibold mb-2">Item</div>
        @foreach ($order->items as $i)
            <div class="flex justify-between py-1"><span>{{ $i->product_name }} × {{ $i->qty }}</span><span><x-rp :v="$i->line_total" /></span></div>
        @endforeach
        <div class="border-t mt-2 pt-2 flex justify-between"><span>Subtotal produk</span><x-rp :v="$order->subtotal" /></div>
        @if($order->discount_total)<div class="flex justify-between"><span>Diskon</span>-<x-rp :v="$order->discount_total" /></div>@endif
        <div class="flex justify-between font-semibold"><span>Ditransfer ke Anda</span><x-rp :v="$order->transfer_amount" /></div>
        <div class="flex justify-between text-gray-500"><span>Ongkir (COD ke kurir)</span><x-rp :v="$order->shipping_cost" /></div>
    </div>
    <div class="bg-white rounded p-4 text-sm">
        <div class="font-semibold mb-2">Bukti pembayaran</div>
        @forelse ($order->payments as $p)
            <div class="border rounded p-3 mb-2">
                <div class="flex justify-between"><span>{{ $p->method_label }} · <x-rp :v="$p->amount" /></span><span class="text-xs">{{ $p->status->label() }}</span></div>
                <a class="text-indigo-600" target="_blank" href="{{ route('seller.orders.proof', [$order->id, $p->id]) }}">Lihat bukti</a>
                @if($p->reject_reason)<div class="text-red-600 text-xs">Ditolak: {{ $p->reject_reason }}</div>@endif
                @if ($p->status === \App\Enums\PaymentStatus::WaitingVerification)
                    <div class="flex gap-2 mt-2 items-start">
                        <form method="POST" action="{{ route('seller.orders.verify', [$order->id, $p->id]) }}">@csrf<button class="bg-green-600 text-white rounded px-3 py-1">Verifikasi</button></form>
                        <form method="POST" action="{{ route('seller.orders.reject', [$order->id, $p->id]) }}" class="flex gap-1">@csrf
                            <input name="reject_reason" required placeholder="Alasan penolakan" class="border rounded px-2 py-1"><button class="bg-red-600 text-white rounded px-3 py-1">Tolak</button></form>
                    </div>
                @endif
            </div>
        @empty
            <p class="text-gray-500">Belum ada bukti pembayaran.</p>
        @endforelse
    </div>
</div>
<div class="space-y-4">
    @unless (in_array($order->order_status, [\App\Enums\OrderStatus::Completed, \App\Enums\OrderStatus::Cancelled]))
    <form method="POST" action="{{ route('seller.orders.status', $order->id) }}" class="bg-white rounded p-4 text-sm space-y-2">
        @csrf <div class="font-semibold">Ubah status</div>
        <select name="order_status" class="w-full border rounded px-2 py-1.5">
            <option value="processing">Diproses</option><option value="ready_to_ship">Siap dikirim</option><option value="cancelled">Batalkan pesanan</option></select>
        <input name="note" placeholder="Catatan (opsional)" class="w-full border rounded px-2 py-1.5">
        <button class="w-full bg-indigo-600 text-white rounded py-1.5">Simpan</button>
    </form>
    <form method="POST" action="{{ route('seller.orders.shipment', $order->id) }}" class="bg-white rounded p-4 text-sm space-y-2">
        @csrf <div class="font-semibold">Pengiriman / resi</div>
        <input name="courier" value="{{ old('courier', $order->shipment?->courier) }}" required placeholder="Kurir (JNE, J&T, ...)" class="w-full border rounded px-2 py-1.5">
        <input name="service" value="{{ old('service', $order->shipment?->service) }}" placeholder="Layanan (REG, YES, ...)" class="w-full border rounded px-2 py-1.5">
        <input name="tracking_number" value="{{ old('tracking_number', $order->shipment?->tracking_number) }}" required placeholder="Nomor resi" class="w-full border rounded px-2 py-1.5">
        <button class="w-full bg-indigo-600 text-white rounded py-1.5">Simpan & tandai dikirim</button>
    </form>
    @endunless
    <div class="bg-white rounded p-4 text-xs">
        <div class="font-semibold text-sm mb-2">Riwayat</div>
        @foreach ($order->histories as $h)
            <div class="py-1 border-b last:border-0">{{ $h->created_at->format('d M H:i') }} · {{ $h->type }}: {{ $h->from_status ?? '-' }} → {{ $h->to_status }} @if($h->note)<div class="text-gray-500">{{ $h->note }}</div>@endif</div>
        @endforeach
    </div>
</div>
</div>
@endsection
