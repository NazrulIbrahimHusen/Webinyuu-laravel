@if (! empty($c['images']))
<section>
    <h2 class="text-xl font-semibold mb-3">{{ $c['title'] ?? 'Galeri' }}</h2>
    <div class="grid grid-cols-2 md:grid-cols-3 gap-2">
        @foreach ($c['images'] as $path)<img src="{{ asset('storage/'.$path) }}" class="rounded-lg w-full aspect-square object-cover" alt="">@endforeach
    </div>
</section>
@endif
