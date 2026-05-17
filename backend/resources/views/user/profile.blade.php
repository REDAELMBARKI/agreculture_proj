@extends('layouts.main')

@section('title', 'My Profile - LetUsDonate')

@section('content')
@push('styles')
<link rel="stylesheet" href="{{ asset('css/user.css') }}">
@endpush
<div class="flex">
    <x-user-sidebar />

    <main class="flex-1 p-10 bg-[var(--bgPrimary)]">
        <header class="mb-10">
            <h1 class="text-3xl font-extrabold text-[var(--textPrimary)]">Account Settings</h1>
            <p class="text-[var(--textSecondary)] mt-2">Manage your personal information and preferences.</p>
        </header>

        <div class="max-w-2xl bg-white rounded-2xl shadow-sm border border-[var(--border)] overflow-hidden">
            <form action="{{ route('user.profile.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <div class="p-8 border-b border-[var(--border)] bg-[var(--bgSecondary)] flex items-center gap-8">
                    <div class="relative group">
                        <div class="w-32 h-32 rounded-full overflow-hidden bg-[var(--bgTertiary)] border-4 border-white shadow-md">
                            @if($user->avatar_path)
                                <img src="{{ asset('storage/'.$user->avatar_path) }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center bg-[var(--primary)] text-white text-4xl font-bold">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                            @endif
                        </div>
                        <label class="absolute inset-0 flex items-center justify-center bg-black/40 text-white rounded-full opacity-0 group-hover:opacity-100 transition-opacity cursor-pointer">
                            <i class="ph ph-camera text-2xl"></i>
                            <input type="file" name="avatar" class="hidden">
                        </label>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-[var(--textPrimary)]">{{ $user->name }}</h3>
                        <p class="text-[var(--textSecondary)]">{{ $user->email }}</p>
                        <div class="mt-2 flex gap-2">
                            <span class="px-3 py-1 bg-[var(--coralLight)] text-[var(--primary)] rounded-full text-xs font-bold">{{ $user->role->name ?? 'User' }}</span>
                            <span class="px-3 py-1 bg-[var(--verifiedBg)] text-[var(--success)] rounded-full text-xs font-bold">Verified Parent</span>
                        </div>
                    </div>
                </div>

                <div class="p-8 space-y-6">
                    <div>
                        <label class="block text-sm font-bold text-[var(--textPrimary)] mb-2">Full Name</label>
                        <input type="text" name="name" value="{{ $user->name }}" 
                               class="w-full p-3 rounded-xl border border-[var(--border)] bg-[var(--bgPrimary)] outline-none focus:border-[var(--primary)] transition-colors">
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-[var(--textPrimary)] mb-2">Email Address</label>
                        <input type="email" name="email" value="{{ $user->email }}" 
                               class="w-full p-3 rounded-xl border border-[var(--border)] bg-[var(--bgPrimary)] outline-none focus:border-[var(--primary)] transition-colors">
                    </div>

                    <div class="pt-6 border-t border-[var(--border)]">
                        <h4 class="font-bold text-[var(--textPrimary)] mb-4">Security</h4>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-semibold text-[var(--textSecondary)] mb-2">New Password (leave blank to keep current)</label>
                                <input type="password" name="password" 
                                       class="w-full p-3 rounded-xl border border-[var(--border)] bg-[var(--bgPrimary)] outline-none focus:border-[var(--primary)] transition-colors">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-[var(--textSecondary)] mb-2">Confirm New Password</label>
                                <input type="password" name="password_confirmation" 
                                       class="w-full p-3 rounded-xl border border-[var(--border)] bg-[var(--bgPrimary)] outline-none focus:border-[var(--primary)] transition-colors">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="p-8 bg-[var(--bgSecondary)] border-t border-[var(--border)] flex justify-end gap-4">
                    <button type="button" class="px-6 py-3 font-bold text-[var(--textSecondary)] hover:text-[var(--textPrimary)] transition-colors">Cancel</button>
                    <button type="submit" class="px-8 py-3 bg-[var(--primary)] text-white font-bold rounded-xl shadow-lg shadow-[var(--primary)]/20 hover:-translate-y-0.5 transition-all">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </main>
</div>
@endsection
