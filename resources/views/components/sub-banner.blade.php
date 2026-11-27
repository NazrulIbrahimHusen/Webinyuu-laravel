@php $sub = app(\App\Services\PlanService::class)->currentSubscription(auth()->id()); @endphp
@if ($sub && $sub->status === \App\Enums\SubscriptionStatus::Grace)
    <div class="mb-4 rounded bg-red-100 text-red-800 px-4 py-2 text-sm">
        Langganan {{ $sub->plan->name }} Anda sudah berakhir. Masa tenggang sampai {{ $sub->grace_ends_at->format('d M Y') }}, setelah itu akun kembali ke paket Basic.
        <a href="{{ route('seller.subscription.index') }}" class="underline font-medium">Perpanjang sekarang</a>
    </div>
@elseif ($sub && $sub->ends_at && $sub->ends_at->isBefore(now()->addDays(7)))
    <div class="mb-4 rounded bg-amber-100 text-amber-800 px-4 py-2 text-sm">
        Langganan {{ $sub->plan->name }} berakhir {{ $sub->ends_at->format('d M Y') }}.
        <a href="{{ route('seller.subscription.index') }}" class="underline font-medium">Perpanjang</a>
    </div>
@endif
