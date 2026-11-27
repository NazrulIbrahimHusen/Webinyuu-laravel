@extends('layouts.admin')
@section('content')
<h1 class="text-xl font-semibold mb-1">Paket</h1>
<p class="text-sm text-gray-500 mb-4">Harga baru hanya berlaku untuk invoice berikutnya. Kosongkan batas produk untuk tanpa batas, dan kosongkan harga tahunan jika tidak ada paket tahunan.</p>
<div class="space-y-4">
@foreach ($plans as $plan)
    <form method="POST" action="{{ route('admin.plans.update', $plan->id) }}" class="bg-white rounded p-4 text-sm">@csrf @method('PUT')
        <div class="font-semibold mb-2">{{ $plan->name }} <span class="text-xs text-gray-400">({{ $plan->code->value }})</span></div>
        <div class="grid grid-cols-2 md:grid-cols-5 gap-2">
            <label>Nama<input name="name" value="{{ $plan->name }}" class="mt-1 w-full border rounded px-2 py-1.5"></label>
            <label>Harga bulanan<input type="number" name="price_monthly" value="{{ $plan->price_monthly }}" class="mt-1 w-full border rounded px-2 py-1.5"></label>
            <label>Harga tahunan<input type="number" name="price_yearly" value="{{ $plan->price_yearly }}" class="mt-1 w-full border rounded px-2 py-1.5"></label>
            <label>Maks produk<input type="number" name="max_products" value="{{ $plan->max_products }}" class="mt-1 w-full border rounded px-2 py-1.5"></label>
            <label>Maks unggulan<input type="number" name="max_featured_products" value="{{ $plan->max_featured_products }}" class="mt-1 w-full border rounded px-2 py-1.5"></label>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-3 gap-1 mt-3">
            @foreach ($featureList as $key => $label)
                <label class="flex items-center gap-2"><input type="checkbox" name="features[]" value="{{ $key }}" @checked($plan->hasFeature($key))> {{ $label }}</label>
            @endforeach
        </div>
        <button class="mt-3 bg-indigo-600 text-white rounded px-4 py-2">Simpan {{ $plan->name }}</button>
    </form>
@endforeach
</div>
@endsection
