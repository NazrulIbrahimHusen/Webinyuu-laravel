@extends('layouts.seller')
@section('content')
<h1 class="text-xl font-semibold mb-4">Pengiriman</h1>
<form method="POST" action="{{ route('seller.shipping.settings') }}" class="bg-white rounded p-4 mb-4 space-y-2 max-w-2xl">
    @csrf <div class="font-medium text-sm">Data pengirim</div>
    <div class="grid grid-cols-2 gap-2">
        <input name="sender_name" value="{{ old('sender_name', $setting?->sender_name) }}" placeholder="Nama pengirim" class="border rounded px-3 py-2 text-sm">
        <input name="sender_phone" value="{{ old('sender_phone', $setting?->sender_phone) }}" placeholder="Telepon pengirim" class="border rounded px-3 py-2 text-sm">
    </div>
    <textarea name="origin_address" rows="2" placeholder="Alamat asal" class="w-full border rounded px-3 py-2 text-sm">{{ old('origin_address', $setting?->origin_address) }}</textarea>
    <input name="postal_code" value="{{ old('postal_code', $setting?->postal_code) }}" placeholder="Kode pos" class="border rounded px-3 py-2 text-sm">
    <p class="text-xs text-gray-500">Mode ongkir: manual (zona). Ongkir otomatis RajaOngkir menyusul. Ongkir dibayar pembeli langsung ke kurir (COD).</p>
    <button class="bg-indigo-600 text-white rounded px-4 py-2 text-sm">Simpan</button>
</form>

<h2 class="font-semibold mb-2">Tarif per wilayah</h2>
<form method="POST" action="{{ route('seller.shipping.zones.store') }}" class="bg-white rounded p-4 flex gap-2 mb-3">
    @csrf
    <input name="name" required placeholder="Nama wilayah (mis. Dalam kota)" class="flex-1 border rounded px-3 py-2 text-sm">
    <input name="fee" type="number" min="0" required placeholder="Tarif (Rp)" class="w-32 border rounded px-3 py-2 text-sm">
    <input name="sort_order" type="number" min="0" placeholder="Urutan" class="w-20 border rounded px-3 py-2 text-sm">
    <button class="bg-indigo-600 text-white rounded px-4 text-sm">Tambah</button>
</form>
<div class="bg-white rounded divide-y">
    @forelse ($zones as $z)
        <div class="p-3 flex gap-2 items-center">
            <form method="POST" action="{{ route('seller.shipping.zones.update', $z->id) }}" class="flex flex-1 gap-2">
                @csrf @method('PUT')
                <input name="name" value="{{ $z->name }}" class="flex-1 border rounded px-2 py-1 text-sm">
                <input name="fee" type="number" value="{{ $z->fee }}" class="w-28 border rounded px-2 py-1 text-sm">
                <input name="sort_order" type="number" value="{{ $z->sort_order }}" class="w-16 border rounded px-2 py-1 text-sm">
                <button class="text-indigo-600 text-sm">Simpan</button>
            </form>
            <form method="POST" action="{{ route('seller.shipping.zones.destroy', $z->id) }}" onsubmit="return confirm('Hapus wilayah?')">@csrf @method('DELETE')<button class="text-red-600 text-sm">Hapus</button></form>
        </div>
    @empty
        <p class="p-4 text-sm text-gray-500">Belum ada wilayah. Tanpa wilayah, ongkir dianggap Rp0 dan disepakati lewat WhatsApp.</p>
    @endforelse
</div>
@endsection
