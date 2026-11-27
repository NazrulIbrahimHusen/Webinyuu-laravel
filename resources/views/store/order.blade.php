@extends('layouts.public')
@section('title', 'Pesanan '.$order->invoice_number)
@section('content')
@php $canPay = in_array($order->payment_status, [\App\Enums\PaymentStatus::WaitingPayment, \App\Enums\PaymentStatus::Rejected]) && ! $order->isCancelled(); @endphp
<div class="flex flex-wrap gap-2 items-center mb-3">
    <h1 class="text-xl font-semibold">{{ $order->invoice_number }}</h1>
    <span class="text-xs rounded bg-gray-200 px-2 py-1">{{ $order->order_status->label() }}</span>
    <span class="text-xs rounded bg-amber-100 px-2 py-1">{{ $order->payment_status->label() }}</span>
</div>
<p class="text-xs text-gray-500 mb-4">Simpan halaman ini untuk memantau pesanan Anda.</p>
<div class="grid md:grid-cols-2 gap-4">
<div class="space-y-4">
    <div class="bg-white rounded p-4 text-sm">
        @foreach ($order->items as $i)<div class="flex justify-between py-0.5"><span>{{ $i->product_name }} × {{ $i->qty }}</span><x-rp :v="$i->line_total" /></div>@endforeach
        <div class="border-t mt-2 pt-2 flex justify-between font-semibold"><span>Transfer ke penjual</span><x-rp :v="$order->transfer_amount" /></div>
        @if($order->shipping_cost)<div class="flex justify-between text-gray-500"><span>Ongkir (bayar ke kurir saat barang tiba)</span><x-rp :v="$order->shipping_cost" /></div>@endif
    </div>
    @if ($order->shipment && $order->shipment->tracking_number)
        <div class="bg-white rounded p-4 text-sm" x-data="{ ok: false }">
            <div class="font-semibold mb-1">Pengiriman</div>
            {{ $order->shipment->courier }} {{ $order->shipment->service }} · Resi <b>{{ $order->shipment->tracking_number }}</b>
            <button type="button" class="ml-2 text-indigo-600" @click="navigator.clipboard.writeText('{{ $order->shipment->tracking_number }}'); ok = true"><span x-text="ok ? 'Tersalin' : 'Salin'"></span></button>
            <div><a target="_blank" class="text-indigo-600" href="https://www.google.com/search?q={{ urlencode($order->shipment->courier.' resi '.$order->shipment->tracking_number) }}">Lacak paket</a></div>
        </div>
    @endif
    @if ($order->order_status === \App\Enums\OrderStatus::Shipped)
        <form method="POST" action="{{ route('order.received', $order->access_token) }}" onsubmit="return confirm('Konfirmasi pesanan sudah diterima?')">@csrf
            <button class="w-full bg-green-600 text-white rounded py-2">Pesanan Diterima</button></form>
    @endif
    <div class="bg-white rounded p-4 text-xs">
        <div class="font-semibold text-sm mb-1">Riwayat</div>
        @foreach ($order->histories as $h)<div class="py-0.5">{{ $h->created_at->format('d M H:i') }} · {{ $h->to_status }} @if($h->note)<span class="text-gray-500">({{ $h->note }})</span>@endif</div>@endforeach
    </div>
</div>
<div class="space-y-4">
    @if ($canPay)
        <div class="bg-white rounded p-4 text-sm">
            <div class="font-semibold mb-2">Cara pembayaran</div>
            @foreach ($methods as $m)
                <div class="border rounded p-3 mb-2">
                    <div class="font-medium">{{ $m->label }}</div>
                    @if($m->account_number)<div>{{ $m->account_number }} @if($m->account_name)a.n. {{ $m->account_name }}@endif</div>@endif
                    @if($m->qris_image_path)<img src="{{ asset('storage/'.$m->qris_image_path) }}" class="h-40 mt-1" alt="QRIS">@endif
                    @if($m->instructions)<div class="text-gray-600 whitespace-pre-line">{{ $m->instructions }}</div>@endif
                </div>
            @endforeach
            @if ($order->payments->first()?->reject_reason)<div class="text-red-600 mb-2">Pembayaran sebelumnya ditolak: {{ $order->payments->first()->reject_reason }}</div>@endif
            <form method="POST" action="{{ route('order.proof', $order->access_token) }}" enctype="multipart/form-data" class="space-y-2">@csrf
                <select name="payment_method_id" required class="w-full border rounded px-2 py-1.5">@foreach ($methods as $m)<option value="{{ $m->id }}">{{ $m->label }}</option>@endforeach</select>
                <input type="file" name="proof" required accept="image/*,application/pdf">
                <button class="w-full bg-indigo-600 text-white rounded py-2">Unggah bukti pembayaran</button>
            </form>
        </div>
    @elseif ($order->payment_status === \App\Enums\PaymentStatus::WaitingVerification)
        <div class="bg-white rounded p-4 text-sm">Bukti pembayaran Anda sedang diperiksa penjual.</div>
    @endif
    @if ($store->whatsapp)
        <a target="_blank" class="block text-center bg-green-600 text-white rounded py-2 text-sm"
           href="{{ \App\Support\Phone::wa($store->whatsapp, 'Halo, saya ingin menanyakan pesanan '.$order->invoice_number) }}">Hubungi penjual via WhatsApp</a>
    @endif
</div>
</div>
@endsection
