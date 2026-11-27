@if (session('status'))
    <div class="mb-4 rounded bg-green-100 text-green-800 px-4 py-2 text-sm">{{ session('status') }}</div>
@endif
@if ($errors->any())
    <div class="mb-4 rounded bg-red-100 text-red-800 px-4 py-2 text-sm">
        <ul class="list-disc pl-5">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
@endif
