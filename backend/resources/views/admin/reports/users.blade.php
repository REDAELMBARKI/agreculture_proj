@extends('layouts.main')

@section('title', 'Users Report - Admin')

@section('content')
<div class="flex min-h-screen bg-[var(--bgPrimary)]">
    <x-admin-sidebar />

    <main class="flex-1 p-8">
        <div class="mb-8 flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-bold text-[var(--textPrimary)]">Users Report</h2>
                <p class="text-[var(--textSecondary)]">Detailed list of registered users.</p>
            </div>
            <a href="{{ route('reports.all') }}" class="text-[var(--primary)] font-bold hover:underline">Back to Reports</a>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-[var(--border)] overflow-hidden">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-[var(--bgSecondary)] text-[var(--textSecondary)] text-xs font-bold border-b border-[var(--border)] uppercase">
                        <th class="p-4">ID</th>
                        <th class="p-4">Name</th>
                        <th class="p-4">Email</th>
                        <th class="p-4">Registered At</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[var(--border)]">
                    @forelse($data as $row)
                    <tr class="hover:bg-[var(--bgSecondary)] transition-colors">
                        <td class="p-4 text-sm text-[var(--textSecondary)]">#{{ $row['user_id'] }}</td>
                        <td class="p-4 font-bold text-[var(--textPrimary)] text-sm">{{ $row['name'] }}</td>
                        <td class="p-4 text-sm text-[var(--textPrimary)]">{{ $row['email'] }}</td>
                        <td class="p-4 text-sm text-[var(--textSecondary)]">{{ $row['registered_at'] }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="p-12 text-center text-[var(--textSecondary)]">No users found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </main>
</div>
@endsection
