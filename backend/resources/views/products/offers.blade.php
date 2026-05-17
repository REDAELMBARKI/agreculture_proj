<div class="offers-list flex flex-col gap-5">
    @forelse($offers as $offer)
        <div class="bg-[var(--bgSecondary)] p-5 rounded-xl shadow-sm">
            <div class="flex items-center justify-between mb-2.5">
                <div class="font-semibold text-[var(--textPrimary)]">{{ $offer->user->name ?? 'Anonymous' }}</div>
                <div class="text-xs text-[var(--textMuted)]">{{ $offer->created_at->format('M d, Y') }}</div>
            </div>
            <div class="text-lg font-bold text-[var(--primary)]">{{ number_format($offer->amount) }} MAD</div>
            @if($offer->message)
                <p class="text-[var(--textSecondary)] text-sm mt-2">{{ $offer->message }}</p>
            @endif
        </div>
    @empty
        <p class="text-[var(--textSecondary)] italic">No offers yet.</p>
    @endforelse
</div>
