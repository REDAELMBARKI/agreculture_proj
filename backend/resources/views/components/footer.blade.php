<footer class="bg-[var(--bgSecondary)] border-t border-[var(--border)] py-12">
    <div class="max-w-[1320px] mx-auto px-6">
        <div class="flex flex-col md:flex-row justify-between items-center gap-8">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-[var(--primary)] text-white rounded-xl flex items-center justify-center">
                    <i class="ph-bold ph-leaf text-2xl"></i>
                </div>
                <span class="text-xl font-black text-[var(--textPrimary)]">LetUsDonate</span>
            </div>

            <div class="flex flex-wrap justify-center gap-x-6 gap-y-2 text-sm font-bold text-[var(--textSecondary)]">
                <a href="{{ route('terms') }}" class="hover:text-[var(--primary)] transition-colors">Terms and Conditions</a>
                <span class="opacity-20">/</span>
                <a href="{{ route('privacy') }}" class="hover:text-[var(--primary)] transition-colors">Privacy Policy</a>
                <span class="opacity-20">/</span>
                <a href="{{ route('accessibility') }}" class="hover:text-[var(--primary)] transition-colors">Accessibility</a>
                <span class="opacity-20">/</span>
                <a href="{{ route('cookies') }}" class="hover:text-[var(--primary)] transition-colors">Cookie Policy</a>
            </div>

            <div class="text-sm text-[var(--textMuted)] font-medium">
                &copy; {{ date('Y') }} LetUsDonate. All rights reserved.
            </div>
        </div>
    </div>
</footer>
