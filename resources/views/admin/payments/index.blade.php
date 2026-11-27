@extends('layouts.admin')
@section('content')
<h1 class="text-xl font-semibold mb-4">Pembayaran Langganan</h1>
<form class="bg-white rounded p-3 flex flex-wrap gap-2 mb-4 text-sm">
    <input name="q" value="{{ request('q') }}" placeholder="Invoice / nama / email" class="border rounded px-3 py-1.5">
    <select name="status" class="border rounded px-2 py-1.5">
        <option value="all" @selected($status === 'all')>Semua status</option>
        @foreach ($statuses as $s)<option value="{{ $s->value }}" @selected($status === $s->value)>{{ $s->label() }}</option>@endforeach
    </select>
    <button class="bg-gray-800 text-white rounded px-3">Filter</button>
</form>
<div class="bg-white rounded divide-y">
    @forelse ($payments as $p)
        <a href="{{ route('admin.payments.show', $p->id) }}" class="p-3 flex items-center gap-3 text-sm hover:bg-gray-50">
            <div class="flex-1 min-w-0"><div class="font-medium">{{ $p->invoice_number }} · {{ $p->user->store?->name ?? $p->user->name }}</div>
                <div class="text-xs text-gray-500">{{ $p->plan->name }} {{ $p->period->label() }} · {{ $p->created_at->format('d M Y H:i') }}</div></div>
            <b><x-rp :v="$p->total_amount" /></b><span class="text-xs rounded bg-gray-100 px-2 py-1">{{ $p->status->label() }}</span>
        </a>
    @empty
        <p class="p-4 text-sm text-gray-500">Tidak ada data.</p>
    @endforelse
</div>
<div class="mt-4">{{ $payments->links() }}</div>
@endsection
