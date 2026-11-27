@if ($featured->count())
<section>
    <h2 class="text-xl font-semibold mb-3">{{ $c['title'] ?? 'Produk Unggulan' }}</h2>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
        @foreach ($featured as $p)
            @php $img = $p->images->firstWhere('is_primary', true) ?? $p->images->first(); @endphp
            <a href="{{ route('store.product', [$store, $p->slug]) }}" class="bg-white rounded overflow-hidden block">
                <div class="aspect-square bg-gray-100">@if($img)<img src="{{ asset('storage/'.$img->path) }}" class="h-full w-full object-cover" alt="{{ $p->name }}">@endif</div>
                <div class="p-3"><div class="text-sm font-medium line-clamp-2">{{ $p->name }}</div><div class="text-sm text-indigo-700"><x-rp :v="$p->price" /></div></div>
            </a>
        @endforeach
    </div>
    <div class="text-center mt-4"><a href="{{ route('store.catalog', $store) }}" class="text-indigo-600 text-sm">Lihat semua produk &rarr;</a></div>
</section>
@endif
