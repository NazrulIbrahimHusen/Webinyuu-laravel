@extends('layouts.admin')
@section('content')
<div class="flex items-center gap-3 mb-4"><h1 class="text-xl font-semibold">Seller</h1>
    <form class="ml-auto"><input name="q" value="{{ request('q') }}" placeholder="Nama / email / toko" class="border rounded px-3 py-1.5 text-sm"></form></div>
<div class="bg-white rounded divide-y">
    @forelse ($users as $u)
        @php $sub = $subs[$u->id]; @endphp
        <div class="p-3 text-sm">
            <div class="flex flex-wrap items-center gap-3">
                <div class="flex-1 min-w-0"><div class="font-medium">{{ $u->name }} <span class="text-gray-500 font-normal">{{ $u->email }}</span></div>
                    <div class="text-xs text-gray-500">
                        @if($u->store)<a class="text-indigo-600" target="_blank" href="{{ url($u->store->slug) }}">/{{ $u->store->slug }}</a> · {{ $u->store->name }} @if(! $u->store->is_active)<span class="text-red-600">(nonaktif)</span>@endif @else belum membuat katalog @endif
                        · daftar {{ $u->created_at->format('d M Y') }}</div></div>
                <div class="text-xs">{{ $sub?->plan->name ?? 'Basic' }} @if($sub)· {{ $sub->status->label() }} s/d {{ $sub->ends_at->format('d M Y') }}@endif</div>
                @if ($u->store)<form method="POST" action="{{ route('admin.sellers.toggle', $u->id) }}">@csrf<button class="text-xs text-red-600">{{ $u->store->is_active ? 'Nonaktifkan' : 'Aktifkan' }}</button></form>@endif
            </div>
            <details class="mt-2"><summary class="cursor-pointer text-xs text-indigo-600">Beri langganan manual</summary>
                <form method="POST" action="{{ route('admin.sellers.grant', $u->id) }}" class="flex gap-2 mt-2 text-xs">@csrf
                    <select name="plan_id" class="border rounded px-2 py-1">@foreach ($plans as $pl)<option value="{{ $pl->id }}">{{ $pl->name }}</option>@endforeach</select>
                    <select name="period" class="border rounded px-2 py-1"><option value="monthly">1 bulan</option><option value="yearly">1 tahun</option></select>
                    <button class="bg-gray-800 text-white rounded px-3">Aktifkan</button></form></details>
        </div>
    @empty
        <p class="p-4 text-sm text-gray-500">Belum ada seller.</p>
    @endforelse
</div>
<div class="mt-4">{{ $users->links() }}</div>
@endsection
