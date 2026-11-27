@extends('layouts.seller')
@section('content')
<h1 class="text-xl font-semibold mb-4">Langganan</h1>
<div class="bg-white rounded p-4 mb-4 text-sm">
    <div>Paket saat ini: <b>{{ $effective->name }}</b>
        @if ($current) · {{ $current->status->label() }} · berakhir {{ $current->ends_at->format('d M Y') }}
        @else · tanpa langganan berbayar @endif</div>
    <p class="text-xs text-gray-500 mt-1">Upgrade berlaku setelah pembayaran diverifikasi. Perpanjang sebelum jatuh tempo agar sisa hari tidak hilang.</p>
</div>
<div class="grid md:grid-cols-2 gap-4 mb-6">
    @foreach ($plans as $plan)
        <div class="bg-white rounded p-4">
            <div class="text-lg font-semibold">{{ $plan->name }}</div>
            <ul class="text-sm text-gray-600 my-2 list-disc pl-5">
                <li>Produk: {{ $plan->max_products ?? 'tanpa batas' }}</li>
                @foreach (\App\Models\Plan::FEATURES as $k => $label)@if($plan->hasFeature($k))<li>{{ $label }}</li>@endif @endforeach
            </ul>
            <form method="POST" action="{{ route('seller.subscription.store') }}" class="space-y-2">
                @csrf <input type="hidden" name="plan_code" value="{{ $plan->code->value }}">
                <select name="period" class="w-full border rounded px-2 py-2 text-sm">
                    <option value="monthly">Bulanan · <x-rp :v="$plan->price_monthly" /></option>
                    @if ($plan->price_yearly)<option value="yearly">Tahunan · <x-rp :v="$plan->price_yearly" /></option>@endif
                </select>
                <button class="w-full bg-indigo-600 text-white rounded py-2 text-sm">Pilih {{ $plan->name }}</button>
            </form>
        </div>
    @endforeach
</div>
<h2 class="font-semibold mb-2">Riwayat invoice</h2>
<div class="bg-white rounded divide-y">
    @forelse ($invoices as $i)
        <a href="{{ route('seller.subscription.show', $i->id) }}" class="p-3 flex items-center gap-3 text-sm hover:bg-gray-50">
            <span class="flex-1">{{ $i->invoice_number }} · {{ $i->plan->name }} {{ $i->period->label() }}</span>
            <x-rp :v="$i->total_amount" /><span class="text-xs rounded bg-gray-100 px-2 py-1">{{ $i->status->label() }}</span>
        </a>
    @empty
        <p class="p-4 text-sm text-gray-500">Belum ada invoice.</p>
    @endforelse
</div>
<div class="mt-3">{{ $invoices->links() }}</div>
@endsection
