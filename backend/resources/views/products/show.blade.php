@extends('layouts.main')

@section('title', $product->title . ' - Marketplace')

@section('content')
<div x-data="{ 
    activeImage: '{{ $product->thumbnail?->url ?? 'https://via.placeholder.com/600x400' }}',
    showOfferModal: false,
    rating: 5,
    comment: '',
    isFavorited: false,
    async toggleFavorite() {
        this.isFavorited = !this.isFavorited;
        try {
            await fetch(`/api/announcements/{{ $product->slug }}/favorite`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify({ favorite: this.isFavorited })
            });
        } catch (e) { console.error(e); }
    }
}" class="max-w-[1200px] mx-auto p-5 bg-[var(--bgPrimary)]">

    <!-- Back Button -->
    <button onclick="window.history.back()" class="flex items-center gap-2 bg-none border-none text-[var(--primary)] font-bold cursor-pointer mb-5 hover:underline">
        <i class="ph ph-caret-left text-xl"></i>
        Back to results
    </button>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">
        <!-- Left: Images -->
        <div class="space-y-4">
            <div class="w-full aspect-square rounded-2xl overflow-hidden bg-[var(--bgTertiary)] border border-[var(--border)] flex items-center justify-center">
                <img :src="activeImage" class="w-full h-full object-contain">
            </div>
            
            <div class="flex gap-2.5 overflow-x-auto pb-2.5 no-scrollbar">
                <button @click="activeImage = '{{ $product->thumbnail?->url }}'" 
                        class="w-20 h-20 rounded-lg border-2 shrink-0 overflow-hidden bg-white p-0.5 transition-all"
                        :class="activeImage === '{{ $product->thumbnail?->url }}' ? 'border-[var(--primary)]' : 'border-[var(--border)]'">
                    <img src="{{ $product->thumbnail?->url }}" class="w-full h-full object-cover rounded-md">
                </button>
                @foreach($product->gallery as $img)
                    <button @click="activeImage = '{{ $img->url }}'" 
                            class="w-20 h-20 rounded-lg border-2 shrink-0 overflow-hidden bg-white p-0.5 transition-all"
                            :class="activeImage === '{{ $img->url }}' ? 'border-[var(--primary)]' : 'border-[var(--border)]'">
                        <img src="{{ $img->url }}" class="w-full h-full object-cover rounded-md">
                    </button>
                @endforeach
            </div>
        </div>

        <!-- Right: Info -->
        <div class="flex flex-col">
            <div class="flex justify-between items-start">
                <span class="px-3 py-1 rounded-full text-[12px] font-bold uppercase tracking-wider text-white"
                      style="background-color: {{ $product->listing_mode === 'sell' ? 'var(--primary)' : 'var(--success)' }}">
                    {{ $product->listing_mode === 'sell' ? 'For Sale' : 'Free / Donation' }}
                </span>
                
                <div class="flex gap-4">
                    <button class="bg-none border-none text-[var(--textSecondary)] cursor-pointer hover:text-[var(--textPrimary)]"><i class="ph ph-share-network text-2xl"></i></button>
                    <button @click="toggleFavorite" class="bg-none border-none cursor-pointer transition-colors"
                            :class="isFavorited ? 'text-red-500' : 'text-[var(--textSecondary)]'">
                        <i class="ph-fill ph-heart text-2xl" x-show="isFavorited"></i>
                        <i class="ph ph-heart text-2xl" x-show="!isFavorited"></i>
                    </button>
                </div>
            </div>

            <h1 class="text-4xl font-extrabold text-[var(--textPrimary)] mt-4 mb-2">
                {{ $product->title }}
            </h1>

            <div class="flex items-center gap-4 text-[var(--textSecondary)] text-sm mb-6">
                <div class="flex items-center gap-1.5"><i class="ph ph-map-pin text-lg text-coral"></i> {{ $product->city->label ?? 'Agadir, Morocco' }}</div>
                <div class="flex items-center gap-1.5"><i class="ph ph-clock text-lg"></i> Posted {{ $product->created_at->format('M d, Y') }}</div>
            </div>

            <div class="text-4xl font-extrabold text-[var(--textPrimary)] mb-8">
                {{ $product->listing_mode === 'sell' ? number_format($product->price) . ' MAD' : 'FREE' }}
                @if($product->price_negotiable)
                    <span class="text-sm font-medium text-[var(--textSecondary)] ml-2.5">(Negotiable)</span>
                @endif
            </div>

            <div class="grid grid-cols-2 gap-4 mb-8">
                <form action="{{ route('chat.start', $product->slug) }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full py-4 bg-[var(--primary)] text-white border-none rounded-xl font-bold text-base flex items-center justify-center gap-2.5 cursor-pointer hover:bg-[var(--primaryHover)] transition-all shadow-lg shadow-blue-500/20">
                        <i class="ph ph-chat-circle text-xl"></i>
                        Chat with Seller
                    </button>
                </form>
                <button @click="showOfferModal = true" class="w-full py-4 bg-white text-[var(--primary)] border-2 border-[var(--primary)] rounded-xl font-bold text-base cursor-pointer hover:bg-blue-50 transition-all">
                    Make Offer
                </button>
            </div>

            <!-- Details Table -->
            <div class="bg-[var(--bgTertiary)] p-5 rounded-2xl mb-8">
                <h3 class="text-lg font-bold mb-4 text-[var(--textPrimary)]">Product Details</h3>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <div class="text-[var(--textSecondary)] text-[11px] font-bold uppercase tracking-wider mb-1">Condition</div>
                        <div class="font-bold text-[var(--textPrimary)] text-sm">{{ ucfirst(str_replace('_', ' ', $product->condition)) }}</div>
                    </div>
                    <div>
                        <div class="text-[var(--textSecondary)] text-[11px] font-bold uppercase tracking-wider mb-1">Age Recommended</div>
                        <div class="font-bold text-[var(--textPrimary)] text-sm">{{ $product->age_range }}</div>
                    </div>
                    <div>
                        <div class="text-[var(--textSecondary)] text-[11px] font-bold uppercase tracking-wider mb-1">Brand</div>
                        <div class="font-bold text-[var(--textPrimary)] text-sm">{{ $product->brand ?: 'No brand' }}</div>
                    </div>
                    <div>
                        <div class="text-[var(--textSecondary)] text-[11px] font-bold uppercase tracking-wider mb-1">Gender</div>
                        <div class="font-bold text-[var(--textPrimary)] text-sm">{{ ucfirst($product->gender ?: 'Unisexe') }}</div>
                    </div>
                </div>
            </div>

            <div class="description">
                <h3 class="text-lg font-bold mb-2.5 text-[var(--textPrimary)]">Description</h3>
                <p class="text-[var(--textSecondary)] leading-relaxed whitespace-pre-line">
                    {{ $product->description ?: 'No description provided.' }}
                </p>
            </div>
        </div>
    </div>

    <!-- Seller Section -->
    <div class="mt-12 pt-8 border-t border-[var(--border)] flex items-center justify-between">
        <div class="flex items-center gap-5">
            <div class="w-16 h-16 rounded-full bg-[var(--bgTertiary)] overflow-hidden flex items-center justify-center">
                @if($product->user->avatar_path)
                    <img src="{{ Storage::url($product->user->avatar_path) }}" class="w-full h-full object-cover">
                @else
                    <i class="ph ph-user text-3xl text-[var(--textMuted)]"></i>
                @endif
            </div>
            <div>
                <div class="text-xl font-bold text-[var(--textPrimary)]">{{ $product->user->name }}</div>
                <div class="flex items-center gap-1.5 text-[var(--warning)] text-sm">
                    <i class="ph-fill ph-star"></i>
                    <span class="font-bold">4.8</span>
                    <span class="text-[var(--textSecondary)] font-normal">(24 reviews)</span>
                </div>
            </div>
        </div>
        
        <a href="{{ route('profile.show', $product->user->id) }}" class="px-5 py-2.5 border border-[var(--border)] rounded-xl font-bold text-[var(--textPrimary)] bg-white hover:bg-[var(--bgPrimary)] transition-all">
            View Profile
        </a>
    </div>

    <!-- Reviews Section -->
    <div class="mt-12 pt-8 border-t border-[var(--border)]">
        <h2 class="text-2xl font-bold mb-6 text-[var(--textPrimary)]">
            Reviews & Comments ({{ $product->reviews->count() }})
        </h2>

        <!-- Add Review Form -->
        <div class="bg-[var(--bgTertiary)] p-6 rounded-2xl mb-8">
            <h3 class="text-lg font-bold mb-4 text-[var(--textPrimary)]">Write a Review</h3>
            <form action="{{ route('products.reviews.store', $product->slug) }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block mb-2 text-[var(--textSecondary)] text-sm font-bold">Rating</label>
                    <div class="flex gap-1.5">
                        <template x-for="star in [1, 2, 3, 4, 5]">
                            <button type="button" @click="rating = star" class="bg-none border-none cursor-pointer p-1">
                                <i class="ph-fill ph-star text-2xl transition-colors" 
                                   :class="star <= rating ? 'text-[var(--warning)]' : 'text-[var(--textMuted)]'"></i>
                            </button>
                        </template>
                    </div>
                    <input type="hidden" name="rating" :value="rating">
                </div>
                <div class="mb-4">
                    <label class="block mb-2 text-[var(--textSecondary)] text-sm font-bold">Your Comment</label>
                    <textarea name="comment" x-model="comment" required placeholder="Share your experience..." 
                              class="w-full p-4 rounded-xl border border-[var(--border)] bg-white text-sm min-h-[100px] outline-none focus:border-[var(--primary)]"></textarea>
                </div>
                <button type="submit" class="px-6 py-3 bg-[var(--primary)] text-white border-none rounded-xl font-bold cursor-pointer hover:bg-[var(--primaryHover)] transition-all shadow-md">
                    Post Review
                </button>
            </form>
        </div>

        <!-- Reviews List -->
        <div class="flex flex-col gap-5">
            @forelse($product->reviews->sortByDesc('created_at') as $review)
                <div class="bg-white p-5 rounded-2xl border border-[var(--border)]">
                    <div class="flex items-center justify-between mb-2.5">
                        <div class="flex items-center gap-2.5">
                            <div class="w-10 h-10 rounded-full bg-[var(--bgTertiary)] overflow-hidden flex items-center justify-center">
                                @if($review->user->avatar_path)
                                    <img src="{{ Storage::url($review->user->avatar_path) }}" class="w-full h-full object-cover">
                                @else
                                    <i class="ph ph-user text-xl text-[var(--textMuted)]"></i>
                                @endif
                            </div>
                            <div>
                                <div class="font-bold text-[var(--textPrimary)] text-sm">{{ $review->user->name }}</div>
                                <div class="text-[10px] text-[var(--textMuted)]">{{ $review->created_at->format('M d, Y') }}</div>
                            </div>
                        </div>
                        <div class="flex gap-0.5">
                            @foreach(range(1, 5) as $i)
                                <i class="ph-fill ph-star text-sm {{ $i <= $review->rating ? 'text-[var(--warning)]' : 'text-[var(--textMuted)]' }}"></i>
                            @endforeach
                        </div>
                    </div>
                    <p class="text-[var(--textSecondary)] leading-relaxed m-0 text-sm">{{ $review->comment }}</p>
                </div>
            @empty
                <p class="text-[var(--textSecondary)] italic text-sm">No reviews yet. Be the first to review!</p>
            @endforelse
        </div>
    </div>

    <!-- Offer Modal -->
    <div x-show="showOfferModal" class="fixed inset-0 z-[1000] flex items-center justify-center p-4 bg-black/75" x-cloak>
        <div class="bg-white rounded-3xl p-8 w-full max-w-md shadow-2xl" @click.away="showOfferModal = false">
            <h2 class="text-2xl font-extrabold text-[var(--textPrimary)] mb-4">Make an Offer</h2>
            <p class="text-[var(--textSecondary)] mb-6">Suggest a price for <strong>{{ $product->title }}</strong></p>
            
            <form action="{{ route('products.offers.store', $product->slug) }}" method="POST">
                @csrf
                <div class="mb-6">
                    <label class="block mb-2 text-[var(--textSecondary)] text-sm font-bold">Your Offer Amount</label>
                    <div class="relative">
                        <input type="number" name="amount" value="{{ $product->price }}" required class="w-full p-4 pl-6 pr-14 rounded-xl border border-[var(--border)] text-lg font-bold outline-none focus:border-[var(--primary)]">
                        <span class="absolute right-4 top-1/2 -translate-y-1/2 font-bold text-[var(--textMuted)]">MAD</span>
                    </div>
                </div>
                <div class="flex gap-4">
                    <button type="button" @click="showOfferModal = false" class="flex-1 py-4 rounded-xl font-bold text-[var(--textSecondary)] hover:bg-gray-100 transition-all">Cancel</button>
                    <button type="submit" class="flex-1 py-4 bg-[var(--primary)] text-white rounded-xl font-bold hover:bg-[var(--primaryHover)] transition-all">Send Offer</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
