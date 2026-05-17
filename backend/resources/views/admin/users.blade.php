@extends('layouts.main')

@section('title', 'User Management - Admin')

@section('content')
@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin.css') }}">
<link rel="stylesheet" href="{{ asset('css/records.css') }}">
@endpush
<div class="flex min-h-screen bg-[var(--bgPrimary)]" x-data="{ 
    searchTerm: '{{ request('search') }}',
    roleFilter: '{{ request('role') }}',
    showEditModal: false,
    showDeleteModal: false,
    selectedUser: null,
    reset() {
        this.searchTerm = '';
        this.roleFilter = '';
        this.apply();
    },
    apply() {
        const params = new URLSearchParams();
        if(this.searchTerm) params.append('search', this.searchTerm);
        if(this.roleFilter) params.append('role', this.roleFilter);
        window.location.search = params.toString();
    },
    openEdit(user) {
        this.selectedUser = user;
        this.showEditModal = true;
    },
    openDelete(user) {
        this.selectedUser = user;
        this.showDeleteModal = true;
    }
}">
    <x-admin-sidebar />
    
    <main class="flex-1 p-8">
        <div class="flex justify-between items-center mb-8">
            <h2 class="text-2xl font-extrabold text-[var(--textPrimary)]">View Users</h2>
            <a href="{{ route('admin.dashboard') }}" class="text-sm font-bold text-[var(--primary)] hover:underline">Return</a>
        </div>

        <!-- Filter Bar -->
        <div class="flex flex-wrap gap-4 mb-8 bg-white p-5 rounded-2xl border border-[var(--border)] shadow-sm">
            <div class="flex-1 min-w-[300px] relative">
                <i class="ph ph-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-[var(--textMuted)]"></i>
                <input type="text" x-model="searchTerm" placeholder="Search by name or email..." 
                       class="w-full pl-11 pr-4 py-2.5 rounded-xl border border-[var(--border)] bg-[var(--bgPrimary)] text-sm outline-none focus:border-[var(--primary)]">
            </div>
            
            <select x-model="roleFilter" class="px-4 py-2.5 rounded-xl border border-[var(--border)] bg-[var(--bgPrimary)] text-sm outline-none">
                <option value="">All Roles</option>
                <option value="1">Admin</option>
                <option value="2">User</option>
            </select>

            <button @click="apply" class="px-6 py-2.5 bg-[var(--primary)] text-white rounded-xl font-bold hover:bg-[var(--primaryHover)] transition-all">Apply Filters</button>
            <button @click="reset" class="px-6 py-2.5 bg-[var(--bgTertiary)] text-[var(--textPrimary)] rounded-xl font-bold hover:bg-[var(--border)] transition-all">Reset</button>
        </div>

        <div class="bg-white rounded-3xl shadow-sm border border-[var(--border)] overflow-hidden">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-[var(--bgPrimary)] text-[var(--textSecondary)] text-[10px] font-bold uppercase tracking-wider">
                        <th class="px-8 py-4">User ID</th>
                        <th class="px-8 py-4">Name</th>
                        <th class="px-8 py-4">Email</th>
                        <th class="px-8 py-4">Role</th>
                        <th class="px-8 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[var(--border)]">
                    @forelse($users as $user)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-8 py-5 text-xs font-bold text-[var(--textSecondary)]">#{{ $user->id }}</td>
                        <td class="px-8 py-5">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-[var(--bgTertiary)] flex items-center justify-center text-xs font-bold text-[var(--primary)]">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                                <span class="text-sm font-bold text-[var(--textPrimary)]">{{ $user->name }}</span>
                            </div>
                        </td>
                        <td class="px-8 py-5 text-sm text-[var(--textPrimary)]">{{ $user->email }}</td>
                        <td class="px-8 py-5">
                            <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase {{ $user->roles->first()->id == 1 ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-700' }}">
                                {{ $user->roles->first()->name ?? 'User' }}
                            </span>
                        </td>
                        <td class="px-8 py-5 text-right">
                            <div class="flex justify-end gap-2">
                                <button @click="openEdit({{ json_encode($user) }})" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"><i class="ph ph-pencil-simple text-lg"></i></button>
                                @if($user->id !== auth()->id())
                                    <button @click="openDelete({{ json_encode($user) }})" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors"><i class="ph ph-trash text-lg"></i></button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-8 py-12 text-center text-[var(--textSecondary)]">No users found matching your search.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </main>

    <!-- Delete Modal -->
    <div x-show="showDeleteModal" class="fixed inset-0 z-[1000] flex items-center justify-center p-4 bg-black/75" x-cloak>
        <div class="bg-white rounded-3xl p-8 w-full max-w-md shadow-2xl" @click.away="showDeleteModal = false">
            <div class="w-16 h-16 bg-red-100 text-red-600 rounded-full flex items-center justify-center text-3xl mx-auto mb-6">
                <i class="ph ph-warning-octagon"></i>
            </div>
            <h2 class="text-2xl font-extrabold text-[var(--textPrimary)] text-center mb-2">Delete User?</h2>
            <p class="text-[var(--textSecondary)] text-center mb-8">Are you sure you want to delete <strong x-text="selectedUser?.name"></strong>? This action cannot be undone.</p>
            
            <form :action="'/admin/users/' + selectedUser?.id" method="POST">
                @csrf
                @method('DELETE')
                <div class="flex gap-4">
                    <button type="button" @click="showDeleteModal = false" class="flex-1 py-4 rounded-xl font-bold text-[var(--textSecondary)] hover:bg-gray-100 transition-all">Cancel</button>
                    <button type="submit" class="flex-1 py-4 bg-red-600 text-white rounded-xl font-bold hover:bg-red-700 transition-all shadow-lg shadow-red-500/20">Delete User</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Modal -->
    <div x-show="showEditModal" class="fixed inset-0 z-[1000] flex items-center justify-center p-4 bg-black/75" x-cloak>
        <div class="bg-white rounded-3xl p-8 w-full max-w-md shadow-2xl" @click.away="showEditModal = false">
            <h2 class="text-2xl font-extrabold text-[var(--textPrimary)] mb-2">Edit User Role</h2>
            <p class="text-[var(--textSecondary)] mb-8">Update permissions for <strong x-text="selectedUser?.name"></strong></p>
            
            <form :action="'/admin/users/' + selectedUser?.id" method="POST">
                @csrf
                @method('PUT')
                <div class="space-y-4 mb-8">
                    <div>
                        <label class="block text-xs font-bold text-[var(--textSecondary)] uppercase tracking-wider mb-2">Assigned Role</label>
                        <select name="role_id" class="w-full px-4 py-3 rounded-xl border border-[var(--border)] bg-[var(--bgPrimary)] text-sm outline-none focus:border-[var(--primary)]">
                            <option value="2">User / Donor</option>
                            <option value="1">Administrator</option>
                            <option value="3">Charity / Foundation</option>
                        </select>
                    </div>
                </div>
                <div class="flex gap-4">
                    <button type="button" @click="showEditModal = false" class="flex-1 py-4 rounded-xl font-bold text-[var(--textSecondary)] hover:bg-gray-100 transition-all">Cancel</button>
                    <button type="submit" class="flex-1 py-4 bg-[var(--primary)] text-white rounded-xl font-bold hover:bg-[var(--primaryHover)] transition-all">Save Changes</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
