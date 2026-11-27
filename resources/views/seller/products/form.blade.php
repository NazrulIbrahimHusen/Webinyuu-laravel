@extends('layouts.seller')
@section('content')
@php $p = $product; @endphp
<h1 class="text-xl font-semibold mb-4">{{ $p ? 'Ubah produk' : 'Tambah produk' }}</h1>
<form method="POST" enctype="multipart/form-data" x-data="{ track: {{ old('track_stock', $p?->track_stock) ? 'true' : 'false' }} }"
      action="{{ $p ? route('seller.products.update', $p->id) : route('seller.products.store') }}" class="bg-white rounded p-6 space-y-3 max-w-2xl">
    @csrf @if($p) @method('PUT') @endif
    <label class="block text-sm">Nama produk<input name="name" value="{{ old('name', $p?->name) }}" required class="mt-1 w-full border rounded px-3 py-2"></label>
    <label class="block text-sm">Kategori
        <select name="category_id" class="mt-1 w-full border rounded px-3 py-2"><option value="">Tanpa kategori</option>
            @foreach ($categories as $c)<option value="{{ $c->id }}" @selected((string) old('category_id', $p?->category_id) === (string) $c->id)>{{ $c->name }}</option>@endforeach
        </select></label>
    <div class="grid grid-cols-2 gap-3">
        <label class="block text-sm">Harga (Rp)<input type="number" min="0" name="price" value="{{ old('price', $p?->price) }}" required class="mt-1 w-full border rounded px-3 py-2"></label>
        <label class="block text-sm">Berat (gram)<input type="number" min="0" name="weight_gram" value="{{ old('weight_gram', $p?->weight_gram) }}" class="mt-1 w-full border rounded px-3 py-2"></label>
    </div>
    <label class="block text-sm">Deskripsi<textarea name="description" rows="4" class="mt-1 w-full border rounded px-3 py-2">{{ old('description', $p?->description) }}</textarea></label>
    <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="track_stock" value="1" x-model="track" @checked(old('track_stock', $p?->track_stock))> Lacak stok</label>
    <label class="block text-sm" x-show="track">Stok<input type="number" min="0" name="stock" value="{{ old('stock', $p?->stock) }}" class="mt-1 w-full border rounded px-3 py-2"></label>
    <label class="block text-sm">Status
        <select name="status" class="mt-1 w-full border rounded px-3 py-2">
            @foreach (\App\Enums\ProductStatus::cases() as $s)<option value="{{ $s->value }}" @selected(old('status', $p?->status?->value ?? 'published') === $s->value)>{{ $s->label() }}</option>@endforeach
        </select></label>
    @if ($p && $p->images->count())
        <div class="text-sm">Foto saat ini (centang untuk menghapus)
            <div class="flex gap-3 mt-1 flex-wrap">@foreach ($p->images as $img)
                <label class="text-xs text-center"><img src="{{ asset('storage/'.$img->path) }}" class="h-20 w-20 object-cover rounded"><input type="checkbox" name="remove_images[]" value="{{ $img->id }}"> hapus</label>
            @endforeach</div></div>
    @endif
    <label class="block text-sm">Tambah foto (maks 5, 2 MB per foto)<input type="file" name="images[]" multiple accept="image/*" class="mt-1 block"></label>
    <div class="flex gap-2"><button class="bg-indigo-600 text-white rounded px-4 py-2">Simpan</button><a href="{{ route('seller.products.index') }}" class="px-4 py-2 text-sm text-gray-600">Batal</a></div>
</form>
@endsection
