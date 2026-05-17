@extends('layouts.main')

@section('title', 'User Dashboard')

@section('content')
@push('styles')
<link rel="stylesheet" href="{{ asset('css/user.css') }}">
<link rel="stylesheet" href="{{ asset('css/impact-dashboard.css') }}">
<link rel="stylesheet" href="{{ asset('css/my_impact.css') }}">
@endpush

<div class="user-dashboard-container">
    <div class="dashboard-left">
        <div class="dashboard">
            <x-user-sidebar />

            <main class="dashboard-main">
                <h2>Welcome, {{ explode(' ', $user->name)[0] }}</h2>
                
                {{-- My Impact View equivalent --}}
                <div class="impact-root">
                    <div class="impact-metrics-grid">
                        <div class="impact-metric-card">
                            <div class="impact-metric-card__icon"><i class="ph ph-heart"></i></div>
                            <p class="impact-metric-card__label">Total Donated</p>
                            <h3 class="impact-metric-card__value">{{ $stats['total_donated'] }}</h3>
                        </div>
                        <div class="impact-metric-card">
                            <div class="impact-metric-card__icon"><i class="ph ph-shopping-cart"></i></div>
                            <p class="impact-metric-card__label">Total Sold</p>
                            <h3 class="impact-metric-card__value">{{ $stats['total_sold'] }}</h3>
                        </div>
                        <div class="impact-metric-card">
                            <div class="impact-metric-card__icon"><i class="ph ph-eye"></i></div>
                            <p class="impact-metric-card__label">Total Views</p>
                            <h3 class="impact-metric-card__value">{{ $stats['total_views'] }}</h3>
                        </div>
                        <div class="impact-metric-card">
                            <div class="impact-metric-card__icon"><i class="ph ph-cursor-click"></i></div>
                            <p class="impact-metric-card__label">Total Clicks</p>
                            <h3 class="impact-metric-card__value">{{ $stats['total_clicks'] }}</h3>
                        </div>
                    </div>

                    <div class="impact-grid-2" style="margin-top: 2rem;">
                        <div class="impact-card">
                            <h3 class="impact-card__title">Activity (Last 30 Days)</h3>
                            <div class="impact-chart-wrap">
                                <canvas id="activityChart"></canvas>
                            </div>
                        </div>
                        <div class="impact-card">
                            <h3 class="impact-card__title">Engagement Overview</h3>
                            <div class="impact-chart-wrap">
                                <canvas id="engagementChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <div class="dashboard-right">
        <div class="new-donation">
            <h3>Your marketplace</h3>
            <p>
                Post anything you want to sell or give away. Buyers and donors connect with you directly by phone to
                arrange pickup or delivery.
            </p>
            <p>
                {{ count($foundations ?? []) }} verified foundations you can support when you donate.
            </p>
            <a href="{{ route('user.announcements.create') }}" class="cta-link">
                Add announcement
            </a>
        </div>
    </div>
</div>

<div class="donation-history full-width">
    <div class="recent-toolbar">
        <h3>Recent announcements &amp; donations</h3>
        <form action="{{ route('user.dashboard') }}" method="GET" x-data x-ref="filterForm">
            <label class="recent-filter-label">
                <span>Show</span>
                <select
                    name="filter"
                    class="recent-filter-select"
                    @change="$refs.filterForm.submit()"
                >
                    <option value="all" {{ request('filter') == 'all' ? 'selected' : '' }}>All listings</option>
                    <option value="donations" {{ request('filter') == 'donations' ? 'selected' : '' }}>Donations only</option>
                    <option value="sale" {{ request('filter') == 'sale' ? 'selected' : '' }}>For sale only</option>
                </select>
            </label>
        </form>
    </div>

    <table>
        <thead>
            <tr>
                <th>Type</th>
                <th>Title</th>
                <th>Price</th>
                <th>Image</th>
                <th>Date</th>
                <th>Status</th>
                <th>Phone</th>
            </tr>
        </thead>

        <tbody>
            @forelse($announcements as $ann)
                <tr>
                    <td>{{ $ann->listing_mode == 'sell' ? 'Sale' : 'Donation' }}</td>
                    <td>{{ $ann->title }}</td>
                    <td>{{ $ann->price ? $ann->price . ' MAD' : 'Free' }}</td>
                    <td>
                        @php
                            $thumbnail = $ann->thumbnail;
                            $imageUrl = null;
                            if ($thumbnail) {
                                if (str_starts_with($thumbnail->url ?? '', 'http')) {
                                    $imageUrl = $thumbnail->url;
                                } else {
                                    $path = $thumbnail->file_path ?? $thumbnail->path;
                                    if ($path) {
                                        $cleanPath = ltrim(str_replace('public/', '', $path), '/');
                                        $imageUrl = asset('storage/' . $cleanPath);
                                    }
                                }
                            }
                        @endphp
                        @if($imageUrl)
                            <img src="{{ $imageUrl }}" alt="" style="width: 50px; border-radius: 4px;">
                        @else
                            —
                        @endif
                    </td>
                    <td>{{ $ann->created_at->format('M d, Y') }}</td>
                    <td>{{ ucfirst($ann->status) }}</td>
                    <td>{{ $ann->contact_phone ?? '—' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7">Nothing to show for this filter yet.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@push('scripts')
<script>
    // Activity Chart
    const activityCtx = document.getElementById('activityChart');
    new Chart(activityCtx, {
        type: 'line',
        data: {
            labels: @json(collect($activity)->pluck('date')),
            datasets: [
                {
                    label: 'Donations',
                    data: @json(collect($activity)->pluck('donations')),
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    tension: 0.4,
                    fill: true
                },
                {
                    label: 'Announcements',
                    data: @json(collect($activity)->pluck('announcements')),
                    borderColor: '#ea580c',
                    backgroundColor: 'rgba(234, 88, 12, 0.1)',
                    tension: 0.4,
                    fill: true
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { position: 'bottom' } },
            scales: {
                y: { beginAtZero: true },
                x: { grid: { display: false } }
            }
        }
    });

    // Engagement Chart
    const engagementCtx = document.getElementById('engagementChart');
    new Chart(engagementCtx, {
        type: 'doughnut',
        data: {
            labels: ['Views', 'Clicks'],
            datasets: [{
                data: [{{ $stats['total_views'] }}, {{ $stats['total_clicks'] }}],
                backgroundColor: ['#e2e8f0', '#ea580c'],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '80%',
            plugins: { legend: { display: false } }
        }
    });
</script>
@endpush
@endsection
