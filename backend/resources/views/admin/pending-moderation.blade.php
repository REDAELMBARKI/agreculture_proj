@extends('layouts.main')

@section('title', 'Pending Moderation - Admin')

@section('content')
<div class="flex min-h-screen bg-[var(--bgPrimary)]">
    <x-admin-sidebar />

    <main class="flex-1 p-8">
        <div class="mb-8 flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-bold text-[var(--textPrimary)]">Pending Moderation</h2>
                <p class="text-[var(--textSecondary)]">Review and approve or reject new listings. Total pending: {{ $totalPending }}</p>
            </div>
            <a href="{{ route('admin.dashboard') }}" class="text-[var(--primary)] font-bold hover:underline">Back to Dashboard</a>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-[var(--border)] overflow-hidden">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-[var(--bgSecondary)] text-[var(--textSecondary)] text-xs font-bold border-b border-[var(--border)] uppercase">
                        <th class="p-4">Listing</th>
                        <th class="p-4">Seller</th>
                        <th class="p-4">Date</th>
                        <th class="p-4">Price</th>
                        <th class="p-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[var(--border)]">
                    @forelse($items as $item)
                    <tr class="hover:bg-[var(--bgSecondary)] transition-colors">
                        <td class="p-4">
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 rounded-lg bg-[var(--bgTertiary)] overflow-hidden shrink-0">
                                    @if($item['image_url'])
                                    <img src="{{ $item['image_url'] }}" class="w-full h-full object-cover">
                                    @else
                                    <div class="w-full h-full flex items-center justify-center"><i class="ph ph-image text-xl text-[var(--textMuted)]"></i></div>
                                    @endif
                                </div>
                                <div>
                                    <div class="font-bold text-[var(--textPrimary)] text-sm">{{ $item['title'] }}</div>
                                    <div class="text-xs text-[var(--textSecondary)]">{{ $item['category_name'] ?? 'No Category' }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="p-4 text-sm text-[var(--textPrimary)]">{{ $item['seller_name'] }}</td>
                        <td class="p-4 text-sm text-[var(--textSecondary)]">{{ $item['created_at'] }}</td>
                        <td class="p-4 text-sm font-bold text-[var(--primary)]">{{ number_format($item['price'] ?? 0) }} MAD</td>
                        <td class="p-4 text-right">
                            <div class="flex justify-end gap-2">
                                <button class="px-3 py-1 bg-green-100 text-green-700 rounded-lg text-xs font-bold hover:bg-green-200 transition-colors">Approve</button>
                                <button class="px-3 py-1 bg-red-100 text-red-700 rounded-lg text-xs font-bold hover:bg-red-200 transition-colors">Reject</button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="p-12 text-center text-[var(--textSecondary)]">No pending items found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </main>
</div>
@endsection
