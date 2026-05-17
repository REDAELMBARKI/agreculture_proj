@extends('layouts.main')

@section('title', 'Top Categories - Admin')

@section('content')
<div class="flex min-h-screen bg-[var(--bgPrimary)]">
    <x-admin-sidebar />
    <main class="flex-1 p-8">
        <h2 class="text-2xl font-bold text-[var(--textPrimary)] mb-8">Top Categories Analysis</h2>
        <div class="bg-white p-8 rounded-3xl shadow-sm border border-[var(--border)] max-w-2xl">
            <div class="space-y-4">
                @foreach($topCategories as $cat)
                <div class="flex items-center justify-between p-4 bg-[var(--bgSecondary)] rounded-xl">
                    <span class="font-bold text-[var(--textPrimary)]">{{ $cat['category'] }}</span>
                    <span class="px-4 py-1 bg-[var(--primary)] text-white rounded-lg font-bold">{{ $cat['count'] }} items</span>
                </div>
                @endforeach
            </div>
        </div>
    </main>
</div>
@endsection
