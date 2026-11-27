@php $waiting = \App\Models\SubscriptionPayment::where('status', 'waiting_verification')->count(); @endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Admin') - Webinyuu</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 text-gray-800">
<nav class="bg-gray-900 text-gray-100">
    <div class="max-w-6xl mx-auto px-4 h-14 flex items-center gap-4 text-sm overflow-x-auto whitespace-nowrap">
        <a href="{{ route('admin.dashboard') }}" class="font-bold">Webinyuu Admin</a>
        <a href="{{ route('admin.payments.index') }}">Pembayaran @if($waiting)<span class="ml-1 rounded-full bg-red-500 text-white text-xs px-2">{{ $waiting }}</span>@endif</a>
        <a href="{{ route('admin.sellers.index') }}">Seller</a>
        <a href="{{ route('admin.plans.index') }}">Paket</a>
        <a href="{{ route('admin.accounts.index') }}">Rekening</a>
        <form method="POST" action="{{ route('logout') }}" class="ml-auto">@csrf<button class="text-gray-400">Keluar</button></form>
    </div>
</nav>
<main class="max-w-6xl mx-auto p-4">
    <x-flash />
    @yield('content')
</main>
</body>
</html>
