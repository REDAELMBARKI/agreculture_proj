@extends('layouts.main')

@section('title', 'Dashboard Stats - Admin')

@section('content')
<div class="flex min-h-screen bg-[var(--bgPrimary)]">
    <x-admin-sidebar />
    <main class="flex-1 p-8">
        <h2 class="text-2xl font-bold text-[var(--textPrimary)] mb-8">Detailed Dashboard Statistics</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            @foreach($stats as $key => $value)
                @if(!is_array($value))
                <div class="bg-white p-6 rounded-2xl border border-[var(--border)] shadow-sm">
                    <div class="text-[var(--textSecondary)] text-sm font-bold uppercase mb-2">{{ str_replace('_', ' ', $key) }}</div>
                    <div class="text-3xl font-extrabold text-[var(--primary)]">{{ $value }}</div>
                </div>
                @endif
            @endforeach
        </div>
    </main>
</div>
@endsection
