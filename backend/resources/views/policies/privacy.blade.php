@extends('layouts.main')

@section('title', 'Privacy Policy - LetUsDonate')

@section('content')
<div class="max-w-4xl mx-auto py-16 px-4">
    <h1 class="text-3xl font-bold mb-8 text-[var(--textPrimary)]">Privacy Policy</h1>
    
    <div class="prose prose-slate max-w-none text-[var(--textSecondary)] leading-relaxed space-y-6">
        <p>Last updated: {{ date('F d, Y') }}</p>

        <section>
            <h2 class="text-xl font-bold text-[var(--textPrimary)] mb-4">1. Information We Collect</h2>
            <p>We collect information you provide directly to us, such as when you create an account, post a listing, or communicate with other users.</p>
        </section>

        <section>
            <h2 class="text-xl font-bold text-[var(--textPrimary)] mb-4">2. How We Use Your Information</h2>
            <p>We use the information we collect to provide, maintain, and improve our services, and to connect donors with charities.</p>
        </section>

        <section>
            <h2 class="text-xl font-bold text-[var(--textPrimary)] mb-4">3. Data Security</h2>
            <p>We implement reasonable security measures to protect your personal information from unauthorized access or disclosure.</p>
        </section>
    </div>
</div>
@endsection
