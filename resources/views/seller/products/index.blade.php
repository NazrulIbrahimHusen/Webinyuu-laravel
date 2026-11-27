@extends('layouts.seller')
@section('content')
<div class="flex items-center mb-4 gap-3">
    <h1 class="text-xl font-semibold">Produk <span class="text-sm text-gray-500">({{ $total }}/{{ $plan->max_products ?? '∞' }})</span></h1>
    <form class="ml-auto"><input name="q" value="{{ request('q') }}" placeholder="Cari produk" class="border rounded px-3 py-1.5 text-sm"></form>
    @if ($canAdd)
        <a href="{{ route('seller.products.create') }}" class="bg-indigo-600 text-white rounded px-4 py-2 text-sm">Tambah produk</a>
    @else
        <span class="text-xs text-amber-700">Batas produk paket tercapai</span>
    @endif
</div>
<div class="bg-white rounded divide-y">
    @forelse ($products as $p)
        @php $img = $p->images->firstWhere('is_primary', true) ?? $p->images->first(); @endphp
        <div class="p-3 flex items-center gap-3">
            <div class="h-14 w-14 bg-gray-100 rounded overflow-hidden shrink-0">@if($img)<img src="{{ asset('storage/'.$img->path) }}" class="h-full w-full object-cover" alt="">@endif</div>
            <div class="flex-1 min-w-0">
                <div class="font-medium truncate">{{ $p->name }}</div>
                <div class="text-xs text-gray-500"><x-rp :v="$p->price" /> · {{ $p->category?->name ?? 'Tanpa kategori' }} · {{ $p->status->label() }}@if($p->track_stock) · stok {{ $p->stock }}@endif</div>
            </div>
            <a href="{{ route('seller.products.edit', $p->id) }}" class="text-indigo-600 text-sm">Ubah</a>
            <form method="POST" action="{{ route('seller.products.destroy', $p->id) }}" onsubmit="return confirm('Hapus produk ini?')">@csrf @method('DELETE')<button class="text-red-600 text-sm">Hapus</button></form>
        </div>
    @empty
        <p class="p-4 text-sm text-gray-500">Belum ada produk.</p>
    @endforelse
</div>
<div class="mt-4">{{ $products->links() }}</div>
@endsection
