@if ($faqs->count())
<section>
    <h2 class="text-xl font-semibold mb-3">{{ $c['title'] ?? 'Pertanyaan Umum' }}</h2>
    <div class="bg-white rounded-xl divide-y">
        @foreach ($faqs as $f)
            <details class="p-4 text-sm"><summary class="cursor-pointer font-medium">{{ $f->question }}</summary><p class="mt-2 text-gray-700 whitespace-pre-line">{{ $f->answer }}</p></details>
        @endforeach
    </div>
</section>
@endif
