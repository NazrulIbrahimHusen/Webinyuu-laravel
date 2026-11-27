@php
    $bg = ($store->bg_color && $store->hasFeature('bg_color')) ? $store->bg_color : '#f9fafb';
    $canCheckout = $store->hasFeature('checkout');
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', $store->name)</title>
    @hasSection('description')<meta name="description" content="@yield('description')">@endif
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="text-gray-800" style="background: {{ $bg }}">
<header class="bg-white border-b">
    <div class="max-w-5xl mx-auto px-4 py-3 flex items-center gap-3">
        <a href="{{ route('store.home', $store) }}" class="flex items-center gap-2 font-bold">
            @if ($store->logo_path)<img src="{{ asset('storage/'.$store->logo_path) }}" class="h-9 w-9 rounded object-cover" alt="">@endif
            {{ $store->name }}
        </a>
        <div class="ml-auto flex items-center gap-4 text-sm">
            @if ($store->whatsapp)<a href="{{ \App\Support\Phone::wa($store->whatsapp) }}" class="text-green-700">WhatsApp</a>@endif
            @if ($canCheckout)<a href="{{ route('store.cart', $store) }}">Keranjang ({{ $cartCount ?? 0 }})</a>@endif
        </div>
    </div>
</header>
<main class="max-w-5xl mx-auto p-4">
    <x-flash />
    @yield('content')
</main>
<footer class="text-center text-xs text-gray-500 py-6">Dibuat dengan Webinyuu Catalog</footer>
</body>
</html>
