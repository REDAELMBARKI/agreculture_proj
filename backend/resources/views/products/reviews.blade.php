<div class="reviews-list flex flex-col gap-5">
    @forelse($reviews as $review)
        <div class="bg-[var(--bgSecondary)] p-5 rounded-xl shadow-sm">
            <div class="flex items-center justify-between mb-2.5">
                <div class="flex items-center gap-2.5">
                    <div class="font-semibold text-[var(--textPrimary)]">{{ $review->user->name ?? 'Anonymous' }}</div>
                    <div class="text-xs text-[var(--textMuted)]">{{ $review->created_at->format('M d, Y') }}</div>
                </div>
                <div class="flex gap-0.5">
                    @foreach(range(1, 5) as $i)
                        <i class="ph-fill ph-star text-sm {{ $i <= $review->rating ? 'text-[var(--warning)]' : 'text-[var(--textMuted)]' }}"></i>
                    @endforeach
                </div>
            </div>
            <p class="text-[var(--textSecondary)] leading-relaxed m-0">{{ $review->comment }}</p>
        </div>
    @empty
        <p class="text-[var(--textSecondary)] italic">No reviews yet.</p>
    @endforelse
</div>
