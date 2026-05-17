@extends('layouts.main')

@section('title', 'Announcements Management - Admin')

@section('content')
@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin.css') }}">
<link rel="stylesheet" href="{{ asset('css/records.css') }}">
@endpush
<div class="flex min-h-screen bg-[var(--bgPrimary)]" x-data="{ 
    search: '{{ request('search') }}',
    mode: '{{ request('mode') }}',
    status: '{{ request('status') }}',
    reset() {
        this.search = '';
        this.mode = '';
        this.status = '';
        this.apply();
    },
    apply() {
        const params = new URLSearchParams();
        if(this.search) params.append('search', this.search);
        if(this.mode) params.append('mode', this.mode);
        if(this.status) params.append('status', this.status);
        window.location.search = params.toString();
    }
}">
    <x-admin-sidebar />
    
    <main class="flex-1 p-8">
        <div class="flex justify-between items-center mb-8">
            <h2 class="text-2xl font-extrabold text-[var(--textPrimary)]">Announcements</h2>
            <a href="{{ route('admin.dashboard') }}" class="text-sm font-bold text-[var(--primary)] hover:underline">Return</a>
        </div>

        <!-- Filter Bar -->
        <div class="flex flex-wrap gap-4 mb-8 bg-white p-5 rounded-2xl border border-[var(--border)] shadow-sm">
            <input type="text" x-model="search" placeholder="Search by item, category, or donor ID..." 
                   class="flex-1 min-w-[300px] px-4 py-2.5 rounded-xl border border-[var(--border)] bg-[var(--bgPrimary)] text-sm outline-none focus:border-[var(--primary)]">
            
            <select x-model="mode" class="px-4 py-2.5 rounded-xl border border-[var(--border)] bg-[var(--bgPrimary)] text-sm outline-none">
                <option value="">Donate / Sell</option>
                <option value="donate">Donate</option>
                <option value="sell">Sell</option>
            </select>

            <select x-model="status" class="px-4 py-2.5 rounded-xl border border-[var(--border)] bg-[var(--bgPrimary)] text-sm outline-none">
                <option value="">All Statuses</option>
                <option value="pending">Pending</option>
                <option value="active">Active</option>
                <option value="sold">Sold</option>
                <option value="completed">Completed</option>
            </select>

            <button @click="apply" class="px-6 py-2.5 bg-[var(--primary)] text-white rounded-xl font-bold hover:bg-[var(--primaryHover)] transition-all">Apply</button>
            <button @click="reset" class="px-6 py-2.5 bg-[var(--bgTertiary)] text-[var(--textPrimary)] rounded-xl font-bold hover:bg-[var(--border)] transition-all">Reset</button>
        </div>

        <div class="bg-white rounded-3xl shadow-sm border border-[var(--border)] overflow-hidden">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-[var(--bgPrimary)] text-[var(--textSecondary)] text-[10px] font-bold uppercase tracking-wider">
                        <th class="px-6 py-4">ID</th>
                        <th class="px-6 py-4">Seller/Donor</th>
                        <th class="px-6 py-4">Item</th>
                        <th class="px-6 py-4">Category</th>
                        <th class="px-6 py-4">Image</th>
                        <th class="px-6 py-4">Date Posted</th>
                        <th class="px-6 py-4">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[var(--border)]">
                    @forelse($products as $product)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 text-xs font-bold text-[var(--textSecondary)]">#{{ $product->id }}</td>
                        <td class="px-6 py-4 text-sm font-bold text-[var(--textPrimary)]">{{ $product->user->name }}</td>
                        <td class="px-6 py-4 text-sm text-[var(--textPrimary)]">{{ $product->title }}</td>
                        <td class="px-6 py-4 text-sm text-[var(--textSecondary)]">{{ $product->category->label ?? 'N/A' }}</td>
                        <td class="px-6 py-4">
                            <div class="w-12 h-12 rounded-lg bg-gray-100 overflow-hidden">
                                @if($product->thumbnail)
                                    <img src="{{ $product->thumbnail->url }}" class="w-full h-full object-cover cursor-pointer hover:scale-110 transition-transform">
                                @else
                                    <div class="w-full h-full flex items-center justify-center opacity-20"><i class="ph ph-image"></i></div>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 text-xs text-[var(--textSecondary)]">{{ $product->created_at->format('M d, Y') }}</td>
                        <td class="px-6 py-4">
                            <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase {{ $product->status === 'active' ? 'bg-green-100 text-green-700' : ($product->status === 'pending' ? 'bg-yellow-100 text-yellow-700' : 'bg-gray-100 text-gray-700') }}">
                                {{ $product->status }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-[var(--textSecondary)]">No announcements found matching filters.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-8">
            {{ $products->appends(request()->query())->links() }}
        </div>
    </main>
</div>
@endsection
