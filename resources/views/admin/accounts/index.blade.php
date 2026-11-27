@extends('layouts.admin')
@section('content')
<h1 class="text-xl font-semibold mb-1">Rekening Penerima Langganan</h1>
<p class="text-sm text-gray-500 mb-4">Rekening aktif akan tampil di invoice seller.</p>
<form method="POST" action="{{ route('admin.accounts.store') }}" class="bg-white rounded p-4 flex flex-wrap gap-2 mb-4 text-sm">@csrf
    <input name="bank_name" required placeholder="Bank / e-wallet" class="border rounded px-3 py-2">
    <input name="account_number" required placeholder="Nomor rekening" class="border rounded px-3 py-2">
    <input name="account_name" required placeholder="Atas nama" class="border rounded px-3 py-2">
    <label class="flex items-center gap-1"><input type="checkbox" name="is_active" value="1" checked> Aktif</label>
    <button class="bg-indigo-600 text-white rounded px-4">Tambah</button>
</form>
<div class="bg-white rounded divide-y">
    @forelse ($accounts as $a)
        <div class="p-3 flex flex-wrap items-center gap-2 text-sm">
            <form method="POST" action="{{ route('admin.accounts.update', $a->id) }}" class="flex flex-wrap flex-1 gap-2 items-center">@csrf @method('PUT')
                <input name="bank_name" value="{{ $a->bank_name }}" class="border rounded px-2 py-1 w-32">
                <input name="account_number" value="{{ $a->account_number }}" class="border rounded px-2 py-1 w-44">
                <input name="account_name" value="{{ $a->account_name }}" class="border rounded px-2 py-1 flex-1">
                <label class="flex items-center gap-1"><input type="checkbox" name="is_active" value="1" @checked($a->is_active)> Aktif</label>
                <button class="text-indigo-600">Simpan</button>
            </form>
            <form method="POST" action="{{ route('admin.accounts.destroy', $a->id) }}" onsubmit="return confirm('Hapus rekening?')">@csrf @method('DELETE')<button class="text-red-600">Hapus</button></form>
        </div>
    @empty
        <p class="p-4 text-sm text-gray-500">Belum ada rekening. Tambahkan satu supaya seller bisa membayar.</p>
    @endforelse
</div>
@endsection
