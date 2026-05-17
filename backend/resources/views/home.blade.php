@extends('layouts.main')

@section('title', 'TinyTrove - Pre-loved Kids Treasures')

@php
    function getCategoryIcon($name, $size = 20) {
        $name = strtolower($name);
        if (str_contains($name, 'jouet') || str_contains($name, 'toy')) return "ph ph-gamepad";
        if (str_contains($name, 'vêtement') || str_contains($name, 'cloth')) return "ph ph-t-shirt";
        if (str_contains($name, 'livre') || str_contains($name, 'book')) return "ph ph-book";
        if (str_contains($name, 'mobilier') || str_contains($name, 'furniture')) return "ph ph-house";
        if (str_contains($name, 'bébé') || str_contains($name, 'baby')) return "ph ph-user";
        if (str_contains($name, 'jeu') || str_contains($name, 'game')) return "ph ph-gamepad";
        if (str_contains($name, 'chaussure') || str_contains($name, 'shoe')) return "ph ph-walking";
        if (str_contains($name, 'activité') || str_contains($name, 'art')) return "ph ph-palette";
        return "ph ph-box";
    }

    function getImageUrl($media) {
        if (!$media) return null;
        if (isset($media['url']) && str_starts_with($media['url'], 'http')) return $media['url'];
        if (isset($media['file_path'])) return asset('storage/' . str_replace('public/', '', $media['file_path']));
        return null;
    }

    function getCategoryColor($id) {
        $catColors = ['var(--primary)', 'var(--coral)', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#3b82f6', '#ec4899'];
        return $catColors[$id % count($catColors)] ?? 'var(--primary)';
    }
@endphp

@section('content')
<main class="home redesign" 
      x-data="{ 
          activeSlide: 0, 
          slideProgress: 0, 
          activeCategoryTab: {{ $data->featuredCategories->first()->id ?? 'null' }},
          timeLeft: { days: 3, hours: 0, minutes: 0, seconds: 0 }
      }"
      x-init="
          setInterval(() => {
              slideProgress += 2;
              if (slideProgress >= 100) {
                  activeSlide = (activeSlide + 1) % {{ count($data->heroSliders) }};
                  slideProgress = 0;
              }
          }, 100);
          
          setInterval(() => {
              // Basic countdown logic
              if (timeLeft.seconds > 0) timeLeft.seconds--;
              else {
                  timeLeft.seconds = 59;
                  if (timeLeft.minutes > 0) timeLeft.minutes--;
                  else {
                      timeLeft.minutes = 59;
                      if (timeLeft.hours > 0) timeLeft.hours--;
                      else {
                          timeLeft.hours = 23;
                          if (timeLeft.days > 0) timeLeft.days--;
                      }
                  }
              }
          }, 1000);
      ">
    
    {{-- Sticky Season Banner --}}
    <div class="sticky-season-wrap">
        <div class="season-pill-banner" style="background-color: var(--bgSecondary);">
            <span class="badge">Limited</span>
            <p>Summer Deals: <span x-text="timeLeft.days"></span>d <span x-text="String(timeLeft.hours).padStart(2, '0')"></span>:<span x-text="String(timeLeft.minutes).padStart(2, '0')"></span> remaining</p>
            <a href="/announcements" style="color: var(--coral);">Shop Now <i class="ph ph-arrow-right" style="font-size: 14px;"></i></a>
        </div>
    </div>

    {{-- Hero Slider --}}
    <section class="hero-slider">
        <div class="slides-container">
            @foreach($data->heroSliders as $index => $slide)
                <div class="slide" :class="{ 'active': activeSlide === {{ $index }} }">
                    <img src="{{ getImageUrl($slide->thumbnail) }}" alt="{{ $slide->headline }}" class="slide-image">
                    <div class="slide-scrim"></div>
                    <div class="slide-content">
                        <h1 class="editorial-title">{{ $slide->headline }}</h1>
                        <p class="slide-subline">{{ $slide->subline }}</p>
                        <div class="slide-actions">
                            @if($slide->cta1_text)
                                <a href="{{ $slide->cta1_link }}" class="btn-primary" style="background-color: var(--coral); color: #fff;">{{ $slide->cta1_text }}</a>
                            @endif
                            @if($slide->cta2_text)
                                <a href="{{ $slide->cta2_link }}" class="btn-outline" style="border-color: #fff; color: #fff;">{{ $slide->cta2_text }}</a>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        
        <div class="slider-nav">
            <div class="slider-dots">
                @foreach($data->heroSliders as $index => $slide)
                    <button class="dot" 
                            :class="{ 'active': activeSlide === {{ $index }} }" 
                            @click="activeSlide = {{ $index }}; slideProgress = 0;"
                            :style="activeSlide === {{ $index }} ? 'background-color: var(--coral)' : 'background-color: rgba(255,255,255,0.5)'">
                    </button>
                @endforeach
            </div>
            <div class="slider-progress-bg">
                <div class="slider-progress-bar" :style="'width: ' + slideProgress + '%; background-color: var(--coral)'"></div>
            </div>
        </div>
    </section>

    {{-- Stats Band --}}
    <div class="stats-band" style="background-color: var(--bgSecondary); border-bottom: 1px solid var(--border);">
        <div class="stats-container">
            <div class="stat-item">
                <i class="ph-bold ph-box" style="font-size: 24px; color: var(--coral);"></i>
                <div>
                    <strong>{{ number_format($data->stats['total_products'] ?? 0) }}</strong>
                    <span>Items Listed</span>
                </div>
            </div>
            <div class="stat-item">
                <i class="ph-bold ph-users-three" style="font-size: 24px; color: var(--coral);"></i>
                <div>
                    <strong>{{ number_format($data->stats['total_users'] ?? 0) }}</strong>
                    <span>Active Parents</span>
                </div>
            </div>
            <div class="stat-item">
                <i class="ph-bold ph-heart" style="font-size: 24px; color: var(--coral);"></i>
                <div>
                    <strong>{{ number_format($data->stats['total_donations'] ?? 0) }}</strong>
                    <span>Donations</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Shop by Category Tabs --}}
    <section class="shop-by-tabs-section tt-container">
        <div class="section-header-editorial">
            <h2 class="editorial-title">Shop by Category</h2>
            <p>Find exactly what they need, sorted by category.</p>
        </div>

        <div class="tabs-wrapper">
            <div class="pill-tabs no-scrollbar">
                @foreach($data->featuredCategories as $cat)
                    <button class="pill-tab" 
                            :class="{ 'active': activeCategoryTab === {{ $cat->id }} }"
                            @click="activeCategoryTab = {{ $cat->id }}">
                        <span class="tab-emoji"><i class="{{ getCategoryIcon($cat->name) }}"></i></span>
                        {{ $cat->name }}
                    </button>
                @endforeach
            </div>
        </div>

        <div class="tab-content-area">
            @foreach($data->featuredCategories as $cat)
                <div class="tab-pane" :class="{ 'active': activeCategoryTab === {{ $cat->id }} }">
                    <div class="scroll-container no-scrollbar" x-data="{ scroll(dir) { $refs.row{{ $cat->id }}.scrollBy({ left: dir === 'left' ? -400 : 400, behavior: 'smooth' }) } }">
                        <button class="scroll-btn left" @click="scroll('left')"><i class="ph ph-caret-left"></i></button>
                        <div class="category-scroll-row" x-ref="row{{ $cat->id }}">
                            @foreach($data->productsByCategory[$cat->id] ?? [] as $product)
                                <div class="home-card-wrapper">
                                    <x-marketplace-card :product="$product" view="grid" />
                                </div>
                            @endforeach
                            <a href="/announcements?category={{ $cat->id }}" class="view-more-card">
                                <div class="view-more-inner">
                                    <div class="icon-circle"><i class="ph ph-arrow-right"></i></div>
                                    <span>View all {{ $cat->name }}</span>
                                </div>
                            </a>
                        </div>
                        <button class="scroll-btn right" @click="scroll('right')"><i class="ph ph-caret-right"></i></button>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    {{-- Collections Grid --}}
    <section class="collections-grid-section tt-container">
        <div class="section-header-editorial">
            <h2 class="editorial-title">Browse Collections</h2>
            <p>Explore our curated selections for every stage.</p>
        </div>
        <div class="collections-grid-redesign">
            @foreach($data->featuredCategories->take(8) as $cat)
                <a href="/announcements?category={{ $cat->id }}" class="collection-tile">
                    <div class="tile-bg-wrap">
                        @if($cat->thumbnail)
                            <img src="{{ $cat->thumbnail->url }}" alt="{{ $cat->name }}" class="tile-image">
                        @else
                            <div class="tile-bg" style="background: linear-gradient(135deg, {{ getCategoryColor($cat->id) }} 0%, {{ getCategoryColor($cat->id) }}cc 100%)">
                                <span class="tile-emoji"><i class="{{ getCategoryIcon($cat->name, 32) }}" style="color: white; font-size: 32px;"></i></span>
                            </div>
                        @endif
                    </div>
                    <div class="tile-info">
                        <h4>{{ $cat->name }}</h4>
                        <span>{{ $cat->products_count ?? 0 }} treasures</span>
                    </div>
                </a>
            @endforeach
        </div>
    </section>

    {{-- Trending Now --}}
    <section class="trending-row-section tt-container">
        <div class="section-header-editorial with-nav">
            <div>
                <h2 class="editorial-title">Trending Now</h2>
                <p>The most loved items in our community this week.</p>
            </div>
            <div class="row-nav" x-data="{ scroll(dir) { $refs.trendingRow.scrollBy({ left: dir === 'left' ? -400 : 400, behavior: 'smooth' }) } }">
                <button @click="scroll('left')"><i class="ph ph-caret-left"></i></button>
                <button @click="scroll('right')"><i class="ph ph-caret-right"></i></button>
            </div>
        </div>
        <div class="scroll-container no-scrollbar">
            <div class="trending-scroll-row" x-ref="trendingRow">
                @foreach($data->popularProducts as $product)
                    <div class="home-card-wrapper">
                        <x-marketplace-card :product="$product" view="grid" />
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Dynamic Banners Section --}}
    @foreach($data->banners as $banner)
        <section class="banner-section {{ $banner['type'] }}-banner" style="background-color: var(--bgTertiary); padding: 60px 0;">
            <div class="tt-container">
                @if($banner['type'] === 'split')
                    <div class="editorial-split">
                        <div class="split-image">
                            @if($banner['thumbnail'])
                                <img src="{{ getImageUrl($banner['thumbnail']) }}" alt="{{ $banner['title'] }}">
                            @endif
                            @if($banner['badge_text'])
                                <div class="floating-badge" style="background-color: var(--coral);">{{ $banner['badge_text'] }}</div>
                            @endif
                        </div>
                        <div class="split-content">
                            <h2 class="editorial-title">{{ $banner['title'] }}</h2>
                            @if($banner['subtitle']) <p class="banner-subtitle">{{ $banner['subtitle'] }}</p> @endif
                            @if($banner['steps'])
                                <div class="steps-list">
                                    @foreach($banner['steps'] as $step)
                                        <div class="step-item">
                                            <span class="step-num">{{ $step['num'] }}</span>
                                            <div>
                                                <h4>{{ $step['title'] }}</h4>
                                                <p>{{ $step['description'] }}</p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                            @if($banner['cta_text'])
                                <a href="{{ $banner['cta_link'] ?? '#' }}" class="btn-text">
                                    {{ $banner['cta_text'] }} <i class="ph ph-arrow-right"></i>
                                </a>
                            @endif
                        </div>
                    </div>
                @else
                    <div class="simple-banner-content" style="text-align: center;">
                        <div class="simple-banner-inner" style=" 
                            background-image: {{ $banner['thumbnail'] ? 'linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)), url('.getImageUrl($banner['thumbnail']).')' : 'none' }};
                            background-size: cover;
                            background-position: center;
                            padding: 80px 40px;
                            border-radius: 24px;
                            color: #fff">
                            @if($banner['badge_text']) <span class="banner-badge" style="background-color: var(--coral); padding: 4px 12px; border-radius: 12px; font-size: 12px; font-weight: bold; margin-bottom: 16px; display: inline-block;">{{ $banner['badge_text'] }}</span> @endif
                            <h2 class="editorial-title" style="color: #fff; margin-bottom: 16px;">{{ $banner['title'] }}</h2>
                            @if($banner['subtitle']) <p style="font-size: 18px; margin-bottom: 32px; max-width: 600px; margin: 0 auto 32px;">{{ $banner['subtitle'] }}</p> @endif
                            @if($banner['cta_text'])
                                <a href="{{ $banner['cta_link'] ?? '#' }}" class="btn-primary" style="background-color: var(--coral); color: #fff; padding: 12px 32px; border-radius: 12px; text-decoration: none; display: inline-block; font-weight: bold;">
                                    {{ $banner['cta_text'] }}
                                </a>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </section>
    @endforeach

    {{-- New Arrivals --}}
    <section class="trending-row-section tt-container">
        <div class="section-header-editorial with-nav">
            <div>
                <h2 class="editorial-title">New Arrivals</h2>
                <p>Fresh finds uploaded by parents just now.</p>
            </div>
            <div class="row-nav" x-data="{ scroll(dir) { $refs.marketRow.scrollBy({ left: dir === 'left' ? -400 : 400, behavior: 'smooth' }) } }">
                <button @click="scroll('left')"><i class="ph ph-caret-left"></i></button>
                <button @click="scroll('right')"><i class="ph ph-caret-right"></i></button>
            </div>
        </div>
        <div class="scroll-container no-scrollbar">
            <div class="trending-scroll-row" x-ref="marketRow">
                @foreach($data->newArrivals as $product)
                    <div class="home-card-wrapper">
                        <x-marketplace-card :product="$product" view="grid" />
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Nearby Products --}}
    @if(count($data->nearbyProducts) > 0)
        <section class="trending-row-section tt-container">
            <div class="section-header-editorial with-nav">
                <div>
                    <h2 class="editorial-title">Nearby Treasures</h2>
                    <p>Find great deals from parents in your city.</p>
                </div>
                <div class="row-nav" x-data="{ scroll(dir) { $refs.nearbyRow.scrollBy({ left: dir === 'left' ? -400 : 400, behavior: 'smooth' }) } }">
                    <button @click="scroll('left')"><i class="ph ph-caret-left"></i></button>
                    <button @click="scroll('right')"><i class="ph ph-caret-right"></i></button>
                </div>
            </div>
            <div class="scroll-container no-scrollbar">
                <div class="trending-scroll-row" x-ref="nearbyRow">
                    @foreach($data->nearbyProducts as $product)
                        <div class="home-card-wrapper">
                            <x-marketplace-card :product="$product" view="grid" />
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- Free Items --}}
    @if(count($data->freeItems) > 0)
        <section class="trending-row-section tt-container">
            <div class="section-header-editorial with-nav">
                <div>
                    <h2 class="editorial-title">Free for All</h2>
                    <p>Generous donations looking for a new home.</p>
                </div>
                <div class="row-nav" x-data="{ scroll(dir) { $refs.freeRow.scrollBy({ left: dir === 'left' ? -400 : 400, behavior: 'smooth' }) } }">
                    <button @click="scroll('left')"><i class="ph ph-caret-left"></i></button>
                    <button @click="scroll('right')"><i class="ph ph-caret-right"></i></button>
                </div>
            </div>
            <div class="scroll-container no-scrollbar">
                <div class="trending-scroll-row" x-ref="freeRow">
                    @foreach($data->freeItems as $product)
                        <div class="home-card-wrapper">
                            <x-marketplace-card :product="$product" view="grid" />
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- Trust & Safety Band --}}
    <section class="trust-band" style="background-color: var(--bgSecondary);">
        <div class="tt-container">
            <div class="trust-grid">
                <div class="trust-item">
                    <i class="ph-bold ph-shield-check" style="font-size: 32px; color: var(--coral);"></i>
                    <h4>Secure Payments</h4>
                    <p>Your transactions are protected with industry-leading encryption.</p>
                </div>
                <div class="trust-item">
                    <i class="ph-bold ph-star" style="font-size: 32px; color: var(--coral);"></i>
                    <h4>Quality Checked</h4>
                    <p>Verified sellers and community ratings ensure high quality.</p>
                </div>
                <div class="trust-item">
                    <i class="ph-bold ph-gift" style="font-size: 32px; color: var(--coral);"></i>
                    <h4>Giving Back</h4>
                    <p>Every donation directly supports local verified charities.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- Testimonials --}}
    <section class="testimonials-redesign tt-container">
        <div class="section-header-editorial centered">
            <h2 class="editorial-title">Trust Reviews</h2>
            <p>Join thousands of families making a difference.</p>
        </div>
        <div class="testimonials-grid-redesign">
            @foreach(collect($data->recentReviews)->take(3) as $review)
                <div class="testimonial-editorial-card" style="background-color: var(--bgSecondary)">
                    <div class="rating-stars">
                        @foreach(range(1, $review['rating'] ?? 5) as $i)
                            <i class="ph-fill ph-star" style="font-size: 14px; color: var(--coral);"></i>
                        @endforeach
                    </div>
                    <p>"{{ $review['comment'] ?? 'Great experience with this community. Found perfect outfits for my toddler!' }}"</p>
                    <div class="reviewer">
                        <img src="{{ $review['reviewer']['avatar'] ?? 'https://ui-avatars.com/api/?name=' . ($review['reviewer']['name'] ?? 'U') }}" alt="{{ $review['reviewer']['name'] ?? 'Happy Customer' }}">
                        <strong>{{ $review['reviewer']['name'] ?? 'Happy Customer' }}</strong>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    {{-- Newsletter --}}
    <section class="newsletter-editorial">
        <div class="tt-container">
            <div class="newsletter-box" style="background-color: var(--primary); color: var(--bgPrimary);">
                <div class="newsletter-content">
                    <h2 class="editorial-title" style="color: var(--bgPrimary);">Join the TinyTrove Newsletter</h2>
                    <p>Get weekly curated treasures and impact reports delivered to your inbox.</p>
                    <form class="newsletter-form">
                        <div class="input-with-icon">
                            <i class="ph ph-envelope" style="font-size: 18px;"></i>
                            <input type="email" placeholder="Your email address" style="color: white;">
                        </div>
                        <button type="submit" style="background-color: var(--coral); color: var(--bgPrimary);">Subscribe</button>
                    </form>
                </div>
                <div class="newsletter-decor">
                    <i class="ph ph-shopping-bag" style="font-size: 120px; opacity: 0.1;"></i>
                </div>
            </div>
        </div>
    </section>
</main>
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('css/home.css') }}">
<style>
    /* Custom override for Blade-specific adjustments */
    .home-card-wrapper {
        min-width: 300px;
    }
</style>
@endpush
