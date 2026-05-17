@extends('layouts.main')

@section('title', 'Generate Reports - Admin')

@section('content')
@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin.css') }}">
<link rel="stylesheet" href="{{ asset('css/data_reports.css') }}">
@endpush
<div class="flex min-h-screen bg-[var(--bgPrimary)]">
    <x-admin-sidebar />
    
    <main class="flex-1 p-8">
        <div class="flex justify-between items-center mb-8">
            <h2 class="text-2xl font-extrabold text-[var(--textPrimary)]">Generate Reports</h2>
            <a href="{{ route('admin.dashboard') }}" class="text-sm font-bold text-[var(--primary)] hover:underline">Return</a>
        </div>

        <div class="bg-white rounded-3xl shadow-sm border border-[var(--border)] overflow-hidden">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-[var(--bgPrimary)] text-[var(--textSecondary)] text-[10px] font-bold uppercase tracking-wider">
                        <th class="px-8 py-4">Report Type</th>
                        <th class="px-8 py-4">Description</th>
                        <th class="px-8 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[var(--border)]">
                    @php
                        $reports = [
                            ['id' => 1, 'name' => 'Users Report', 'desc' => 'All registered users with base profile data.', 'route' => 'reports.users'],
                            ['id' => 2, 'name' => 'Top Users Report', 'desc' => 'Best sellers / best donaters by listing activity.', 'route' => 'reports.users'],
                            ['id' => 3, 'name' => 'User Activity Report', 'desc' => 'Historical activity of user logins and interactions.', 'route' => 'reports.users'],
                            ['id' => 4, 'name' => 'Location Report', 'desc' => 'Distribution of users by city and region.', 'route' => 'reports.users'],
                            ['id' => 5, 'name' => 'Sales Report', 'desc' => 'Completed marketplace transactions and revenue.', 'route' => 'reports.sales'],
                            ['id' => 6, 'name' => 'Donations Report', 'desc' => 'Daily breakdown of items donated for free.', 'route' => 'reports.sales'],
                            ['id' => 7, 'name' => 'Listings Performance', 'desc' => 'View counts and engagement metrics for all items.', 'route' => 'reports.sales'],
                            ['id' => 8, 'name' => 'Inventory Report', 'desc' => 'Current stock levels categorized by item type.', 'route' => 'reports.sales'],
                            ['id' => 9, 'name' => 'Time-Based Analysis', 'desc' => 'Peak posting hours and best days for activity.', 'route' => 'reports.sales'],
                        ];
                    @endphp

                    @foreach($reports as $report)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-8 py-5 text-sm font-bold text-[var(--textPrimary)]">
                            {{ $report['id'] }} - {{ $report['name'] }}
                        </td>
                        <td class="px-8 py-5 text-sm text-[var(--textSecondary)]">
                            {{ $report['desc'] }}
                        </td>
                        <td class="px-8 py-5 text-right">
                            <div class="flex justify-end gap-3">
                                <a href="{{ route($report['route']) }}" class="px-4 py-2 bg-[var(--bgTertiary)] text-[var(--textPrimary)] rounded-xl font-bold text-xs hover:bg-[var(--border)] transition-all">
                                    View Table
                                </a>
                                <button class="px-4 py-2 bg-[var(--primary)] text-white rounded-xl font-bold text-xs hover:bg-[var(--primaryHover)] transition-all">
                                    Download CSV
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-8 flex justify-end">
            <button class="px-8 py-4 bg-[var(--textPrimary)] text-white rounded-2xl font-bold hover:bg-black transition-all shadow-lg shadow-gray-200">
                Download All Reports (.zip)
            </button>
        </div>
    </main>
</div>
@endsection
