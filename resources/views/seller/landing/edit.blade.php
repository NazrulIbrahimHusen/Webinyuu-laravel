@extends('layouts.seller')
@section('content')
<div class="flex flex-wrap items-center gap-3 mb-4">
    <h1 class="text-xl font-semibold">Landing Page</h1>
    <span class="text-xs rounded px-2 py-1 {{ $page->status === 'published' ? 'bg-green-100 text-green-800' : 'bg-gray-200' }}">{{ $page->status === 'published' ? 'Dipublikasikan' : 'Draf' }}</span>
    <a href="{{ route('seller.landing.preview') }}" target="_blank" class="ml-auto text-indigo-600 text-sm">Pratinjau</a>
    @if ($page->status === 'published')<a href="{{ route('store.home', $store) }}" target="_blank" class="text-indigo-600 text-sm">Lihat live</a>@endif
</div>
<p class="text-xs text-gray-500 mb-3">Landing page tampil di {{ url($store->slug) }}. Katalog lengkap selalu ada di {{ url($store->slug.'/katalog') }}. Jika belum dipublikasikan atau paket turun, alamat utama menampilkan katalog.</p>

<form method="POST" action="{{ route('seller.landing.update') }}" enctype="multipart/form-data" class="space-y-3">
    @csrf @method('PUT')
    <div class="bg-white rounded p-4 grid md:grid-cols-3 gap-3 text-sm">
        <label>Status
            <select name="status" class="mt-1 w-full border rounded px-2 py-2">
                <option value="draft" @selected(old('status', $page->status) === 'draft')>Draf</option>
                <option value="published" @selected(old('status', $page->status) === 'published')>Publikasikan</option></select></label>
        <label>Judul SEO<input name="meta_title" maxlength="70" value="{{ old('meta_title', $page->meta_title) }}" class="mt-1 w-full border rounded px-3 py-2"></label>
        <label>Deskripsi SEO<input name="meta_description" maxlength="160" value="{{ old('meta_description', $page->meta_description) }}" class="mt-1 w-full border rounded px-3 py-2"></label>
    </div>

    @foreach ($sections as $s)
        @php $c = $s->content ?? []; $t = $s->type; $n = "sections[$t]"; @endphp
        <div class="bg-white rounded p-4 text-sm space-y-2">
            <div class="flex items-center gap-3">
                <div class="font-semibold">{{ $labels[$t] }}</div>
                <label class="ml-auto flex items-center gap-1"><input type="checkbox" name="{{ $n }}[enabled]" value="1" @checked($s->is_enabled)> Tampilkan</label>
                <label class="flex items-center gap-1">Urutan <input type="number" name="{{ $n }}[sort_order]" value="{{ $s->sort_order }}" min="0" max="99" class="w-16 border rounded px-2 py-1"></label>
            </div>
            @switch($t)
                @case('hero')
                    <input name="{{ $n }}[headline]" value="{{ $c['headline'] ?? '' }}" placeholder="Judul utama" class="w-full border rounded px-3 py-2">
                    <input name="{{ $n }}[subheadline]" value="{{ $c['subheadline'] ?? '' }}" placeholder="Sub judul" class="w-full border rounded px-3 py-2">
                    <input name="{{ $n }}[button_text]" value="{{ $c['button_text'] ?? '' }}" placeholder="Teks tombol (menuju katalog)" class="w-full border rounded px-3 py-2">
                    @if (! empty($c['image']))<img src="{{ asset('storage/'.$c['image']) }}" class="h-20 rounded">@endif
                    <label class="block text-xs">Gambar latar<input type="file" name="image_hero" accept="image/*" class="block"></label>
                    @break
                @case('about')
                    <input name="{{ $n }}[title]" value="{{ $c['title'] ?? '' }}" placeholder="Judul" class="w-full border rounded px-3 py-2">
                    <textarea name="{{ $n }}[text]" rows="4" placeholder="Cerita bisnis Anda" class="w-full border rounded px-3 py-2">{{ $c['text'] ?? '' }}</textarea>
                    @if (! empty($c['image']))<img src="{{ asset('storage/'.$c['image']) }}" class="h-20 rounded">@endif
                    <label class="block text-xs">Gambar<input type="file" name="image_about" accept="image/*" class="block"></label>
                    @break
                @case('featured')
                    <input name="{{ $n }}[title]" value="{{ $c['title'] ?? '' }}" placeholder="Judul" class="w-full border rounded px-3 py-2">
                    <input type="hidden" name="featured_present" value="1">
                    <div class="text-xs text-gray-500">Pilih maksimal {{ $limits['featured'] }} produk terbit.</div>
                    <div class="grid md:grid-cols-2 gap-1 max-h-48 overflow-y-auto border rounded p-2">
                        @forelse ($products as $p)
                            <label class="flex items-center gap-2"><input type="checkbox" name="featured_ids[]" value="{{ $p->id }}" @checked(in_array($p->id, old('featured_ids', $featuredIds)))> {{ $p->name }}</label>
                        @empty<span class="text-gray-500">Belum ada produk terbit.</span>@endforelse
                    </div>
                    @break
                @case('contact')
                    <input name="{{ $n }}[title]" value="{{ $c['title'] ?? '' }}" placeholder="Judul" class="w-full border rounded px-3 py-2">
                    <textarea name="{{ $n }}[text]" rows="2" placeholder="Teks pengantar (opsional). WhatsApp, email, dan alamat diambil dari Profil Toko." class="w-full border rounded px-3 py-2">{{ $c['text'] ?? '' }}</textarea>
                    @break
                @case('gallery')
                    <input name="{{ $n }}[title]" value="{{ $c['title'] ?? '' }}" placeholder="Judul" class="w-full border rounded px-3 py-2">
                    <div class="flex gap-2 flex-wrap">@foreach (($c['images'] ?? []) as $i => $path)
                        <label class="text-xs text-center"><img src="{{ asset('storage/'.$path) }}" class="h-16 w-16 object-cover rounded"><input type="checkbox" name="gallery_remove[]" value="{{ $i }}"> hapus</label>
                    @endforeach</div>
                    <label class="block text-xs">Tambah foto (total maks {{ $limits['gallery'] }})<input type="file" name="gallery_images[]" multiple accept="image/*" class="block"></label>
                    @break
                @case('promo')
                    <input name="{{ $n }}[title]" value="{{ $c['title'] ?? '' }}" placeholder="Judul promo" class="w-full border rounded px-3 py-2">
                    <textarea name="{{ $n }}[text]" rows="2" placeholder="Deskripsi promo" class="w-full border rounded px-3 py-2">{{ $c['text'] ?? '' }}</textarea>
                    <div class="grid grid-cols-2 gap-2">
                        <input name="{{ $n }}[button_text]" value="{{ $c['button_text'] ?? '' }}" placeholder="Teks tombol" class="border rounded px-3 py-2">
                        <input name="{{ $n }}[button_link]" value="{{ $c['button_link'] ?? '' }}" placeholder="https://... (kosong = ke katalog)" class="border rounded px-3 py-2">
                    </div>
                    @break
                @default
                    <input name="{{ $n }}[title]" value="{{ $c['title'] ?? '' }}" placeholder="Judul section" class="w-full border rounded px-3 py-2">
                    <p class="text-xs text-gray-500">Isi {{ strtolower($labels[$t]) }} dikelola di bagian bawah halaman ini.</p>
            @endswitch
        </div>
    @endforeach

    @foreach ($locked as $t)
        <div class="bg-white rounded p-4 text-sm opacity-60">{{ $labels[$t] }} <span class="text-xs">· tersedia di paket Pro</span></div>
    @endforeach
    <button class="bg-indigo-600 text-white rounded px-5 py-2">Simpan landing page</button>
</form>

@if ($sections->contains('type', 'testimonial'))
<div class="bg-white rounded p-4 mt-6 text-sm">
    <div class="font-semibold mb-2">Testimoni ({{ $testimonials->count() }}/{{ $limits['testimonials'] }})</div>
    @foreach ($testimonials as $t)
        <div class="flex items-center gap-2 border-b py-2"><div class="flex-1"><b>{{ $t->name }}</b>: {{ $t->content }}</div>
            <form method="POST" action="{{ route('seller.landing.testimonials.destroy', $t->id) }}">@csrf @method('DELETE')<button class="text-red-600 text-xs">Hapus</button></form></div>
    @endforeach
    <form method="POST" action="{{ route('seller.landing.testimonials.store') }}" enctype="multipart/form-data" class="grid md:grid-cols-4 gap-2 mt-3">@csrf
        <input name="name" required placeholder="Nama" class="border rounded px-3 py-2">
        <input name="content" required placeholder="Isi testimoni" class="border rounded px-3 py-2 md:col-span-2">
        <input type="file" name="photo" accept="image/*" class="text-xs">
        <button class="bg-gray-800 text-white rounded py-2 md:col-span-4">Tambah testimoni</button>
    </form>
</div>
@endif

@if ($sections->contains('type', 'faq'))
<div class="bg-white rounded p-4 mt-4 text-sm">
    <div class="font-semibold mb-2">FAQ ({{ $faqs->count() }}/{{ $limits['faqs'] }})</div>
    @foreach ($faqs as $f)
        <div class="flex items-center gap-2 border-b py-2"><div class="flex-1"><b>{{ $f->question }}</b><div class="text-gray-600">{{ $f->answer }}</div></div>
            <form method="POST" action="{{ route('seller.landing.faqs.destroy', $f->id) }}">@csrf @method('DELETE')<button class="text-red-600 text-xs">Hapus</button></form></div>
    @endforeach
    <form method="POST" action="{{ route('seller.landing.faqs.store') }}" class="grid md:grid-cols-2 gap-2 mt-3">@csrf
        <input name="question" required placeholder="Pertanyaan" class="border rounded px-3 py-2">
        <input name="answer" required placeholder="Jawaban" class="border rounded px-3 py-2">
        <button class="bg-gray-800 text-white rounded py-2 md:col-span-2">Tambah FAQ</button>
    </form>
</div>
@endif
@endsection
