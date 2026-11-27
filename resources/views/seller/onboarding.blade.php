@extends('layouts.seller')
@section('content')
<div class="max-w-lg mx-auto bg-white rounded p-6">
    <h1 class="text-xl font-semibold mb-1">Buat katalog Anda</h1>
    <p class="text-sm text-gray-500 mb-4">URL sebaiknya tidak diubah setelah dibagikan, pilih dengan hati-hati.</p>
    <form method="POST" action="{{ route('seller.onboarding.store') }}" class="space-y-3">
        @csrf
        <label class="block text-sm">Nama bisnis<input name="name" value="{{ old('name') }}" required class="mt-1 w-full border rounded px-3 py-2"></label>
        <label class="block text-sm">URL katalog
            <div class="flex items-center mt-1"><span class="px-2 text-gray-500">{{ url('/') }}/</span>
            <input name="slug" value="{{ old('slug') }}" required pattern="[a-z0-9]+(-[a-z0-9]+)*" class="flex-1 border rounded px-3 py-2"></div></label>
        <label class="block text-sm">Jenis bisnis
            <select name="business_type" class="mt-1 w-full border rounded px-3 py-2">
                @foreach ($types as $t)<option value="{{ $t->value }}" @selected(old('business_type') === $t->value)>{{ $t->label() }}</option>@endforeach
            </select></label>
        <label class="block text-sm">WhatsApp<input name="whatsapp" value="{{ old('whatsapp') }}" placeholder="08xxxxxxxxxx" class="mt-1 w-full border rounded px-3 py-2"></label>
        <button class="w-full bg-indigo-600 text-white rounded py-2">Buat katalog</button>
    </form>
</div>
@endsection
