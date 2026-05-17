@extends('layouts.main')

@section('title', 'Hourly Activity - Admin')

@section('content')
<div class="flex min-h-screen bg-[var(--bgPrimary)]">
    <x-admin-sidebar />
    <main class="flex-1 p-8">
        <h2 class="text-2xl font-bold text-[var(--textPrimary)] mb-8">Hourly Activity Analysis</h2>
        <div class="bg-white p-8 rounded-3xl shadow-sm border border-[var(--border)]">
            <div class="h-[400px]">
                <canvas id="hourlyActivityChart"></canvas>
            </div>
        </div>
    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const hourlyCtx = document.getElementById('hourlyActivityChart');
    new Chart(hourlyCtx, {
        type: 'line',
        data: {
            labels: Array.from({length: 24}, (_, i) => i + ':00'),
            datasets: [{
                label: 'Activity',
                data: @json($hourlyActivity),
                borderColor: '#6366f1',
                tension: 0.4,
                fill: true,
                backgroundColor: 'rgba(99, 102, 241, 0.1)'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } }
        }
    });
</script>
@endsection
