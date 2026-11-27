@extends('layouts.seller')
@section('content')
<h1 class="text-xl font-semibold mb-4">Profil Toko</h1>
<form method="POST" action="{{ route('seller.store.update') }}" enctype="multipart/form-data" class="bg-white rounded p-6 space-y-3 max-w-2xl">
    @csrf @method('PUT')
    <label class="block text-sm">Nama bisnis<input name="name" value="{{ old('name', $store->name) }}" required class="mt-1 w-full border rounded px-3 py-2"></label>
    <p class="text-xs text-gray-500">URL: {{ url($store->slug) }} (tidak dapat diubah di sini)</p>
    <label class="block text-sm">Jenis bisnis
        <select name="business_type" class="mt-1 w-full border rounded px-3 py-2">
            @foreach ($types as $t)<option value="{{ $t->value }}" @selected(old('business_type', $store->business_type->value) === $t->value)>{{ $t->label() }}</option>@endforeach
        </select></label>
    <label class="block text-sm">Deskripsi<textarea name="description" rows="3" class="mt-1 w-full border rounded px-3 py-2">{{ old('description', $store->description) }}</textarea></label>
    <label class="block text-sm">Alamat<textarea name="address" rows="2" class="mt-1 w-full border rounded px-3 py-2">{{ old('address', $store->address) }}</textarea></label>
    <div class="grid grid-cols-2 gap-3">
        <label class="block text-sm">WhatsApp<input name="whatsapp" value="{{ old('whatsapp', $store->whatsapp) }}" class="mt-1 w-full border rounded px-3 py-2"></label>
        <label class="block text-sm">Email<input name="email" value="{{ old('email', $store->email) }}" class="mt-1 w-full border rounded px-3 py-2"></label>
    </div>
    <label class="block text-sm">Logo @if($store->logo_path)<img src="{{ asset('storage/'.$store->logo_path) }}" class="h-12 my-1">@endif<input type="file" name="logo" accept="image/*" class="mt-1 block"></label>
    @if ($store->hasFeature('bg_color'))
        <label class="block text-sm">Warna latar<input type="color" name="bg_color" value="{{ old('bg_color', $store->bg_color ?? '#f9fafb') }}" class="mt-1 block h-9 w-20"></label>
    @else
        <p class="text-xs text-gray-500">Warna latar tersedia di paket Starter.</p>
    @endif
    <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $store->is_active))> Katalog aktif (tampil publik)</label>
    <button class="bg-indigo-600 text-white rounded px-4 py-2">Simpan</button>
</form>
@endsection
