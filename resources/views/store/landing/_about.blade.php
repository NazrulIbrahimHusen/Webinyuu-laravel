<section class="bg-white rounded-xl p-6 grid md:grid-cols-2 gap-6 items-center">
    <div><h2 class="text-xl font-semibold mb-2">{{ $c['title'] ?? 'Tentang Kami' }}</h2><p class="text-gray-700 whitespace-pre-line">{{ $c['text'] ?? '' }}</p></div>
    @if (! empty($c['image']))<img src="{{ asset('storage/'.$c['image']) }}" class="rounded-lg w-full object-cover max-h-72" alt="">@endif
</section>
