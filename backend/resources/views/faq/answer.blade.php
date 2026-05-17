@extends('layouts.main')

@section('title', 'FAQ Answer - LetUsDonate')

@section('content')
<div class="max-w-4xl mx-auto p-10">
    <div class="bg-white p-8 rounded-3xl shadow-sm border border-[var(--border)]">
        <h2 class="text-2xl font-bold text-[var(--textPrimary)] mb-6">AI Assistant Response</h2>
        
        <div class="prose prose-sm max-w-none text-[var(--textSecondary)] leading-relaxed">
            {!! nl2br(e($answer)) !!}
        </div>

        <div class="mt-10 pt-6 border-t border-[var(--border)] flex justify-between items-center">
            <p class="text-xs text-[var(--textMuted)]">Powered by OpenAI GPT-3.5</p>
            <a href="{{ route('faq') }}" class="px-6 py-2 bg-[var(--bgTertiary)] text-[var(--textPrimary)] rounded-xl font-bold hover:bg-[var(--border)] transition-all">
                Ask another question
            </a>
        </div>
    </div>
</div>
@endsection
