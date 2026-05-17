@extends('layouts.main')

@section('title', 'Admin Dashboard')

@section('content')
@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin.css') }}">
@endpush

<div class="admin-dashboard">
    <div class="admin-links">
        <h2>Welcome Admin!</h2>
        <ul>
            <li>
                <i class="ph ph-users"></i>
                <a href="{{ route('admin.users') }}">View Users</a>
            </li>
            <li>
                <i class="ph ph-package"></i>
                <a href="{{ route('admin.announcements') }}">Announcements</a>
            </li>
            <li>
                <i class="ph ph-chart-line"></i>
                <a href="{{ route('admin.reports') }}">Data Reports</a>
            </li>
            <li>
                <i class="ph ph-sign-out"></i>
                <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                    @csrf
                    <button type="submit" class="admin-button">Logout</button>
                </form>
            </li>
        </ul>
    </div>

    <div class="admin-overview">
        <div class="Stats">
            <div>
                <h4>Total Announcements</h4>
                <p>{{ $stats['total_announcements'] }}</p>
            </div>
            <div>
                <h4>Active Listings</h4>
                <p>{{ $stats['active_announcements'] }}</p>
            </div>
            <div>
                <h4>Pending Moderation</h4>
                <p class="{{ $stats['pending_moderation'] > 0 ? 'pending-warning' : '' }}">
                    {{ $stats['pending_moderation'] }}
                </p>
            </div>
            <div>
                <h4>New Users Today</h4>
                <p>{{ $stats['new_users_today'] }}</p>
            </div>
        </div>
    </div>

    <div class="dashboard-sections">
        <div class="two-col-grid">
            <div class="chart-card">
                <h3>Donation Trends</h3>
                <div class="chart-box">
                    <canvas id="donationTrendsChart"></canvas>
                </div>
            </div>

            <div class="chart-card">
                <h3>Monthly User Trends</h3>
                <div class="chart-box">
                    <canvas id="userTrendsChart"></canvas>
                </div>
            </div>
        </div>

        <div class="two-col-grid">
            <div class="chart-card">
                <h3>Donation vs Sale Split</h3>
                <div class="donut-layout">
                    <div class="donut-box">
                        <canvas id="typeSplitChart"></canvas>
                    </div>
                    <div class="chart-legend">
                        <div class="legend-item">
                            <span class="legend-dot" style="background: #22c55e"></span>
                            <span>Donations: {{ $typeSplit['donations'] }}</span>
                        </div>
                        <div class="legend-item">
                            <span class="legend-dot" style="background: #f59e0b"></span>
                            <span>Sales: {{ $typeSplit['sales'] }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="chart-card">
                <h3>New vs Returning Users</h3>
                <div class="donut-layout">
                    <div class="donut-box">
                        <canvas id="retentionChart"></canvas>
                    </div>
                    <div class="chart-legend">
                        <div class="legend-item">
                            <span class="legend-dot" style="background: #7c3aed"></span>
                            <span>New Users: {{ $retention['new_users'] }}</span>
                        </div>
                        <div class="legend-item">
                            <span class="legend-dot" style="background: #a78bfa"></span>
                            <span>Returning: {{ $retention['returning_users'] }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="two-col-grid">
            <div class="chart-card">
                <h3>Announcement Funnel</h3>
                <div class="funnel-wrap">
                    @foreach($funnelStages as $stage)
                        <div>
                            <div class="funnel-row-head">
                                <span>{{ $stage['label'] }}</span>
                                <span>{{ $stage['count'] }} ({{ $stage['percentage'] }}%)</span>
                            </div>
                            <div class="funnel-row-track">
                                <div class="funnel-row-fill" style="width: {{ $stage['percentage'] }}%; background-color: {{ $stage['color'] }};"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="chart-card">
                <h3>Top Categories</h3>
                <div class="chart-box">
                    <canvas id="categoriesChart"></canvas>
                </div>
            </div>
        </div>

        <div class="chart-card full-width-card">
            <h3>Peak Posting Hours</h3>
            <div class="hourly-grid">
                @foreach($hourlyActivity as $hour => $count)
                    @php
                        $intensity = $hourlyMax > 0 ? $count / $hourlyMax : 0;
                        $bg = "rgba(22, 163, 74, " . max(0.08, $intensity) . ")";
                    @endphp
                    <div class="hour-cell" style="background-color: {{ $bg }}" title="{{ $hour }}h: {{ $count }} posts"></div>
                @endforeach
            </div>
            <div class="hour-labels">
                <span>0h</span><span>4h</span><span>8h</span><span>12h</span><span>16h</span><span>20h</span>
            </div>
        </div>

        <div class="chart-card full-width-card">
            <h3>Pending Moderation Queue</h3>
            <div class="pending-list">
                @forelse($pendingModeration as $item)
                    <div class="pending-item">
                        <div class="pending-left">
                            <span class="{{ $item->listing_mode === 'donate' ? 'tag-donation' : 'tag-sale' }}">
                                {{ $item->listing_mode === 'donate' ? 'Donation' : 'Sale' }}
                            </span>
                            <div>
                                <p>{{ $item->title }}</p>
                                <p class="muted-text">{{ $item->city }}</p>
                            </div>
                        </div>
                        <span class="muted-text">{{ $item->created_at->diffForHumans() }}</span>
                    </div>
                @empty
                    <p>No announcements pending moderation.</p>
                @endforelse
            </div>
            <div class="pending-footer">
                <a href="{{ route('admin.announcements') }}">View all announcements -></a>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Donation Trends
    new Chart(document.getElementById('donationTrendsChart'), {
        type: 'line',
        data: {
            labels: @json(collect($stats['donation_trends'])->pluck('label')),
            datasets: [{
                label: 'Donations',
                data: @json(collect($stats['donation_trends'])->pluck('count')),
                borderColor: '#22c55e',
                backgroundColor: 'rgba(34, 197, 94, 0.25)',
                fill: true,
                tension: 0.4
            }]
        },
        options: { responsive: true, maintainAspectRatio: false }
    });

    // User Trends
    new Chart(document.getElementById('userTrendsChart'), {
        type: 'line',
        data: {
            labels: @json(collect($stats['user_trends'])->pluck('label')),
            datasets: [{
                label: 'New Users',
                data: @json(collect($stats['user_trends'])->pluck('count')),
                borderColor: '#3b82f6',
                backgroundColor: 'rgba(59, 130, 246, 0.25)',
                fill: true,
                tension: 0.4
            }]
        },
        options: { responsive: true, maintainAspectRatio: false }
    });

    // Type Split
    new Chart(document.getElementById('typeSplitChart'), {
        type: 'doughnut',
        data: {
            labels: ['Donations', 'Sales'],
            datasets: [{
                data: [{{ $typeSplit['donations'] }}, {{ $typeSplit['sales'] }}],
                backgroundColor: ['#22c55e', '#f59e0b'],
                borderWidth: 0
            }]
        },
        options: { responsive: true, maintainAspectRatio: false, cutout: '70%', plugins: { legend: { display: false } } }
    });

    // Retention
    new Chart(document.getElementById('retentionChart'), {
        type: 'doughnut',
        data: {
            labels: ['New Users', 'Returning'],
            datasets: [{
                data: [{{ $retention['new_users'] }}, {{ $retention['returning_users'] }}],
                backgroundColor: ['#7c3aed', '#a78bfa'],
                borderWidth: 0
            }]
        },
        options: { responsive: true, maintainAspectRatio: false, cutout: '70%', plugins: { legend: { display: false } } }
    });

    // Categories
    new Chart(document.getElementById('categoriesChart'), {
        type: 'bar',
        data: {
            labels: @json(collect($categories)->pluck('category')),
            datasets: [{
                label: 'Postings',
                data: @json(collect($categories)->pluck('count')),
                backgroundColor: '#3b82f6'
            }]
        },
        options: { 
            responsive: true, 
            maintainAspectRatio: false,
            indexAxis: 'y',
            plugins: { legend: { display: false } }
        }
    });
</script>
@endpush
@endsection
