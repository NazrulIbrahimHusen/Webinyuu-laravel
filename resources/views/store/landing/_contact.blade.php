<section class="bg-white rounded-xl p-6 text-sm">
    <h2 class="text-xl font-semibold mb-2">{{ $c['title'] ?? 'Hubungi Kami' }}</h2>
    @if (! empty($c['text']))<p class="text-gray-700 mb-3 whitespace-pre-line">{{ $c['text'] }}</p>@endif
    <div class="space-y-1">
        @if ($store->address)<div>{{ $store->address }}</div>@endif
        @if ($store->email)<div>{{ $store->email }}</div>@endif
    </div>
    @if ($store->whatsapp)<a target="_blank" href="{{ \App\Support\Phone::wa($store->whatsapp, 'Halo '.$store->name) }}" class="inline-block mt-3 bg-green-600 text-white rounded-full px-5 py-2">Chat WhatsApp</a>@endif
</section>
