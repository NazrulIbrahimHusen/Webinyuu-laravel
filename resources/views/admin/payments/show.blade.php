@extends('layouts.admin')
@section('content')
<a href="{{ route('admin.payments.index') }}" class="text-sm text-gray-500">&larr; Semua pembayaran</a>
<div class="flex items-center gap-2 my-2"><h1 class="text-xl font-semibold">{{ $p->invoice_number }}</h1><span class="text-xs rounded bg-amber-100 px-2 py-1">{{ $p->status->label() }}</span></div>
<div class="grid md:grid-cols-2 gap-4">
    <div class="bg-white rounded p-4 text-sm space-y-1">
        <div><b>{{ $p->user->name }}</b> · {{ $p->user->email }}</div>
        <div class="text-gray-500">Katalog: {{ $p->user->store?->name ?? '-' }} @if($p->user->store)(/{{ $p->user->store->slug }})@endif</div>
        <div class="border-t pt-2">Paket {{ $p->plan->name }} · {{ $p->period->label() }}</div>
        <div class="flex justify-between"><span>Harga</span><x-rp :v="$p->base_amount" /></div>
        <div class="flex justify-between"><span>Kode unik</span><span>{{ $p->unique_code }}</span></div>
        <div class="flex justify-between font-semibold text-base"><span>Cocokkan dengan mutasi</span><x-rp :v="$p->total_amount" /></div>
        @if ($p->verified_at)<div class="text-xs text-gray-500">Diproses {{ $p->verified_at->format('d M Y H:i') }} oleh {{ $p->verifier?->name }}</div>@endif
        @if ($p->reject_reason)<div class="text-red-600">Alasan penolakan: {{ $p->reject_reason }}</div>@endif
        @if ($p->subscription)<div class="text-xs text-gray-500">Langganan: {{ $p->subscription->starts_at->format('d M Y') }} - {{ $p->subscription->ends_at->format('d M Y') }}</div>@endif
    </div>
    <div class="space-y-4">
        <div class="bg-white rounded p-4 text-sm">
            <div class="font-semibold mb-2">Bukti transfer</div>
            @if ($p->proof_path)
                @if ($isPdf)<a class="text-indigo-600" target="_blank" href="{{ route('admin.payments.proof', $p->id) }}">Buka PDF</a>
                @else<a target="_blank" href="{{ route('admin.payments.proof', $p->id) }}"><img src="{{ route('admin.payments.proof', $p->id) }}" class="max-h-96 rounded border" alt="Bukti"></a>@endif
            @else<p class="text-gray-500">Belum ada bukti.</p>@endif
        </div>
        @if ($p->status === \App\Enums\SubscriptionPaymentStatus::WaitingVerification)
            <div class="bg-white rounded p-4 text-sm space-y-3">
                <form method="POST" action="{{ route('admin.payments.verify', $p->id) }}" onsubmit="return confirm('Nominal sudah cocok dengan mutasi? Langganan akan langsung aktif.')">@csrf
                    <button class="w-full bg-green-600 text-white rounded py-2">Verifikasi & aktifkan</button></form>
                <form method="POST" action="{{ route('admin.payments.reject', $p->id) }}" class="flex gap-2">@csrf
                    <input name="reject_reason" required placeholder="Alasan penolakan" class="flex-1 border rounded px-2 py-1.5"><button class="bg-red-600 text-white rounded px-4">Tolak</button></form>
            </div>
        @endif
    </div>
</div>
@endsection
