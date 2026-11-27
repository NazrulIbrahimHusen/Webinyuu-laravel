@if ($testimonials->count())
<section>
    <h2 class="text-xl font-semibold mb-3">{{ $c['title'] ?? 'Kata Pelanggan' }}</h2>
    <div class="grid md:grid-cols-3 gap-3">
        @foreach ($testimonials as $t)
            <div class="bg-white rounded-xl p-4 text-sm">
                <p class="text-gray-700">&ldquo;{{ $t->content }}&rdquo;</p>
                <div class="flex items-center gap-2 mt-3">
                    @if ($t->photo_path)<img src="{{ asset('storage/'.$t->photo_path) }}" class="h-8 w-8 rounded-full object-cover" alt="">@endif
                    <b>{{ $t->name }}</b></div>
            </div>
        @endforeach
    </div>
</section>
@endif
