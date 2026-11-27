@php $s = request()->attributes->get('store'); @endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Dashboard') - Webinyuu</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 text-gray-800">
<nav class="bg-white border-b">
    <div class="max-w-6xl mx-auto px-4 h-14 flex items-center gap-4 text-sm overflow-x-auto whitespace-nowrap">
        <a href="{{ route('dashboard') }}" class="font-bold">Webinyuu</a>
        @if ($s)
            <a href="{{ route('seller.products.index') }}">Produk</a>
            <a href="{{ route('seller.categories.index') }}">Kategori</a>
            @if ($s->hasFeature('orders'))
                <a href="{{ route('seller.orders.index') }}">Pesanan</a>
                <a href="{{ route('seller.payment-methods.index') }}">Pembayaran</a>
                <a href="{{ route('seller.shipping.index') }}">Pengiriman</a>
            @endif
            @if ($s->hasFeature('landing_page'))<a href="{{ route('seller.landing.edit') }}">Landing Page</a>@endif
            <a href="{{ route('seller.subscription.index') }}">Langganan</a>
            <a href="{{ route('seller.store.edit') }}">Profil Toko</a>
            <a href="{{ route('store.home', $s) }}" target="_blank" class="text-indigo-600">Lihat katalog</a>
        @endif
        <form method="POST" action="{{ route('logout') }}" class="ml-auto">@csrf<button class="text-gray-500">Keluar</button></form>
    </div>
</nav>
<main class="max-w-6xl mx-auto p-4">
    @if ($s)<x-sub-banner />@endif
    <x-flash />
    @yield('content')
</main>
</body>
</html>
