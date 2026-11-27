@extends('layouts.seller')
@section('content')
<h1 class="text-xl font-semibold mb-4">Halo, {{ auth()->user()->name }}</h1>
<div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-6">
    <div class="bg-white rounded p-4"><div class="text-xs text-gray-500">Paket</div><div class="text-lg font-semibold">{{ $plan->name }}</div></div>
    <div class="bg-white rounded p-4"><div class="text-xs text-gray-500">Produk</div><div class="text-lg font-semibold">{{ $productCount }} / {{ $plan->max_products ?? '∞' }}</div></div>
    @if ($hasOrders)
        <div class="bg-white rounded p-4"><div class="text-xs text-gray-500">Pesanan baru</div><div class="text-lg font-semibold">{{ $newOrders }}</div></div>
        <div class="bg-white rounded p-4"><div class="text-xs text-gray-500">Perlu verifikasi bayar</div><div class="text-lg font-semibold">{{ $toVerify }}</div></div>
    @endif
</div>
<div class="bg-white rounded p-4 text-sm">
    Alamat katalog: <a class="text-indigo-600" target="_blank" href="{{ route('store.home', $store) }}">{{ url($store->slug) }}</a>
</div>
@unless ($hasOrders)
    <div class="mt-4 bg-amber-50 border border-amber-200 rounded p-4 text-sm">Paket Basic hanya menampilkan katalog. Upgrade ke Starter untuk menerima pesanan.</div>
@endunless
@endsection
