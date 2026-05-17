@props(['initData', 'filters', 'resultsCount'])

<aside class="w-[280px] border-r border-[var(--sidebarBorder)] bg-[var(--bgSecondary)] sticky top-[80px] h-[calc(100vh-80px)] flex flex-col z-10"
       x-data="{ 
           sizeTab: 'clothes',
           filters: @js($filters),
           initData: @js($initData)
       }">
    
    {{-- Top Row — Clear Only --}}
    <div class="px-4 py-2.5 bg-[var(--filterBg)] border-b border-[var(--filterBorder)] flex items-center justify-end shrink-0">
        <a href="{{ url()->current() }}" class="text-xs text-[var(--textSecondary)] no-underline hover:underline">Effacer tout</a>
    </div>

    {{-- Scrollable Content --}}
    <div class="custom-scrollbar p-4 overflow-y-auto flex-1">
        <style>
            .custom-scrollbar::-webkit-scrollbar { width: 3px; }
            .custom-scrollbar::-webkit-scrollbar-track { background: var(--scrollbarTrack); }
            .custom-scrollbar::-webkit-scrollbar-thumb { background: var(--coral); border-radius: 10px; }
        </style>

        {{-- Catégorie --}}
        <div class="mb-5">
            <div class="text-[12px] font-semibold text-[var(--textSecondary)] uppercase tracking-wider mb-2">Catégorie</div>
            <x-custom-select 
                :options="$initData['categories']" 
                :value="$filters['category'] ?? ''"
                placeholder="Choisir catégorie"
                name="category"
                icon='<i class="ph-bold ph-shopping-bag text-lg text-[var(--coral)]"></i>'
            />
        </div>

        {{-- Ville - Secteur --}}
        <div class="mb-5">
            <div class="text-[12px] font-semibold text-[var(--textSecondary)] uppercase tracking-wider mb-2">Ville - Secteur</div>
            <x-custom-select 
                :options="$initData['cities']" 
                :value="$filters['cities'] ?? []"
                :multiple="true"
                :searchable="true"
                placeholder="Toutes les villes"
                name="cities"
                icon='<i class="ph-bold ph-map-pin text-lg text-[var(--coral)]"></i>'
            />
        </div>

        {{-- Type d'annonce --}}
        <div class="mb-5">
            <div class="text-[12px] font-semibold text-[var(--textSecondary)] uppercase tracking-wider mb-2">Type d'annonce</div>
            <div class="flex flex-col gap-2.5">
                @foreach($initData['listingTypes'] as $type)
                    <div class="flex items-center justify-between cursor-pointer group" @click="filters.mode = (filters.mode || []).includes('{{ $type['value'] }}') ? filters.mode.filter(v => v !== '{{ $type['value'] }}') : [...(filters.mode || []), '{{ $type['value'] }}']">
                        <div class="flex items-center gap-2.5">
                            <i class="ph-bold ph-{{ $type['value'] === 'sell' ? 'storefront' : 'gift' }} text-xl transition-colors"
                               :class="(filters.mode || []).includes('{{ $type['value'] }}') ? 'text-[var(--coral)]' : 'text-[var(--iconMuted)]'"></i>
                            <span class="text-sm text-[var(--textPrimary)]">{{ $type['label'] }}</span>
                        </div>
                        <div class="w-5 h-5 rounded border transition-all flex items-center justify-center"
                             :class="(filters.mode || []).includes('{{ $type['value'] }}') ? 'bg-[var(--coral)] border-[var(--coral)]' : 'border-[var(--border)]'">
                            <template x-if="(filters.mode || []).includes('{{ $type['value'] }}')">
                                <i class="ph ph-check text-white text-xs"></i>
                            </template>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Tranche d'âge --}}
        <div class="mb-5">
            <div class="text-[12px] font-semibold text-[var(--textSecondary)] uppercase tracking-wider mb-2">Tranche d'âge</div>
            <div class="flex flex-wrap gap-2">
                @foreach($initData['ageRanges'] as $age)
                    <button type="button" 
                            class="px-3 py-1.5 rounded-full border text-xs font-semibold transition-all"
                            :class="(filters.age_range || []).includes('{{ $age['value'] }}') ? 'bg-[var(--coral)] border-transparent text-white' : 'bg-[var(--bgSecondary)] border-[var(--border)] text-[var(--textSecondary)]'"
                            @click="filters.age_range = (filters.age_range || []).includes('{{ $age['value'] }}') ? filters.age_range.filter(v => v !== '{{ $age['value'] }}') : [...(filters.age_range || []), '{{ $age['value'] }}']">
                        {{ $age['label'] }}
                    </button>
                @endforeach
            </div>
        </div>

        {{-- Genre --}}
        <div class="mb-5">
            <div class="text-[12px] font-semibold text-[var(--textSecondary)] uppercase tracking-wider mb-2">Genre</div>
            <div class="flex gap-2">
                @foreach(['girl' => 'Fille', 'boy' => 'Garçon', 'both' => 'Mixte'] as $val => $label)
                    <button type="button" 
                            class="flex-1 py-2 rounded-xl border text-xs font-semibold flex flex-col items-center gap-1 transition-all"
                            :class="filters.gender === '{{ $val }}' ? 'bg-[var(--coral)] border-transparent text-white' : 'bg-[var(--bgSecondary)] border-[var(--border)] text-[var(--textSecondary)]'"
                            @click="filters.gender = filters.gender === '{{ $val }}' ? '' : '{{ $val }}'">
                        <i class="ph-bold ph-{{ $val === 'both' ? 'users' : 'user' }} text-lg" :class="filters.gender === '{{ $val }}' ? 'text-white' : 'text-[var(--iconMuted)]'"></i>
                        {{ $label }}
                    </button>
                @endforeach
            </div>
        </div>

        {{-- Prix --}}
        <div class="mb-5">
            <div class="text-[12px] font-semibold text-[var(--textSecondary)] uppercase tracking-wider mb-2">Prix</div>
            <div class="flex gap-2.5 items-center mb-3">
                <div class="relative flex-1">
                    <input type="number" name="min_price" placeholder="Min" x-model="filters.min_price"
                           class="w-full pl-3 pr-10 py-2 rounded-lg border border-[var(--border)] text-sm bg-[var(--bgSecondary)] text-[var(--textPrimary)] outline-none focus:border-[var(--coral)]">
                    <span class="absolute right-2.5 top-1/2 -translate-y-1/2 text-[11px] text-[var(--textMuted)]">MAD</span>
                </div>
                <div class="relative flex-1">
                    <input type="number" name="max_price" placeholder="Max" x-model="filters.max_price"
                           class="w-full pl-3 pr-10 py-2 rounded-lg border border-[var(--border)] text-sm bg-[var(--bgSecondary)] text-[var(--textPrimary)] outline-none focus:border-[var(--coral)]">
                    <span class="absolute right-2.5 top-1/2 -translate-y-1/2 text-[11px] text-[var(--textMuted)]">MAD</span>
                </div>
            </div>
            <div class="flex items-center justify-between">
                <span class="text-sm text-[var(--textPrimary)]">Gratuit uniquement</span>
                <div class="w-9 h-5 rounded-full relative cursor-pointer transition-colors"
                     :class="filters.free_only ? 'bg-[var(--coral)]' : 'bg-[var(--bgTertiary)]'"
                     @click="filters.free_only = !filters.free_only">
                    <div class="w-4 h-4 rounded-full bg-white absolute top-0.5 transition-all"
                         :class="filters.free_only ? 'left-[18px]' : 'left-[2px]'"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Apply Button --}}
    <div class="p-4 border-t border-[var(--border)] bg-[var(--bgSecondary)] sticky bottom-0 z-10">
        <form action="{{ url()->current() }}" method="GET">
            <template x-for="(val, key) in filters">
                <template x-if="Array.isArray(val)">
                    <template x-for="v in val">
                        <input type="hidden" :name="key + '[]'" :value="v">
                    </template>
                </template>
                <template x-if="!Array.isArray(val) && val !== ''">
                    <input type="hidden" :name="key" :value="val">
                </template>
            </template>
            <button type="submit" 
                    class="w-full py-3.5 bg-[var(--coral)] text-white border-none rounded-xl font-bold text-sm cursor-pointer shadow-lg shadow-[var(--coral)]/20 transition-all hover:-translate-y-0.5 hover:brightness-110">
                Voir les annonces ({{ $resultsCount }})
            </button>
        </form>
    </div>
</aside>
