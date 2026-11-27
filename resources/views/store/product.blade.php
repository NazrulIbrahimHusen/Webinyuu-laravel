@extends('layouts.public')
@section('title', $product->name.' - '.$store->name)
@section('content')
<div class="bg-white rounded p-4 grid md:grid-cols-2 gap-6" x-data="{ main: '{{ $product->images->first() ? asset('storage/'.$product->images->first()->path) : '' }}' }">
    <div>
        <div class="aspect-square bg-gray-100 rounded overflow-hidden"><img x-show="main" :src="main" class="h-full w-full object-cover" alt="{{ $product->name }}"></div>
        <div class="flex gap-2 mt-2">@foreach ($product->images as $img)<img src="{{ asset('storage/'.$img->path) }}" @click="main = $el.src" class="h-14 w-14 object-cover rounded cursor-pointer">@endforeach</div>
    </div>
    <div>
        <h1 class="text-xl font-semibold">{{ $product->name }}</h1>
        <div class="text-xl text-indigo-700 my-2"><x-rp :v="$product->price" /></div>
        <p class="text-sm whitespace-pre-line text-gray-700">{{ $product->description }}</p>
        @if($product->track_stock)<p class="text-xs text-gray-500 mt-2">Stok: {{ $product->stock }}</p>@endif
        <div class="mt-4">
        @if ($store->hasFeature('checkout'))
            @if ($product->track_stock && $product->stock < 1)
                <span class="text-red-600 text-sm">Stok habis</span>
            @else
                <form method="POST" action="{{ route('store.cart.add', $store) }}" class="flex gap-2">@csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <input type="number" name="qty" value="1" min="1" max="99" class="w-20 border rounded px-2 py-2">
                    <button class="bg-indigo-600 text-white rounded px-5">Tambah ke keranjang</button></form>
            @endif
        @elseif ($store->whatsapp)
            <a target="_blank" href="{{ \App\Support\Phone::wa($store->whatsapp, 'Halo, saya tertarik dengan '.$product->name) }}" class="inline-block bg-green-600 text-white rounded px-5 py-2">Tanya via WhatsApp</a>
        @endif
        </div>
    </div>
</div>
@endsection
