@extends('layouts.public')
@section('content')
@if ($store->description)<p class="text-sm text-gray-600 mb-4">{{ $store->description }}</p>@endif
<form class="flex gap-2 mb-3"><input name="q" value="{{ request('q') }}" placeholder="Cari produk" class="flex-1 border rounded px-3 py-2 text-sm bg-white">
    @if(request('kategori'))<input type="hidden" name="kategori" value="{{ request('kategori') }}">@endif<button class="bg-gray-800 text-white rounded px-4 text-sm">Cari</button></form>
@if ($categories->count())
    <div class="flex gap-2 overflow-x-auto mb-4 text-sm">
        <a href="{{ route('store.catalog', $store) }}" class="px-3 py-1 rounded-full {{ request('kategori') ? 'bg-white' : 'bg-gray-800 text-white' }}">Semua</a>
        @foreach ($categories as $c)<a href="{{ route('store.catalog', [$store, 'kategori' => $c->slug]) }}" class="px-3 py-1 rounded-full whitespace-nowrap {{ request('kategori') === $c->slug ? 'bg-gray-800 text-white' : 'bg-white' }}">{{ $c->name }}</a>@endforeach
    </div>
@endif
<div class="grid grid-cols-2 md:grid-cols-4 gap-3">
    @forelse ($products as $p)
        @php $img = $p->images->firstWhere('is_primary', true) ?? $p->images->first(); @endphp
        <a href="{{ route('store.product', [$store, $p->slug]) }}" class="bg-white rounded overflow-hidden block">
            <div class="aspect-square bg-gray-100">@if($img)<img src="{{ asset('storage/'.$img->path) }}" class="h-full w-full object-cover" alt="{{ $p->name }}">@endif</div>
            <div class="p-3"><div class="text-sm font-medium line-clamp-2">{{ $p->name }}</div><div class="text-sm text-indigo-700"><x-rp :v="$p->price" /></div>
                @if($p->track_stock && $p->stock < 1)<div class="text-xs text-red-600">Stok habis</div>@endif</div>
        </a>
    @empty
        <p class="col-span-full text-sm text-gray-500">Belum ada produk.</p>
    @endforelse
</div>
<div class="mt-4">{{ $products->links() }}</div>
@endsection
