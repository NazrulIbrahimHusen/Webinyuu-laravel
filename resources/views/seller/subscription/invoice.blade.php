@extends('layouts.seller')
@section('content')
<a href="{{ route('seller.subscription.index') }}" class="text-sm text-gray-500">&larr; Langganan</a>
<div class="flex items-center gap-2 my-2"><h1 class="text-xl font-semibold">{{ $invoice->invoice_number }}</h1>
    <span class="text-xs rounded bg-amber-100 px-2 py-1">{{ $invoice->status->label() }}</span></div>
<div class="grid md:grid-cols-2 gap-4">
    <div class="bg-white rounded p-4 text-sm space-y-1">
        <div>Paket {{ $invoice->plan->name }} · {{ $invoice->period->label() }}</div>
        <div class="flex justify-between"><span>Harga paket</span><x-rp :v="$invoice->base_amount" /></div>
        <div class="flex justify-between"><span>Kode unik</span><span>{{ $invoice->unique_code }}</span></div>
        <div class="flex justify-between font-semibold text-base border-t pt-2"><span>Total transfer</span><x-rp :v="$invoice->total_amount" /></div>
        <p class="text-xs text-red-600">Transfer tepat sesuai total (termasuk kode unik) agar mudah dicocokkan.</p>
        @if ($invoice->due_at && $invoice->status === \App\Enums\SubscriptionPaymentStatus::Pending)
            <div class="text-xs text-gray-500">Bayar sebelum {{ $invoice->due_at->format('d M Y H:i') }}</div>
        @endif
        @if ($invoice->reject_reason)<div class="text-red-600">Ditolak: {{ $invoice->reject_reason }}</div>@endif
        @if ($invoice->status === \App\Enums\SubscriptionPaymentStatus::Pending)
            <form method="POST" action="{{ route('seller.subscription.cancel', $invoice->id) }}" class="pt-2">@csrf<button class="text-red-600 text-xs">Batalkan invoice</button></form>
        @endif
    </div>
    <div class="space-y-4">
        <div class="bg-white rounded p-4 text-sm">
            <div class="font-semibold mb-2">Transfer ke</div>
            @forelse ($accounts as $a)<div class="border rounded p-2 mb-2"><b>{{ $a->bank_name }}</b> {{ $a->account_number }}<div class="text-gray-500">a.n. {{ $a->account_name }}</div></div>
            @empty<p class="text-gray-500">Rekening belum tersedia, hubungi admin.</p>@endforelse
        </div>
        @if ($invoice->canUploadProof())
            <form method="POST" action="{{ route('seller.subscription.proof', $invoice->id) }}" enctype="multipart/form-data" class="bg-white rounded p-4 text-sm space-y-2">
                @csrf <div class="font-semibold">Unggah bukti transfer</div>
                <input type="file" name="proof" required accept="image/*,application/pdf">
                <button class="w-full bg-indigo-600 text-white rounded py-2">Kirim bukti</button>
            </form>
        @elseif ($invoice->status === \App\Enums\SubscriptionPaymentStatus::WaitingVerification)
            <div class="bg-white rounded p-4 text-sm">Bukti terkirim, menunggu verifikasi admin. <a class="text-indigo-600" target="_blank" href="{{ route('seller.subscription.proof.view', $invoice->id) }}">Lihat bukti</a></div>
        @elseif ($invoice->status === \App\Enums\SubscriptionPaymentStatus::Paid)
            <div class="bg-green-50 rounded p-4 text-sm text-green-800">Pembayaran terverifikasi, paket sudah aktif.</div>
        @endif
    </div>
</div>
@endsection
