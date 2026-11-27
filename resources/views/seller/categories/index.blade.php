@extends('layouts.seller')
@section('content')
<h1 class="text-xl font-semibold mb-4">Kategori</h1>
<form method="POST" action="{{ route('seller.categories.store') }}" class="bg-white rounded p-4 flex gap-2 mb-4">
    @csrf
    <input name="name" required placeholder="Nama kategori" class="flex-1 border rounded px-3 py-2">
    <input name="sort_order" type="number" min="0" placeholder="Urutan" class="w-24 border rounded px-3 py-2">
    <button class="bg-indigo-600 text-white rounded px-4">Tambah</button>
</form>
<div class="bg-white rounded divide-y">
    @forelse ($categories as $c)
        <div class="p-3 flex items-center gap-2">
            <form method="POST" action="{{ route('seller.categories.update', $c->id) }}" class="flex flex-1 gap-2">
                @csrf @method('PUT')
                <input name="name" value="{{ $c->name }}" class="flex-1 border rounded px-2 py-1">
                <input name="sort_order" type="number" value="{{ $c->sort_order }}" class="w-20 border rounded px-2 py-1">
                <button class="text-indigo-600 text-sm">Simpan</button>
            </form>
            <span class="text-xs text-gray-500">{{ $c->products_count }} produk</span>
            <form method="POST" action="{{ route('seller.categories.destroy', $c->id) }}" onsubmit="return confirm('Hapus kategori? Produk tidak ikut terhapus.')">
                @csrf @method('DELETE')<button class="text-red-600 text-sm">Hapus</button>
            </form>
        </div>
    @empty
        <p class="p-4 text-sm text-gray-500">Belum ada kategori.</p>
    @endforelse
</div>
@endsection
