@extends('layouts.seller')
@section('content')
<h1 class="text-xl font-semibold mb-4">Metode Pembayaran</h1>
<p class="text-sm text-gray-500 mb-3">Pembeli mentransfer langsung ke rekening Anda. Webinyuu tidak menerima uang pesanan.</p>
<form method="POST" action="{{ route('seller.payment-methods.store') }}" enctype="multipart/form-data" class="bg-white rounded p-4 mb-4">
    @csrf <div class="font-medium text-sm mb-2">Tambah metode</div>
    @include('seller.payment-methods._form')
    <button class="mt-3 bg-indigo-600 text-white rounded px-4 py-2 text-sm">Tambah</button>
</form>
@foreach ($methods as $m)
    <details class="bg-white rounded p-4 mb-2">
        <summary class="cursor-pointer text-sm">{{ $m->label }} · {{ $m->account_number }} @unless($m->is_active)<span class="text-red-600">(nonaktif)</span>@endunless</summary>
        <form method="POST" action="{{ route('seller.payment-methods.update', $m->id) }}" enctype="multipart/form-data" class="mt-3">
            @csrf @method('PUT')
            @include('seller.payment-methods._form', ['m' => $m])
            @if ($m->qris_image_path)<img src="{{ asset('storage/'.$m->qris_image_path) }}" class="h-24 mt-2" alt="QRIS">@endif
            <button class="mt-3 bg-indigo-600 text-white rounded px-4 py-2 text-sm">Simpan</button>
        </form>
        <form method="POST" action="{{ route('seller.payment-methods.destroy', $m->id) }}" onsubmit="return confirm('Hapus metode ini?')" class="mt-2">
            @csrf @method('DELETE')<button class="text-red-600 text-sm">Hapus</button>
        </form>
    </details>
@endforeach
@endsection
