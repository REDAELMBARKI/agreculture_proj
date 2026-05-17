@extends('layouts.main')

@section('title', 'Marketplace - LetUsDonate')

@section('content')
@push('styles')
<link rel="stylesheet" href="{{ asset('css/home.css') }}">
<style>
    .custom-scrollbar::-webkit-scrollbar { width: 3px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: var(--scrollbarTrack); }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: var(--coral); border-radius: 10px; }

    [x-cloak] { display: none !important; }

    .section-label {
        font-size: 12px;
        font-weight: 600;
        color: var(--textSecondary);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 8px;
    }

    .sidebar-btn-pill {
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
    }

    .sidebar-btn-pill.active {
        background-color: var(--coral);
        color: var(--bgSecondary);
        border: none;
    }

    .sidebar-btn-pill.inactive {
        background-color: var(--bgSecondary);
        color: var(--textSecondary);
        border: 1px solid var(--border);
    }

    .sidebar-toggle-switch {
        width: 36px;
        height: 20px;
        border-radius: 10px;
        position: relative;
        cursor: pointer;
        transition: background-color 0.2s;
    }

    .sidebar-toggle-knob {
        width: 16px;
        height: 16px;
        border-radius: 50%;
        background-color: var(--bgSecondary);
        position: absolute;
        top: 2px;
        transition: left 0.2s;
    }

    .size-tab-btn {
        flex: 1;
        padding: 8px 0;
        background: none;
        border: none;
        font-weight: 700;
        font-size: 13px;
        cursor: pointer;
        transition: all 0.2s;
    }

    .size-tab-btn.active {
        color: var(--coral);
        border-bottom: 2px solid var(--coral);
    }

    .size-tab-btn.inactive {
        color: var(--textSecondary);
        border-bottom: 1px solid var(--border);
    }
</style>
@endpush

<div x-data="{ 
    filters: {
        category: '{{ request('category') }}',
        cities: @json(request('cities', [])),
        mode: @json(request('mode', [])),
        age_range: @json(request('age_range', [])),
        gender: '{{ request('gender') }}',
        sizes: @json(request('sizes', [])),
        condition: '{{ request('condition') }}',
        min_price: '{{ request('min_price') }}',
        max_price: '{{ request('max_price') }}',
        free_only: {{ request('free_only', false) ? 'true' : 'false' }},
        with_media: {{ request('with_media', false) ? 'true' : 'false' }},
        search: '{{ request('search') }}',
        view: '{{ request('view', 'grid') }}'
    },
    sizeTab: 'clothes',
    loading: false,
    toggleArrayFilter(key, value) {
        if (this.filters[key].includes(value)) {
            this.filters[key] = this.filters[key].filter(v => v !== value);
        } else {
            this.filters[key].push(value);
        }
    },
    resetFilters() {
        this.filters = {
            category: '',
            cities: [],
            mode: [],
            age_range: [],
            gender: '',
            sizes: [],
            condition: '',
            min_price: '',
            max_price: '',
            free_only: false,
            with_media: false,
            search: '',
            view: this.filters.view
        };
        this.applyFilters();
    },
    applyFilters() {
        this.loading = true;
        const params = new URLSearchParams();
        Object.keys(this.filters).forEach(key => {
            if (Array.isArray(this.filters[key]) && this.filters[key].length > 0) {
                this.filters[key].forEach(val => params.append(`${key}[]`, val));
            } else if (this.filters[key] !== '' && this.filters[key] !== null && !Array.isArray(this.filters[key])) {
                params.append(key, this.filters[key]);
            }
        });
        window.location.href = '/announcements?' + params.toString();
    }
}" class="flex min-h-screen bg-[var(--bgPrimary)]" style="font-family: 'Poppins', sans-serif;">

    <!-- Sidebar -->
    <aside class="w-[280px] border-r border-[var(--sidebarBorder)] bg-[var(--bgSecondary)] sticky top-[80px] h-[calc(100vh-80px)] flex flex-col z-10">
        <!-- Top Row — Clear Only -->
        <div class="px-4 py-2.5 bg-[var(--filterBg)] border-b border-[var(--filterBorder)] flex justify-end shrink-0">
            <button @click="resetFilters" class="text-[var(--textSecondary)] text-[12px] font-medium cursor-pointer bg-none border-none p-0">Effacer tout</button>
        </div>

        <!-- Scrollable Content -->
        <div class="p-3 px-4 overflow-y-auto flex-1 custom-scrollbar">
            <!-- Category -->
            <div class="mb-5">
                <div class="section-label">Catégorie</div>
                <div class="relative">
                    <i class="ph ph-shopping-bag absolute left-3 top-1/2 -translate-y-1/2 text-[var(--coral)] z-10"></i>
                    <select x-model="filters.category" class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-[var(--border)] bg-[var(--bgSecondary)] text-sm focus:border-[var(--coral)] outline-none transition-colors appearance-none">
                        <option value="">Choisir catégorie</option>
                        @foreach($initData['categories'] as $cat)
                            <option value="{{ $cat['id'] }}">{{ $cat['label'] }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Cities -->
            <div class="mb-5">
                <div class="section-label">Ville - Secteur</div>
                <div class="space-y-2">
                    <div class="relative">
                        <i class="ph ph-map-pin absolute left-3 top-1/2 -translate-y-1/2 text-[var(--coral)] z-10"></i>
                        <select x-on:change="if($event.target.value && !filters.cities.includes($event.target.value)) filters.cities.push($event.target.value); $event.target.value=''" class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-[var(--border)] bg-[var(--bgSecondary)] text-sm focus:border-[var(--coral)] outline-none transition-colors appearance-none">
                            <option value="">Toutes les villes</option>
                            @foreach($initData['cities'] as $city)
                                <option value="{{ $city['id'] }}">{{ $city['label'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex flex-wrap gap-1.5 mt-2">
                        <template x-for="cityId in filters.cities" :key="cityId">
                            <div class="flex items-center gap-1 px-2 py-1 rounded-lg border border-[var(--infoText)] bg-[var(--infoBg)] text-[var(--infoText)] text-[11px] font-bold">
                                <span x-text="@json($initData['cities']).find(c => c.id == cityId)?.label"></span>
                                <i @click="filters.cities = filters.cities.filter(id => id != cityId)" class="ph ph-x cursor-pointer"></i>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            <!-- Listing Type -->
            <div class="mb-5">
                <div class="section-label">Type d'annonce</div>
                <div class="flex flex-col gap-2.5">
                    @foreach($initData['listingTypes'] as $type)
                        <div @click="toggleArrayFilter('mode', '{{ $type['value'] }}')" class="flex items-center justify-between cursor-pointer group">
                            <div class="flex items-center gap-2.5">
                                <i class="ph ph-{{ $type['value'] == 'sell' ? 'storefront' : 'gift' }} text-xl transition-colors" :class="filters.mode.includes('{{ $type['value'] }}') ? 'text-[var(--coral)]' : 'text-[var(--textMuted)]'"></i>
                                <span class="text-sm text-[var(--textPrimary)]">{{ $type['label'] }}</span>
                            </div>
                            <div class="w-5 h-5 rounded border transition-colors flex items-center justify-center" :class="filters.mode.includes('{{ $type['value'] }}') ? 'bg-[var(--coral)] border-[var(--coral)]' : 'border-[var(--border)]'">
                                <i x-show="filters.mode.includes('{{ $type['value'] }}')" class="ph ph-check text-white text-xs"></i>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Age Range -->
            <div class="mb-5">
                <div class="section-label">Tranche d'âge</div>
                <div class="flex flex-wrap gap-2">
                    @foreach($initData['ageRanges'] as $age)
                        <button @click="toggleArrayFilter('age_range', '{{ $age['value'] }}')" 
                                class="sidebar-btn-pill"
                                :class="filters.age_range.includes('{{ $age['value'] }}') ? 'active' : 'inactive'">
                            {{ $age['label'] }}
                        </button>
                    @endforeach
                </div>
            </div>

            <!-- Gender -->
            <div class="mb-5">
                <div class="section-label">Genre</div>
                <div class="flex gap-2">
                    @foreach(['girl' => ['label' => 'Fille', 'icon' => 'user'], 'boy' => ['label' => 'Garçon', 'icon' => 'user'], 'both' => ['label' => 'Mixte', 'icon' => 'users']] as $key => $g)
                        <button @click="filters.gender = filters.gender === '{{ $key }}' ? '' : '{{ $key }}'" 
                                class="flex-1 py-2 rounded-xl border flex flex-col items-center gap-1 transition-all cursor-pointer"
                                :class="filters.gender === '{{ $key }}' ? 'bg-[var(--coral)] border-[var(--coral)] text-white' : 'bg-[var(--bgSecondary)] border-[var(--border)] text-[var(--textSecondary)]'">
                            <i class="ph ph-{{ $g['icon'] }} text-lg" :class="filters.gender === '{{ $key }}' ? 'text-white' : 'text-[var(--textMuted)]'"></i>
                            <span class="text-[11px] font-bold">{{ $g['label'] }}</span>
                        </button>
                    @endforeach
                </div>
            </div>

            <!-- Size Tabbed Section -->
            <div class="mb-5">
                <div class="section-label">Taille</div>
                <div class="flex border-b border-[var(--border)] mb-3">
                    <button @click="sizeTab = 'clothes'" class="size-tab-btn" :class="sizeTab === 'clothes' ? 'active' : 'inactive'">Vêtements</button>
                    <button @click="sizeTab = 'shoes'" class="size-tab-btn" :class="sizeTab === 'shoes' ? 'active' : 'inactive'">Chaussures</button>
                </div>
                <div class="flex flex-wrap gap-1.5">
                    <template x-if="sizeTab === 'clothes'">
                        @foreach($initData['clothingSizes'] as $size)
                            <button @click="toggleArrayFilter('sizes', '{{ $size['value'] }}')" 
                                    class="px-2.5 py-1 rounded-[15px] border text-[11px] font-semibold cursor-pointer transition-all"
                                    :class="filters.sizes.includes('{{ $size['value'] }}') ? 'bg-[var(--coral)] border-[var(--coral)] text-white' : 'bg-[var(--bgSecondary)] border-[var(--border)] text-[var(--textSecondary)]'">
                                {{ $size['label'] }}
                            </button>
                        @endforeach
                    </template>
                    <template x-if="sizeTab === 'shoes'">
                        @foreach($initData['shoeSizes'] as $size)
                            <button @click="toggleArrayFilter('sizes', '{{ $size['value'] }}')" 
                                    class="px-2.5 py-1 rounded-[15px] border text-[11px] font-semibold cursor-pointer transition-all"
                                    :class="filters.sizes.includes('{{ $size['value'] }}') ? 'bg-[var(--coral)] border-[var(--coral)] text-white' : 'bg-[var(--bgSecondary)] border-[var(--border)] text-[var(--textSecondary)]'">
                                {{ $size['label'] }}
                            </button>
                        @endforeach
                    </template>
                </div>
            </div>

            <!-- Condition -->
            <div class="mb-5">
                <div class="section-label">État</div>
                <div class="flex flex-col gap-1">
                    @foreach($initData['conditions'] as $cond)
                        <div @click="filters.condition = filters.condition === '{{ $cond['value'] }}' ? '' : '{{ $cond['value'] }}'" 
                             class="flex items-center gap-2.5 p-2.5 cursor-pointer rounded-r-lg transition-all border-l-[3px]"
                             :class="filters.condition === '{{ $cond['value'] }}' ? 'bg-[var(--coralLight)] border-[var(--coral)]' : 'border-transparent'">
                            <div class="w-2 h-2 rounded-full" style="background-color: {{ $cond['color'] }}"></div>
                            <span class="text-[13px] transition-all" :class="filters.condition === '{{ $cond['value'] }}' ? 'text-[var(--coral)] font-semibold' : 'text-[var(--textPrimary)]'">{{ $cond['label'] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Price -->
            <div class="mb-5">
                <div class="section-label">Prix</div>
                <div class="flex gap-2.5 items-center mb-3">
                    <div class="relative flex-1">
                        <input type="number" placeholder="Min" x-model="filters.min_price" class="w-full pl-3 pr-10 py-2 rounded-lg border border-[var(--border)] bg-[var(--bgSecondary)] text-xs focus:border-[var(--coral)] outline-none text-[var(--textPrimary)]">
                        <span class="absolute right-2 top-1/2 -translate-y-1/2 text-[11px] text-[var(--textMuted)]">MAD</span>
                    </div>
                    <div class="relative flex-1">
                        <input type="number" placeholder="Max" x-model="filters.max_price" class="w-full pl-3 pr-10 py-2 rounded-lg border border-[var(--border)] bg-[var(--bgSecondary)] text-xs focus:border-[var(--coral)] outline-none text-[var(--textPrimary)]">
                        <span class="absolute right-2 top-1/2 -translate-y-1/2 text-[11px] text-[var(--textMuted)]">MAD</span>
                    </div>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-[13px] text-[var(--textPrimary)]">Gratuit uniquement</span>
                    <div @click="filters.free_only = !filters.free_only" class="sidebar-toggle-switch" :class="filters.free_only ? 'bg-[var(--coral)]' : 'bg-[var(--bgTertiary)]'">
                        <div class="sidebar-toggle-knob" :class="filters.free_only ? 'left-[18px]' : 'left-[2px]'"></div>
                    </div>
                </div>
            </div>

            <!-- Media Only Toggle -->
            <div class="mb-5 flex items-center gap-3">
                <div class="bg-[var(--darkNavy)] text-white p-1.5 rounded-lg flex items-center justify-center">
                    <i class="ph ph-camera text-sm"></i>
                </div>
                <span class="flex-1 text-[12px] text-[var(--textPrimary)]">Annonces avec photos-vidéos uniquement</span>
                <div @click="filters.with_media = !filters.with_media" class="sidebar-toggle-switch" :class="filters.with_media ? 'bg-[var(--coral)]' : 'bg-[var(--bgTertiary)]'">
                    <div class="sidebar-toggle-knob" :class="filters.with_media ? 'left-[18px]' : 'left-[2px]'"></div>
                </div>
            </div>
        </div>

        <!-- Apply Button -->
        <div class="p-4 border-t border-[var(--border)] bg-[var(--bgSecondary)] sticky bottom-0 z-10">
            <button @click="applyFilters" class="w-full py-3.5 bg-[var(--coral)] text-white rounded-xl font-bold hover:bg-[var(--coralHover)] transition-all shadow-lg shadow-[#5580A833] active:translate-y-0.5">
                <span x-show="!loading">Voir les annonces ({{ $listings->total() }})</span>
                <span x-show="loading">Chargement...</span>
            </button>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 flex flex-col">
        <!-- Top Bar -->
        <div class="px-10 py-5 bg-[var(--bgPrimary)] border-b border-[var(--border)] flex items-center justify-between sticky top-[80px] z-[5]">
            <div class="flex items-center gap-5">
                <h1 class="text-[24px] font-extrabold text-[var(--textPrimary)] m-0">Marketplace</h1>
                
                <div class="flex items-center gap-2.5">
                    <div class="relative w-[300px]">
                        <i class="ph ph-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-[var(--textMuted)] z-10"></i>
                        <input type="text" x-model="filters.search" @keyup.enter="applyFilters" placeholder="Rechercher un article..." class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-[var(--border)] bg-[var(--bgSecondary)] text-sm focus:border-[var(--coral)] outline-none transition-all text-[var(--textPrimary)]">
                    </div>

                    <div class="relative w-[200px]">
                        <i class="ph ph-map-pin absolute left-3 top-1/2 -translate-y-1/2 text-[var(--coral)] z-10"></i>
                        <select x-on:change="if($event.target.value && !filters.cities.includes($event.target.value)) filters.cities.push($event.target.value); $event.target.value=''" class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-[var(--border)] bg-[var(--bgSecondary)] text-sm focus:border-[var(--coral)] outline-none transition-colors appearance-none text-[var(--textPrimary)]">
                            <option value="">Toutes les villes</option>
                            @foreach($initData['cities'] as $city)
                                <option value="{{ $city['id'] }}">{{ $city['label'] }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-4">
                <div class="flex bg-[var(--bgTertiary)] p-1 rounded-lg gap-1">
                    <button @click="filters.view = 'grid'" class="p-1.5 rounded transition-all border-none cursor-pointer" :class="filters.view === 'grid' ? 'bg-white shadow-sm' : 'bg-transparent'">
                        <i class="ph ph-squares-four text-lg" :class="filters.view === 'grid' ? 'text-[var(--coral)]' : 'text-[var(--textMuted)]'"></i>
                    </button>
                    <button @click="filters.view = 'list'" class="p-1.5 rounded transition-all border-none cursor-pointer" :class="filters.view === 'list' ? 'bg-white shadow-sm' : 'bg-transparent'">
                        <i class="ph ph-list text-lg" :class="filters.view === 'list' ? 'text-[var(--coral)]' : 'text-[var(--textMuted)]'"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Listings Grid -->
        <div class="p-10 flex-1">
            @if($listings->count() > 0)
                <div x-show="filters.view === 'grid'" class="grid gap-6 grid-cols-[repeat(auto-fill,minmax(280px,1fr))]">
                    @foreach($listings as $product)
                        <x-marketplace-card :product="$product" view="grid" />
                    @endforeach
                </div>

                <div x-show="filters.view === 'list'" class="grid gap-6 grid-cols-1">
                    @foreach($listings as $product)
                        <x-marketplace-card :product="$product" view="list" />
                    @endforeach
                </div>

                <div class="mt-12">
                    {{ $listings->appends(request()->query())->links() }}
                </div>
            @else
                <div class="text-center py-24 bg-[var(--bgSecondary)] rounded-[24px] shadow-sm border border-[var(--border)]">
                    <i class="ph-bold ph-shopping-bag text-7xl text-[var(--textMuted)] mb-5 block mx-auto"></i>
                    <h2 class="text-[24px] font-extrabold text-[var(--textPrimary)] mb-2">Aucun article trouvé</h2>
                    <p class="text-[var(--textSecondary)] mb-8">Essayez d'ajuster vos filtres pour trouver ce que vous cherchez.</p>
                    <button @click="resetFilters" class="px-8 py-3 bg-[var(--coral)] text-white rounded-xl font-bold hover:bg-[var(--coralHover)] transition-all cursor-pointer border-none">Réinitialiser les filtres</button>
                </div>
            @endif
        </div>
    </main>

</div>
@endsection
