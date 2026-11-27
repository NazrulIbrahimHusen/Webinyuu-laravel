@extends('layouts.public')
@section('content')
@php $type = $store->business_type->value; @endphp
<h1 class="text-xl font-semibold mb-4">Checkout</h1>
@unless ($hasPayment)
    <div class="bg-amber-50 border border-amber-200 rounded p-4 text-sm">Toko ini belum mengatur metode pembayaran. Silakan hubungi penjual via WhatsApp.</div>
@else
<form method="POST" action="{{ route('store.checkout.place', $store) }}" class="grid md:grid-cols-3 gap-4"
      x-data="{ f: '{{ old('fulfillment', 'pickup') }}', zones: {{ $zones->pluck('fee', 'id')->toJson() }}, zone: '{{ old('shipping_zone_id') }}', sub: {{ $subtotal }},
                get delivery() { return {{ $type === 'physical' ? 'true' : 'false' }} || this.f === 'delivery' },
                get ship() { return this.delivery && this.zone ? (this.zones[this.zone] || 0) : 0 } }">
    @csrf
    <div class="md:col-span-2 bg-white rounded p-4 space-y-3">
        <label class="block text-sm">Nama<input name="customer_name" value="{{ old('customer_name') }}" required class="mt-1 w-full border rounded px-3 py-2"></label>
        <label class="block text-sm">Nomor WhatsApp/telepon<input name="customer_phone" value="{{ old('customer_phone') }}" required class="mt-1 w-full border rounded px-3 py-2"></label>
        <label class="block text-sm">Email @if(in_array($type, ['digital','service']))(wajib)@else(opsional)@endif<input type="email" name="customer_email" value="{{ old('customer_email') }}" @required(in_array($type, ['digital','service'])) class="mt-1 w-full border rounded px-3 py-2"></label>
        @if ($type === 'fnb')
            <div class="text-sm">Pengambilan
                <label class="ml-3"><input type="radio" name="fulfillment" value="pickup" x-model="f"> Ambil sendiri</label>
                <label class="ml-3"><input type="radio" name="fulfillment" value="delivery" x-model="f"> Diantar</label></div>
        @endif
        @if (in_array($type, ['fnb', 'physical']))
            <div x-show="delivery" class="space-y-3">
                <label class="block text-sm">Alamat lengkap<textarea name="shipping_address" rows="3" class="mt-1 w-full border rounded px-3 py-2">{{ old('shipping_address') }}</textarea></label>
                @if ($zones->count())
                    <label class="block text-sm">Wilayah pengiriman
                        <select name="shipping_zone_id" x-model="zone" class="mt-1 w-full border rounded px-3 py-2"><option value="">Pilih wilayah</option>
                            @foreach ($zones as $z)<option value="{{ $z->id }}">{{ $z->name }} (Rp{{ number_format($z->fee, 0, ',', '.') }})</option>@endforeach</select></label>
                @endif
            </div>
        @endif
        <label class="block text-sm">Catatan @if($type === 'service')(jelaskan kebutuhan Anda, wajib)@else(opsional)@endif<textarea name="customer_note" rows="2" @required($type === 'service') class="mt-1 w-full border rounded px-3 py-2">{{ old('customer_note') }}</textarea></label>
    </div>
    <div class="bg-white rounded p-4 text-sm h-fit space-y-1">
        <div class="font-semibold mb-1">Ringkasan</div>
        @foreach ($items as $r)<div class="flex justify-between"><span>{{ $r['product']->name }} × {{ $r['qty'] }}</span><span><x-rp :v="$r['line']" /></span></div>@endforeach
        <div class="border-t pt-2 flex justify-between font-semibold"><span>Bayar ke penjual</span><span x-text="'Rp' + sub.toLocaleString('id-ID')"></span></div>
        <div class="flex justify-between text-gray-500" x-show="delivery"><span>Ongkir (bayar ke kurir)</span><span x-text="'Rp' + ship.toLocaleString('id-ID')"></span></div>
        <button class="mt-3 w-full bg-indigo-600 text-white rounded py-2">Buat pesanan</button>
    </div>
</form>
@endunless
@endsection
