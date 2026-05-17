@extends('layouts.main')

@section('title', 'Login - LetUsDonate')

@section('content')
@push('styles')
<link rel="stylesheet" href="{{ asset('css/sign_up_login.css') }}">
@endpush
<div class="min-h-[calc(100vh-80px)] flex items-center justify-center p-6 bg-[var(--bgPrimary)]">
    <div class="w-full max-w-[450px] bg-white rounded-3xl shadow-xl shadow-blue-500/5 p-10 border border-[var(--border)]">
        <div class="mb-10 text-center">
            <h2 class="text-3xl font-extrabold text-[var(--textPrimary)] mb-2">Welcome Back</h2>
            <p class="text-[var(--textSecondary)] font-medium">Sign in to your account</p>
        </div>

        @if($errors->any())
            <div class="mb-6 p-4 bg-red-50 border border-red-100 text-red-600 rounded-xl text-sm font-bold">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('login') }}" method="POST" class="space-y-6">
            @csrf
            <div class="space-y-2">
                <label class="block text-xs font-bold text-[var(--textSecondary)] uppercase tracking-wider ml-1">Email</label>
                <div class="relative group">
                    <i class="ph ph-envelope absolute left-4 top-1/2 -translate-y-1/2 text-xl text-[var(--textMuted)] group-focus-within:text-[var(--primary)] transition-colors"></i>
                    <input type="email" name="email" value="{{ old('email') }}" required placeholder="your@email.com" 
                           class="w-full pl-12 pr-4 py-3.5 rounded-2xl border border-[var(--border)] bg-[var(--bgSecondary)] text-[var(--textPrimary)] outline-none focus:border-[var(--primary)] transition-all">
                </div>
            </div>

            <div class="space-y-2">
                <div class="flex justify-between items-center px-1">
                    <label class="block text-xs font-bold text-[var(--textSecondary)] uppercase tracking-wider">Password</label>
                    <a href="#" class="text-[11px] font-bold text-[var(--primary)] hover:underline">Forgot password?</a>
                </div>
                <div class="relative group">
                    <i class="ph ph-lock absolute left-4 top-1/2 -translate-y-1/2 text-xl text-[var(--textMuted)] group-focus-within:text-[var(--primary)] transition-colors"></i>
                    <input type="password" name="password" required placeholder="••••••••" 
                           class="w-full pl-12 pr-4 py-3.5 rounded-2xl border border-[var(--border)] bg-[var(--bgSecondary)] text-[var(--textPrimary)] outline-none focus:border-[var(--primary)] transition-all">
                </div>
            </div>

            <div class="pt-4">
                <button type="submit" class="w-full py-4 bg-[var(--primary)] text-white rounded-2xl font-bold text-base hover:bg-[var(--primaryHover)] transition-all shadow-lg shadow-blue-500/20 active:scale-[0.98]">
                    Login
                </button>
            </div>

            <div class="pt-6 text-center border-t border-[var(--border)]">
                <p class="text-[var(--textSecondary)] text-sm font-medium">
                    Don't have an account? 
                    <a href="{{ route('register') }}" class="text-[var(--primary)] font-bold hover:underline">Sign up now</a>
                </p>
            </div>
        </form>
    </div>
</div>
@endsection
