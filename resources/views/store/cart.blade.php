@extends('layouts.public')
@section('content')
<h1 class="text-xl font-semibold mb-4">Keranjang</h1>
@if ($items->isEmpty())
    <p class="text-sm text-gray-500">Keranjang kosong. <a class="text-indigo-600" href="{{ route('store.catalog', $store) }}">Lihat produk</a></p>
@else
<div class="bg-white rounded divide-y">
    @foreach ($items as $row)
        <div class="p-3 flex items-center gap-3">
            <div class="flex-1 min-w-0"><div class="font-medium truncate">{{ $row['product']->name }}</div><div class="text-xs text-gray-500"><x-rp :v="$row['product']->price" /></div></div>
            <form method="POST" action="{{ route('store.cart.update', $store) }}" class="flex gap-1">@csrf @method('PATCH')
                <input type="hidden" name="product_id" value="{{ $row['product']->id }}">
                <input type="number" name="qty" value="{{ $row['qty'] }}" min="0" max="99" class="w-16 border rounded px-2 py-1 text-sm"><button class="text-xs text-indigo-600">Ubah</button></form>
            <div class="w-28 text-right text-sm"><x-rp :v="$row['line']" /></div>
            <form method="POST" action="{{ route('store.cart.remove', [$store, $row['product']->id]) }}">@csrf @method('DELETE')<button class="text-red-600 text-xs">Hapus</button></form>
        </div>
    @endforeach
    <div class="p-3 flex justify-between font-semibold"><span>Subtotal</span><x-rp :v="$subtotal" /></div>
</div>
<a href="{{ route('store.checkout', $store) }}" class="mt-4 block text-center bg-indigo-600 text-white rounded py-3">Lanjut ke checkout</a>
@endif
@endsection
