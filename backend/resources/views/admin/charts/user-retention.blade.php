@extends('layouts.main')

@section('title', 'User Retention - Admin')

@section('content')
<div class="flex min-h-screen bg-[var(--bgPrimary)]">
    <x-admin-sidebar />
    <main class="flex-1 p-8">
        <h2 class="text-2xl font-bold text-[var(--textPrimary)] mb-8">User Retention Analysis</h2>
        <div class="bg-white p-8 rounded-3xl shadow-sm border border-[var(--border)] max-w-md">
            <div class="h-[300px]">
                <canvas id="retentionChart"></canvas>
            </div>
            <div class="mt-8 grid grid-cols-2 gap-4">
                <div class="text-center">
                    <div class="text-xs text-[var(--textSecondary)] uppercase font-bold">Returning Users</div>
                    <div class="text-2xl font-bold text-[var(--primary)]">{{ $userRetention['returning_users_percent'] }}%</div>
                </div>
                <div class="text-center">
                    <div class="text-xs text-[var(--textSecondary)] uppercase font-bold">New Users</div>
                    <div class="text-2xl font-bold text-[var(--textPrimary)]">{{ $userRetention['new_users_percent'] }}%</div>
                </div>
            </div>
        </div>
    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const retentionCtx = document.getElementById('retentionChart');
    new Chart(retentionCtx, {
        type: 'doughnut',
        data: {
            labels: ['Returning', 'New'],
            datasets: [{
                data: [@json($userRetention['returning_users_percent']), @json($userRetention['new_users_percent'])],
                backgroundColor: ['#6366f1', '#e2e8f0']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });
</script>
@endsection
