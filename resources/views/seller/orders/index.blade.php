@extends('layouts.seller')
@section('content')
<h1 class="text-xl font-semibold mb-4">Pesanan</h1>
<form class="bg-white rounded p-3 flex flex-wrap gap-2 mb-4 text-sm">
    <input name="q" value="{{ request('q') }}" placeholder="Invoice / nama pembeli" class="border rounded px-3 py-1.5">
    <select name="order_status" class="border rounded px-2 py-1.5"><option value="">Semua status</option>
        @foreach ($orderStatuses as $s)<option value="{{ $s->value }}" @selected(request('order_status') === $s->value)>{{ $s->label() }}</option>@endforeach</select>
    <select name="payment_status" class="border rounded px-2 py-1.5"><option value="">Semua pembayaran</option>
        @foreach ($paymentStatuses as $s)<option value="{{ $s->value }}" @selected(request('payment_status') === $s->value)>{{ $s->label() }}</option>@endforeach</select>
    <button class="bg-gray-800 text-white rounded px-3">Filter</button>
</form>
<div class="bg-white rounded divide-y">
    @forelse ($orders as $o)
        <a href="{{ route('seller.orders.show', $o->id) }}" class="p-3 flex items-center gap-3 hover:bg-gray-50">
            <div class="flex-1 min-w-0"><div class="font-medium">{{ $o->invoice_number }} · {{ $o->customer_name }}</div>
                <div class="text-xs text-gray-500">{{ $o->created_at->format('d M Y H:i') }} · <x-rp :v="$o->transfer_amount" /></div></div>
            <span class="text-xs rounded bg-gray-100 px-2 py-1">{{ $o->order_status->label() }}</span>
            <span class="text-xs rounded bg-amber-100 px-2 py-1">{{ $o->payment_status->label() }}</span>
        </a>
    @empty
        <p class="p-4 text-sm text-gray-500">Belum ada pesanan.</p>
    @endforelse
</div>
<div class="mt-4">{{ $orders->links() }}</div>
@endsection
