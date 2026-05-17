@extends('layouts.main')

@section('title', 'Terms and Conditions')

@section('content')
<div class="max-w-4xl mx-auto py-16 px-4">
    <h1 class="text-3xl font-bold mb-8 text-[var(--textPrimary)]">Terms and Conditions</h1>
    
    <div class="prose prose-slate max-w-none text-[var(--textSecondary)] leading-relaxed space-y-6">
        <p>Last updated: {{ date('F d, Y') }}</p>

        <section>
            <h2 class="text-xl font-bold text-[var(--textPrimary)] mb-4">1. Acceptance of Terms</h2>
            <p>By accessing and using this Marketplace, you agree to be bound by these Terms and Conditions and all applicable laws and regulations.</p>
        </section>

        <section>
            <h2 class="text-xl font-bold text-[var(--textPrimary)] mb-4">2. User Accounts</h2>
            <p>To use certain features of the platform, you must register for an account. You are responsible for maintaining the confidentiality of your account information.</p>
        </section>

        <section>
            <h2 class="text-xl font-bold text-[var(--textPrimary)] mb-4">3. Listing Guidelines</h2>
            <p>Users are responsible for the accuracy of their listings. Items must be described truthfully and categories must be selected appropriately.</p>
        </section>

        <section>
            <h2 class="text-xl font-bold text-[var(--textPrimary)] mb-4">4. Prohibited Items</h2>
            <p>The sale of illegal, stolen, or hazardous items is strictly prohibited on our platform.</p>
        </section>
    </div>
</div>
@endsection
