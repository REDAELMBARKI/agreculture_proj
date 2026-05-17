@extends('layouts.main')

@section('title', 'Listing Funnel - Admin')

@section('content')
<div class="flex min-h-screen bg-[var(--bgPrimary)]">
    <x-admin-sidebar />
    <main class="flex-1 p-8">
        <h2 class="text-2xl font-bold text-[var(--textPrimary)] mb-8">Listing Funnel Analysis</h2>
        <div class="bg-white p-8 rounded-3xl shadow-sm border border-[var(--border)] max-w-2xl">
            <div class="h-[400px]">
                <canvas id="funnelChartPage"></canvas>
            </div>
        </div>
    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const funnelCtx = document.getElementById('funnelChartPage');
    new Chart(funnelCtx, {
        type: 'bar',
        indexAxis: 'y',
        data: {
            labels: ['Posted', 'Active', 'Contacted', 'Closed'],
            datasets: [{
                label: 'Listings',
                data: [@json($funnel['posted']), @json($funnel['active']), @json($funnel['contacted']), @json($funnel['closed'])],
                backgroundColor: ['#6366f1', '#10b981', '#f59e0b', '#ef4444'],
                borderRadius: 8
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
