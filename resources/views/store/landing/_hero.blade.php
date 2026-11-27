<section class="relative rounded-xl overflow-hidden bg-gray-800 text-white">
    @if (! empty($c['image']))<img src="{{ asset('storage/'.$c['image']) }}" class="absolute inset-0 h-full w-full object-cover opacity-50" alt="">@endif
    <div class="relative px-6 py-16 md:py-24 text-center">
        <h1 class="text-3xl md:text-4xl font-bold">{{ $c['headline'] ?? $store->name }}</h1>
        @if (! empty($c['subheadline']))<p class="mt-3 max-w-xl mx-auto text-gray-100">{{ $c['subheadline'] }}</p>@endif
        <a href="{{ route('store.catalog', $store) }}" class="inline-block mt-6 bg-white text-gray-900 rounded-full px-6 py-3 font-medium">{{ $c['button_text'] ?? 'Lihat Katalog' }}</a>
    </div>
</section>
