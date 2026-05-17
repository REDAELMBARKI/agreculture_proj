@extends('layouts.main')

@section('title', 'FAQ')

@section('content')
@push('styles')
<link rel="stylesheet" href="{{ asset('css/faq.css') }}">
@endpush
<div class="faq redesign">
    <h1 class="text-4xl font-bold text-[var(--textPrimary)] mb-12 text-center">Frequently Asked Questions</h1>

    <!-- ChatBot Placeholder -->
    <div class="mb-16 bg-[var(--bgSecondary)] p-8 rounded-3xl border border-[var(--border)] text-center">
        <h3 class="text-xl font-bold text-[var(--textPrimary)] mb-2">Need quick answers?</h3>
        <p class="text-[var(--textSecondary)] mb-6">Our AI assistant is here to help you with any questions.</p>
        <a href="{{ route('faq.chat') }}" class="inline-flex items-center gap-2 px-8 py-3 bg-[var(--primary)] text-white rounded-xl font-bold hover:bg-[var(--primaryHover)] transition-all">
            <i class="ph ph-chat-centered-dots"></i>
            Chat with FAQ Bot
        </a>
    </div>

    <div class="space-y-12">
        <div class="bg-white p-8 rounded-3xl shadow-sm border border-[var(--border)]">
            <h4 class="text-xl font-bold text-[var(--textPrimary)] mb-4">What is our Marketplace?</h4>
            <p class="text-[var(--textSecondary)] leading-relaxed">
                Our Marketplace is a platform where users can buy and sell items. 
                Whether you're looking to find great deals or sell unwanted items, our marketplace connects you with local buyers and sellers.
            </p>
        </div>

        <div class="bg-white p-8 rounded-3xl shadow-sm border border-[var(--border)]">
            <h4 class="text-xl font-bold text-[var(--textPrimary)] mb-4">How do I create an announcement?</h4>
            <div class="space-y-4 text-[var(--textSecondary)]">
                <p class="font-semibold text-[var(--textPrimary)]">Follow these simple steps:</p>
                <ul class="space-y-2">
                    <li class="flex gap-3"><span>1:</span> Login or create a free account</li>
                    <li class="flex gap-3"><span>2:</span> Click "Add Announcement" and fill in your item details</li>
                    <li class="flex gap-3"><span>3:</span> Add photos and set your price</li>
                    <li class="flex gap-3"><span>4:</span> Choose pickup location and contact preferences</li>
                    <li class="flex gap-3"><span>5:</span> Publish your announcement</li>
                </ul>
            </div>
        </div>

        <div class="bg-white p-8 rounded-3xl shadow-sm border border-[var(--border)]">
            <h4 class="text-xl font-bold text-[var(--textPrimary)] mb-4">Is it safe to use?</h4>
            <p class="text-[var(--textSecondary)] leading-relaxed">
                Yes! We prioritize user safety with features like user ratings, secure messaging, 
                and verification options. Always meet in safe public locations and inspect items 
                before completing transactions.
            </p>
        </div>
    </div>
</div>
@endsection
