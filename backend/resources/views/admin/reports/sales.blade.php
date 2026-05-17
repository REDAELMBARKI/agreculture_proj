@extends('layouts.main')

@section('title', 'Sales Report - Admin')

@section('content')
<div class="flex min-h-screen bg-[var(--bgPrimary)]">
    <x-admin-sidebar />

    <main class="flex-1 p-8">
        <div class="mb-8 flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-bold text-[var(--textPrimary)]">Sales Report</h2>
                <p class="text-[var(--textSecondary)]">View all completed transactions.</p>
            </div>
            <a href="{{ route('reports.all') }}" class="text-[var(--primary)] font-bold hover:underline">Back to Reports</a>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-[var(--border)] overflow-hidden">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-[var(--bgSecondary)] text-[var(--textSecondary)] text-xs font-bold border-b border-[var(--border)] uppercase">
                        <th class="p-4">Product</th>
                        <th class="p-4">Seller</th>
                        <th class="p-4">Category</th>
                        <th class="p-4">Price</th>
                        <th class="p-4">Sold Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[var(--border)]">
                    @forelse($data as $row)
                    <tr class="hover:bg-[var(--bgSecondary)] transition-colors">
                        <td class="p-4">
                            <div class="font-bold text-[var(--textPrimary)] text-sm">{{ $row->title }}</div>
                        </td>
                        <td class="p-4 text-sm text-[var(--textPrimary)]">{{ $row->user->name }}</td>
                        <td class="p-4 text-sm text-[var(--textSecondary)]">{{ $row->category->name ?? 'N/A' }}</td>
                        <td class="p-4 text-sm font-bold text-[var(--primary)]">{{ number_format($row->price) }} MAD</td>
                        <td class="p-4 text-sm text-[var(--textSecondary)]">{{ $row->updated_at->format('M d, Y') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="p-12 text-center text-[var(--textSecondary)]">No sales found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </main>
</div>
@endsection
