<section class="rounded-xl bg-indigo-600 text-white p-8 text-center">
    <h2 class="text-2xl font-bold">{{ $c['title'] ?? 'Promo Spesial' }}</h2>
    @if (! empty($c['text']))<p class="mt-2 text-indigo-100 whitespace-pre-line">{{ $c['text'] }}</p>@endif
    <a href="{{ ! empty($c['button_link']) ? $c['button_link'] : route('store.catalog', $store) }}" class="inline-block mt-4 bg-white text-indigo-700 rounded-full px-6 py-2 font-medium">{{ $c['button_text'] ?? 'Pesan Sekarang' }}</a>
</section>
