@extends('layouts.main')

@section('title', 'My Announcements')

@section('content')
@push('styles')
<link rel="stylesheet" href="{{ asset('css/user.css') }}">
<link rel="stylesheet" href="{{ asset('css/records.css') }}">
@endpush

<main class="my-announcements" style="background-color: var(--bgPrimary); min-height: 100vh; padding: 20px;">
    <div class="records-container">
        <div class="header-left">
            <h2>My Announcements</h2>
            <p class="subtitle">Manage your items for sale and donation</p>
        </div>

        <div class="return-right">
            <a href="{{ route('user.announcements.create') }}" class="post_btn" style="display: flex; align-items: center; gap: 8px;">
                <i class="ph ph-plus"></i>
                Post New Item
            </a>
        </div>
    </div>

    <div class="filter-bar" style="display: flex; gap: 15px; marginBottom: 20px; padding: 15px; background-color: var(--bgTertiary); border-radius: 12px;">
        <form action="{{ route('user.listings') }}" method="GET" x-data x-ref="filterForm" style="display: flex; gap: 15px;">
            <div class="filter-group">
                <label style="font-size: 14px; font-weight: 600; color: var(--textSecondary); marginBottom: 5px; display: block;">Listing Type</label>
                <select 
                    name="mode" 
                    class="status-filter" 
                    @change="$refs.filterForm.submit()"
                    style="padding: 8px 12px; border-radius: 6px; border: 1px solid var(--border); background-color: var(--bgSecondary); color: var(--textPrimary);"
                >
                    <option value="all" {{ request('mode') == 'all' ? 'selected' : '' }}>All Items</option>
                    <option value="sell" {{ request('mode') == 'sell' ? 'selected' : '' }}>For Sale</option>
                    <option value="donate" {{ request('mode') == 'donate' ? 'selected' : '' }}>For Donation</option>
                </select>
            </div>

            <div class="filter-group">
                <label style="font-size: 14px; font-weight: 600; color: var(--textSecondary); marginBottom: 5px; display: block;">Status</label>
                <select 
                    name="status" 
                    class="status-filter" 
                    @change="$refs.filterForm.submit()"
                    style="padding: 8px 12px; border-radius: 6px; border: 1px solid var(--border); background-color: var(--bgSecondary); color: var(--textPrimary);"
                >
                    <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>All statuses</option>
                    <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>Published</option>
                    <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="sold" {{ request('status') == 'sold' ? 'selected' : '' }}>Sold</option>
                    <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>Closed</option>
                </select>
            </div>
        </form>
    </div>

    <div class="table-container" style="background-color: var(--bgSecondary); border-radius: 12px; padding: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.02);">
        <table class="table" style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="text-align: left; border-bottom: 1px solid var(--border); color: var(--textSecondary); font-size: 13px;">
                    <th style="padding: 12px;">Product</th>
                    <th style="padding: 12px;">Type</th>
                    <th style="padding: 12px;">Price</th>
                    <th style="padding: 12px;">Stats</th>
                    <th style="padding: 12px;">Status</th>
                    <th style="padding: 12px;">Date</th>
                    <th style="padding: 12px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($listings as $p)
                <tr style="border-bottom: 1px solid var(--border);">
                    <td style="display: flex; align-items: center; gap: 12px; padding: 12px;">
                        <div class="product-img-wrapper">
                            @php
                                $thumbnail = $p->thumbnail;
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
                                <img src="{{ $imageUrl }}" alt="{{ $p->title }}" style="width: 60px; height: 60px; object-fit: cover; border-radius: 8px;">
                            @else
                                <div style="width: 60px; height: 60px; background-color: var(--bgTertiary); display: flex; align-items: center; justify-content: center; border-radius: 8px;">
                                    <i class="ph ph-package text-2xl text-[var(--textMuted)]"></i>
                                </div>
                            @endif
                        </div>
                        <div>
                            <div style="font-weight: 600; color: var(--textPrimary);">{{ $p->title }}</div>
                            <div style="font-size: 12px; color: var(--textSecondary);">{{ $p->condition }}</div>
                        </div>
                    </td>
                    <td style="padding: 12px;">
                        <span class="pill {{ $p->listing_mode }}" style="padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 600; background-color: {{ $p->listing_mode === 'sell' ? '#dcfce7' : '#fef3c7' }}; color: {{ $p->listing_mode === 'sell' ? '#15803d' : '#b45309' }};">
                            {{ $p->listing_mode === 'sell' ? 'Selling' : 'Donating' }}
                        </span>
                    </td>
                    <td style="padding: 12px;">
                        @if($p->listing_mode === 'sell')
                            <span style="font-weight: 700; color: var(--textPrimary);">{{ $p->price }} MAD</span>
                        @else
                            <span style="color: var(--textMuted);">Free</span>
                        @endif
                    </td>
                    <td style="padding: 12px;">
                        <div style="display: flex; gap: 12px; color: var(--textSecondary); font-size: 13px;">
                            <span title="Views" style="display: flex; align-items: center; gap: 4px;"><i class="ph ph-eye"></i> {{ $p->views_count }}</span>
                            <span title="Favorites" style="display: flex; align-items: center; gap: 4px;"><i class="ph ph-heart"></i> {{ $p->favorites_count }}</span>
                        </div>
                    </td>
                    <td style="padding: 12px;">
                        <span class="status-tag {{ $p->status }}" style="padding: 4px 8px; border-radius: 4px; font-size: 12px; text-transform: capitalize; background-color: var(--bgTertiary); color: var(--textSecondary);">
                            {{ $p->status }}
                        </span>
                    </td>
                    <td style="font-size: 13px; color: var(--textSecondary); padding: 12px;">
                        {{ $p->created_at->format('M d, Y') }}
                    </td>
                    <td style="padding: 12px;">
                        <div style="display: flex; gap: 8px;">
                            <a href="{{ route('marketplace.show', $p->slug) }}" class="action-btn" title="View details" style="padding: 6px; border: 1px solid var(--border); border-radius: 6px; background-color: var(--bgSecondary); cursor: pointer; color: inherit;">
                                <i class="ph ph-eye"></i>
                            </a>
                            <a href="{{ route('user.announcements.edit', $p->slug) }}" class="action-btn" title="Edit listing" style="padding: 6px; border: 1px solid var(--border); border-radius: 6px; background-color: var(--bgSecondary); cursor: pointer; color: inherit;">
                                <i class="ph ph-pencil-simple"></i>
                            </a>
                            <form action="{{ route('user.announcements.destroy', $p->slug) }}" method="POST" onsubmit="return confirm('Are you sure?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="action-btn delete" title="Delete listing" style="padding: 6px; border: 1px solid #fee2e2; border-radius: 6px; background-color: #fef2f2; cursor: pointer; color: #dc2626;">
                                    <i class="ph ph-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align: center; padding: 80px 20px; background-color: var(--bgTertiary); border-radius: 12px; border: 2px dashed var(--border);">
                        <i class="ph ph-package" style="font-size: 48px; color: var(--textMuted); marginBottom: 16px; display: block;"></i>
                        <h3 style="color: var(--textPrimary); marginBottom: 8px;">No announcements found</h3>
                        <p style="color: var(--textSecondary); marginBottom: 24px;">You haven't posted any items yet. Start sharing today!</p>
                        <a href="{{ route('user.announcements.create') }}" class="post_btn" style="padding: 10px 24px; border-radius: 8px; background-color: var(--primary); color: white; text-decoration: none;">
                            Publish your first announcement
                        </a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-6">
        {{ $listings->links() }}
    </div>
</main>
@endsection
