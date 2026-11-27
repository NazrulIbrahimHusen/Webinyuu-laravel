@extends('layouts.admin')
@section('content')
<h1 class="text-xl font-semibold mb-4">Dashboard</h1>
<div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-6">
    <div class="bg-white rounded p-4"><div class="text-xs text-gray-500">Seller</div><div class="text-2xl font-semibold">{{ $sellers }}</div></div>
    <div class="bg-white rounded p-4"><div class="text-xs text-gray-500">Katalog</div><div class="text-2xl font-semibold">{{ $stores }}</div></div>
    <a href="{{ route('admin.payments.index') }}" class="bg-white rounded p-4 {{ $toVerify ? 'ring-2 ring-red-400' : '' }}"><div class="text-xs text-gray-500">Menunggu verifikasi</div><div class="text-2xl font-semibold">{{ $toVerify }}</div></a>
    <div class="bg-white rounded p-4"><div class="text-xs text-gray-500">Pendapatan bulan ini</div><div class="text-xl font-semibold"><x-rp :v="$revenueMonth" /></div></div>
</div>
<div class="grid md:grid-cols-2 gap-4">
    <div class="bg-white rounded p-4 text-sm">
        <div class="font-semibold mb-2">Langganan aktif per paket</div>
        @forelse ($byPlan as $name => $n)<div class="flex justify-between py-0.5"><span>{{ $name }}</span><b>{{ $n }}</b></div>@empty<p class="text-gray-500">Belum ada langganan berbayar.</p>@endforelse
        @if ($graceCount)<div class="mt-2 text-red-600">{{ $graceCount }} seller dalam masa tenggang</div>@endif
    </div>
    <div class="bg-white rounded p-4 text-sm">
        <div class="font-semibold mb-2">Berakhir dalam 7 hari</div>
        @forelse ($expiring as $s)<div class="flex justify-between py-0.5"><span>{{ $s->user->store?->name ?? $s->user->name }} · {{ $s->plan->name }}</span><span>{{ $s->ends_at->format('d M') }}</span></div>@empty<p class="text-gray-500">Tidak ada.</p>@endforelse
    </div>
</div>
@endsection
