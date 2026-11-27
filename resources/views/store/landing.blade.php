@extends('layouts.public')
@section('title', $page->meta_title ?: $store->name)
@if ($page->meta_description)@section('description', $page->meta_description)@endif
@section('content')
@if ($preview)<div class="mb-4 rounded bg-indigo-100 text-indigo-800 px-4 py-2 text-sm">Pratinjau landing page ({{ $page->status === 'published' ? 'sudah dipublikasikan' : 'masih draf, belum tampil publik' }}).</div>@endif
<div class="space-y-6">
    @forelse ($sections as $section)
        @include('store.landing._'.$section->type, ['c' => $section->content ?? []])
    @empty
        <p class="text-sm text-gray-500">Belum ada konten.</p>
    @endforelse
</div>
@endsection
