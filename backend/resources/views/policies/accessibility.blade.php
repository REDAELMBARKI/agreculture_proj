@extends('layouts.main')

@section('title', 'Accessibility - LetUsDonate')

@section('content')
<div class="max-w-4xl mx-auto py-16 px-4">
    <h1 class="text-3xl font-bold mb-8 text-[var(--textPrimary)]">Accessibility Statement</h1>
    
    <div class="prose prose-slate max-w-none text-[var(--textSecondary)] leading-relaxed space-y-6">
        <p>LetUsDonate is committed to ensuring digital accessibility for people with disabilities. We are continually improving the user experience for everyone and applying the relevant accessibility standards.</p>

        <section>
            <h2 class="text-xl font-bold text-[var(--textPrimary)] mb-4">Our Standards</h2>
            <p>We aim to conform to the Web Content Accessibility Guidelines (WCAG) 2.1 level AA standards.</p>
        </section>

        <section>
            <h2 class="text-xl font-bold text-[var(--textPrimary)] mb-4">Feedback</h2>
            <p>We welcome your feedback on the accessibility of our website. Please let us know if you encounter accessibility barriers.</p>
        </section>
    </div>
</div>
@endsection
