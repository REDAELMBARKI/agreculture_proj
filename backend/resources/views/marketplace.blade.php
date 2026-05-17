@extends('layouts.main')

@section('title', 'Marketplace - LetUsDonate')

@section('content')
<div x-data="{ 
    filters: {
        search: '{{ request('search', '') }}',
        category: '{{ request('category', '') }}',
        cities: @json(request('cities', [])),
        mode: @json(request('mode', [])),
        age_range: @json(request('age_range', [])),
        gender: '{{ request('gender', '') }}',
        condition: '{{ request('condition', '') }}',
        min_price: '{{ request('min_price', '') }}',
        max_price: '{{ request('max_price', '') }}',
        sizes: @json(request('sizes', [])),
        free_only: {{ request('free_only', false) ? 'true' : 'false' }},
        with_media: {{ request('with_media', false) ? 'true' : 'false' }},
        sort: '{{ request('sort', 'newest') }}',
        view: '{{ request('view', 'grid') }}'
    },
    initData: @json($initData),
    sizeTab: 'clothes',
    loading: false,
    listingsLoading: false,
    
    handleFilterChange(key, value) {
        this.filters[key] = value;
        this.applyFilters();
    },
    
    handleToggleArrayFilter(key, value) {
        if (!Array.isArray(this.filters[key])) this.filters[key] = [];
        if (this.filters[key].includes(value)) {
            this.filters[key] = this.filters[key].filter(v => v !== value);
        } else {
            this.filters[key].push(value);
        }
        this.applyFilters();
    },
    
    handleReset() {
        this.filters = {
            search: '',
            category: '',
            cities: [],
            mode: [],
            age_range: [],
            gender: '',
            condition: '',
            min_price: '',
            max_price: '',
            sizes: [],
            free_only: false,
            with_media: false,
            sort: 'newest',
            view: this.filters.view
        };
        this.applyFilters();
    },
    
    applyFilters() {
        this.listingsLoading = true;
        const params = new URLSearchParams();
        Object.keys(this.filters).forEach(key => {
            if (Array.isArray(this.filters[key])) {
                this.filters[key].forEach(v => params.append(key + '[]', v));
            } else if (this.filters[key] !== '' && this.filters[key] !== null) {
                params.append(key, this.filters[key]);
            }
        });
        window.location.href = '{{ route('marketplace') }}?' + params.toString();
    }
}" style="display: flex; background-color: var(--bgPrimary); min-height: 100vh; font-family: 'Poppins', sans-serif;">

    <!-- --- Sidebar --- -->
    <aside style="width: 280px; border-right: 1px solid var(--sidebarBorder); background-color: var(--bgSecondary); height: calc(100vh - 80px); position: sticky; top: 80px; display: flex; flexDirection: column; zIndex: 10;">
        <!-- Top Row — Clear Only -->
        <div style="padding: 10px 16px; backgroundColor: var(--filterBg); border-bottom: 1px solid var(--filterBorder); display: flex; alignItems: center; justifyContent: flex-end; flexShrink: 0;">
            <button @click="handleReset" style="background: none; border: none; color: var(--textSecondary); fontSize: 12px; cursor: pointer; padding: 0;">Effacer tout</button>
        </div>

        <!-- Scrollable Content -->
        <div class="custom-scrollbar" style="padding: 12px 16px; overflow-y: auto; flex: 1; scrollbarWidth: thin; scrollbarColor: var(--coral) var(--scrollbarTrack);">
            
            <!-- Catégorie -->
            <div style="margin-bottom: 20px;">
                <div style="fontSize: 12px; fontWeight: 600; color: var(--textSecondary); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px;">Catégorie</div>
                <div style="position: relative;">
                    <i class="ph-bold ph-shopping-bag" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: var(--iconCoral); z-index: 1;"></i>
                    <select x-model="filters.category" @change="applyFilters()" style="width: 100%; padding: 10px 15px 10px 40px; border-radius: 12px; border: 1px solid var(--border); background-color: var(--bgSecondary); color: var(--textPrimary); fontSize: 14px; outline: none; appearance: none;">
                        <option value="">Choisir catégorie</option>
                        <template x-for="cat in initData.categories" :key="cat.id">
                            <option :value="cat.id" x-text="cat.label"></option>
                        </template>
                    </select>
                </div>
            </div>

            <!-- Ville - Secteur -->
            <div style="margin-bottom: 20px;">
                <div style="fontSize: 12px; fontWeight: 600; color: var(--textSecondary); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px;">Ville - Secteur</div>
                <div style="position: relative;">
                    <i class="ph-bold ph-map-pin" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: var(--iconCoral); z-index: 1;"></i>
                    <select @change="handleToggleArrayFilter('cities', $event.target.value); $event.target.value = ''" style="width: 100%; padding: 10px 15px 10px 40px; border-radius: 12px; border: 1px solid var(--border); background-color: var(--bgSecondary); color: var(--textPrimary); fontSize: 14px; outline: none; appearance: none;">
                        <option value="">Toutes les villes</option>
                        <template x-for="city in initData.cities" :key="city.id">
                            <option :value="city.id" x-text="city.label"></option>
                        </template>
                    </select>
                </div>
                <!-- Selected city pills -->
                <div style="display: flex; flex-wrap: wrap; gap: 6px; margin-top: 8px;">
                    <template x-for="cityId in filters.cities" :key="cityId">
                        <div style="display: flex; alignItems: center; gap: 4px; padding: 4px 8px; border-radius: 6px; border: 1px solid var(--infoText); background-color: var(--infoBg); color: var(--infoText); fontSize: 11px; fontWeight: 600;">
                            <span x-text="initData.cities.find(c => c.id == cityId)?.label"></span>
                            <i class="ph ph-x" style="cursor: pointer;" @click="handleToggleArrayFilter('cities', cityId)"></i>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Type d'annonce -->
            <div style="margin-bottom: 20px;">
                <div style="fontSize: 12px; fontWeight: 600; color: var(--textSecondary); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px;">Type d'annonce</div>
                <div style="display: flex; flex-direction: column; gap: 10px;">
                    <template x-for="type in initData.listingTypes" :key="type.value">
                        <div @click="handleToggleArrayFilter('mode', type.value)" style="display: flex; alignItems: center; justifyContent: space-between; cursor: pointer;">
                            <div style="display: flex; alignItems: center; gap: 10px;">
                                <i :class="type.value === 'sell' ? 'ph-bold ph-storefront' : 'ph-bold ph-gift'" :style="'font-size: 20px; color: ' + (filters.mode.includes(type.value) ? 'var(--coral)' : 'var(--iconMuted)')"></i>
                                <span style="fontSize: 14px; color: var(--textPrimary);" x-text="type.label"></span>
                            </div>
                            <div :style="'width: 20px; height: 20px; border-radius: 4px; border: 1px solid ' + (filters.mode.includes(type.value) ? 'var(--coral)' : 'var(--border)') + '; backgroundColor: ' + (filters.mode.includes(type.value) ? 'var(--coral)' : 'transparent') + '; display: flex; alignItems: center; justifyContent: center;'">
                                <i x-show="filters.mode.includes(type.value)" class="ph ph-check" style="color: white; fontSize: 14px;"></i>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Tranche d'âge -->
            <div style="margin-bottom: 20px;">
                <div style="fontSize: 12px; fontWeight: 600; color: var(--textSecondary); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px;">Tranche d'âge</div>
                <div style="display: flex; flex-wrap: wrap; gap: 8px;">
                    <template x-for="age in initData.ageRanges" :key="age.value">
                        <button @click="handleToggleArrayFilter('age_range', age.value)" 
                                :style="'padding: 6px 12px; border-radius: 20px; border: ' + (filters.age_range.includes(age.value) ? 'none' : '1px solid var(--border)') + '; backgroundColor: ' + (filters.age_range.includes(age.value) ? 'var(--coral)' : 'var(--bgSecondary)') + '; color: ' + (filters.age_range.includes(age.value) ? 'var(--bgSecondary)' : 'var(--textSecondary)') + '; fontSize: 12px; fontWeight: 600; cursor: pointer;'">
                            <span x-text="age.label"></span>
                        </button>
                    </template>
                </div>
            </div>

            <!-- Genre -->
            <div style="margin-bottom: 20px;">
                <div style="fontSize: 12px; fontWeight: 600; color: var(--textSecondary); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px;">Genre</div>
                <div style="display: flex; gap: 8px;">
                    <template x-for="g in ['girl', 'boy', 'both']" :key="g">
                        <button @click="filters.gender = (filters.gender === g ? '' : g); applyFilters()" 
                                :style="'flex: 1; padding: 8px 0; border-radius: 10px; border: ' + (filters.gender === g ? 'none' : '1px solid var(--border)') + '; backgroundColor: ' + (filters.gender === g ? 'var(--coral)' : 'var(--bgSecondary)') + '; color: ' + (filters.gender === g ? 'var(--bgSecondary)' : 'var(--textSecondary)') + '; fontSize: 12px; fontWeight: 600; cursor: pointer; display: flex; flex-direction: column; alignItems: center; gap: 4px;'">
                            <i :class="g === 'both' ? 'ph-bold ph-users' : 'ph-bold ph-user'" :style="'font-size: 18px; color: ' + (filters.gender === g ? 'var(--bgSecondary)' : 'var(--iconMuted)')"></i>
                            <span x-text="g === 'girl' ? 'Fille' : (g === 'boy' ? 'Garçon' : 'Mixte')"></span>
                        </button>
                    </template>
                </div>
            </div>

            <!-- Taille -->
            <div style="margin-bottom: 20px;">
                <div style="fontSize: 12px; fontWeight: 600; color: var(--textSecondary); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px;">Taille</div>
                <div style="display: flex; border-bottom: 1px solid var(--border); marginBottom: 12px;">
                    <button @click="sizeTab = 'clothes'" :style="'flex: 1; padding: 8px 0; background: none; border: none; border-bottom: ' + (sizeTab === 'clothes' ? '2px solid var(--coral)' : 'none') + '; color: ' + (sizeTab === 'clothes' ? 'var(--coral)' : 'var(--textSecondary)') + '; fontWeight: 700; fontSize: 13px; cursor: pointer;'">Vêtements</button>
                    <button @click="sizeTab = 'shoes'" :style="'flex: 1; padding: 8px 0; background: none; border: none; border-bottom: ' + (sizeTab === 'shoes' ? '2px solid var(--coral)' : 'none') + '; color: ' + (sizeTab === 'shoes' ? 'var(--coral)' : 'var(--textSecondary)') + '; fontWeight: 700; fontSize: 13px; cursor: pointer;'">Chaussures</button>
                </div>
                <div style="display: flex; flex-wrap: wrap; gap: 6px;">
                    <template x-if="sizeTab === 'clothes'">
                        <template x-for="s in initData.clothingSizes" :key="s.value">
                            <button @click="handleToggleArrayFilter('sizes', s.value)" 
                                    :style="'padding: 4px 10px; border-radius: 15px; border: ' + (filters.sizes.includes(s.value) ? 'none' : '1px solid var(--border)') + '; backgroundColor: ' + (filters.sizes.includes(s.value) ? 'var(--coral)' : 'var(--bgSecondary)') + '; color: ' + (filters.sizes.includes(s.value) ? 'var(--bgSecondary)' : 'var(--textSecondary)') + '; fontSize: 11px; fontWeight: 600; cursor: pointer;'">
                                <span x-text="s.label"></span>
                            </button>
                        </template>
                    </template>
                    <template x-if="sizeTab === 'shoes'">
                        <template x-for="s in initData.shoeSizes" :key="s.value">
                            <button @click="handleToggleArrayFilter('sizes', s.value)" 
                                    :style="'padding: 4px 10px; border-radius: 15px; border: ' + (filters.sizes.includes(s.value) ? 'none' : '1px solid var(--border)') + '; backgroundColor: ' + (filters.sizes.includes(s.value) ? 'var(--coral)' : 'var(--bgSecondary)') + '; color: ' + (filters.sizes.includes(s.value) ? 'var(--bgSecondary)' : 'var(--textSecondary)') + '; fontSize: 11px; fontWeight: 600; cursor: pointer;'">
                                <span x-text="s.label"></span>
                            </button>
                        </template>
                    </template>
                </div>
            </div>

            <!-- État -->
            <div style="margin-bottom: 20px;">
                <div style="fontSize: 12px; fontWeight: 600; color: var(--textSecondary); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px;">État</div>
                <div style="display: flex; flex-direction: column; gap: 4px;">
                    <template x-for="cond in initData.conditions" :key="cond.value">
                        <div @click="filters.condition = (filters.condition === cond.value ? '' : cond.value); applyFilters()" 
                             :style="'display: flex; alignItems: center; gap: 10px; padding: 10px 12px; cursor: pointer; borderRadius: 0 8px 8px 0; border-left: ' + (filters.condition === cond.value ? '3px solid var(--coral)' : '3px solid transparent') + '; backgroundColor: ' + (filters.condition === cond.value ? 'var(--coralLight)' : 'transparent') + '; transition: all 0.2s;'">
                            <div :style="'width: 8px; height: 8px; border-radius: 50%; backgroundColor: ' + cond.color"></div>
                            <span :style="'fontSize: 13px; color: ' + (filters.condition === cond.value ? 'var(--coral)' : 'var(--textPrimary)') + '; fontWeight: ' + (filters.condition === cond.value ? '600' : '400')" x-text="cond.label"></span>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Prix -->
            <div style="margin-bottom: 20px;">
                <div style="fontSize: 12px; fontWeight: 600; color: var(--textSecondary); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px;">Prix</div>
                <div style="display: flex; gap: 10px; alignItems: center; marginBottom: 12px;">
                    <div style="position: relative; flex: 1;">
                        <input type="number" placeholder="Min" x-model="filters.min_price" @change="applyFilters()" style="width: 100%; padding: 8px 40px 8px 12px; border-radius: 8px; border: 1px solid var(--border); fontSize: 13px; backgroundColor: var(--bgSecondary); color: var(--textPrimary);">
                        <span style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); fontSize: 11px; color: var(--textMuted);">MAD</span>
                    </div>
                    <div style="position: relative; flex: 1;">
                        <input type="number" placeholder="Max" x-model="filters.max_price" @change="applyFilters()" style="width: 100%; padding: 8px 40px 8px 12px; border-radius: 8px; border: 1px solid var(--border); fontSize: 13px; backgroundColor: var(--bgSecondary); color: var(--textPrimary);">
                        <span style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); fontSize: 11px; color: var(--textMuted);">MAD</span>
                    </div>
                </div>
                <div style="display: flex; alignItems: center; justifyContent: space-between;">
                    <span style="fontSize: 13px; color: var(--textPrimary);">Gratuit uniquement</span>
                    <div @click="filters.free_only = !filters.free_only; applyFilters()" style="width: 36px; height: 20px; border-radius: 10px; position: relative; cursor: pointer; transition: background-color 0.2s;" :style="'backgroundColor: ' + (filters.free_only ? 'var(--coral)' : 'var(--bgTertiary)')">
                        <div style="width: 16px; height: 16px; border-radius: 50%; backgroundColor: var(--bgSecondary); position: absolute; top: 2px; transition: left 0.2s;" :style="'left: ' + (filters.free_only ? '18px' : '2px')"></div>
                    </div>
                </div>
            </div>

            <!-- Toggle Switches (bottom) -->
            <div style="margin-bottom: 20px; display: flex; flexDirection: column; gap: 12px;">
                <div style="display: flex; alignItems: center; gap: 12px;">
                    <div style="backgroundColor: var(--darkNavy); color: var(--bgSecondary); padding: 6px; border-radius: 8px; display: flex; alignItems: center; justifyContent: center;">
                        <i class="ph ph-camera"></i>
                    </div>
                    <span style="flex: 1; fontSize: 12px; color: var(--textPrimary);">Annonces avec photos-vidéos uniquement</span>
                    <div @click="filters.with_media = !filters.with_media; applyFilters()" style="width: 36px; height: 20px; border-radius: 10px; position: relative; cursor: pointer; transition: background-color 0.2s;" :style="'backgroundColor: ' + (filters.with_media ? 'var(--coral)' : 'var(--bgTertiary)')">
                        <div style="width: 16px; height: 16px; border-radius: 50%; backgroundColor: var(--bgSecondary); position: absolute; top: 2px; transition: left 0.2s;" :style="'left: ' + (filters.with_media ? '18px' : '2px')"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Apply Button -->
        <div style="padding: 16px; border-top: 1px solid var(--border); backgroundColor: var(--bgSecondary); position: sticky; bottom: 0; zIndex: 5;">
            <button @click="applyFilters()" style="width: 100%; padding: 14px; backgroundColor: var(--coral); color: var(--bgSecondary); border: none; border-radius: 12px; fontWeight: 700; fontSize: 14px; cursor: pointer; transition: all 0.2s;">
                Voir les annonces ({{ $listings->total() }})
            </button>
        </div>
    </aside>

    <!-- --- Main Content --- -->
    <div style="flex: 1; display: flex; flex-direction: column;">
        
        <!-- --- Top Bar --- -->
        <div style="position: sticky; top: 80px; zIndex: 5; backgroundColor: var(--bgPrimary); padding: 20px 40px; display: flex; alignItems: center; justifyContent: space-between; border-bottom: 1px solid var(--sidebarBorder);">
            <div style="display: flex; alignItems: center; gap: 20px;">
                <h1 style="fontSize: 24px; fontWeight: 800; color: var(--textPrimary); margin: 0;">Marketplace</h1>
                
                <div style="display: flex; alignItems: center; gap: 10px;">
                    <div style="position: relative; width: 300px;">
                        <i class="ph ph-magnifying-glass" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: var(--textMuted); z-index: 1;"></i>
                        <input type="text" placeholder="Rechercher un article..." x-model="filters.search" @keyup.enter="applyFilters()" style="width: 100%; padding: 10px 15px 10px 40px; border-radius: 12px; border: 1px solid var(--border); backgroundColor: var(--bgSecondary); color: var(--textPrimary); fontSize: 14px; outline: none;">
                    </div>

                    <div style="width: 200px; position: relative;">
                        <i class="ph-bold ph-map-pin" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: var(--iconCoral); z-index: 1;"></i>
                        <select @change="handleToggleArrayFilter('cities', $event.target.value); $event.target.value = ''" style="width: 100%; padding: 10px 15px 10px 40px; border-radius: 12px; border: 1px solid var(--border); background-color: var(--bgSecondary); color: var(--textPrimary); fontSize: 14px; outline: none; appearance: none;">
                            <option value="">Toutes les villes</option>
                            <template x-for="city in initData.cities" :key="city.id">
                                <option :value="city.id" x-text="city.label"></option>
                            </template>
                        </select>
                    </div>
                </div>
            </div>

            <div style="display: flex; alignItems: center; gap: 15px;">
                <div style="display: flex; backgroundColor: var(--bgTertiary); padding: 4px; border-radius: 8px;">
                    <button @click="filters.view = 'grid'; applyFilters()" :style="'padding: 6px; border-radius: 6px; border: none; cursor: pointer; backgroundColor: ' + (filters.view === 'grid' ? 'var(--bgSecondary)' : 'transparent') + '; color: ' + (filters.view === 'grid' ? 'var(--coral)' : 'var(--textMuted)')">
                        <i class="ph ph-squares-four" style="fontSize: 18px;"></i>
                    </button>
                    <button @click="filters.view = 'list'; applyFilters()" :style="'padding: 6px; border-radius: 6px; border: none; cursor: pointer; backgroundColor: ' + (filters.view === 'list' ? 'var(--bgSecondary)' : 'transparent') + '; color: ' + (filters.view === 'list' ? 'var(--coral)' : 'var(--textMuted)')">
                        <i class="ph ph-list" style="fontSize: 18px;"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- --- Listings Grid --- -->
        <div style="padding: 30px 40px; flex: 1;">
            <div x-show="listingsLoading" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 25px;">
                <template x-for="i in [1,2,3,4,5,6,7,8]" :key="i">
                    <div style="height: 380px; backgroundColor: var(--bgSecondary); border-radius: 20px; animation: pulse 1.5s infinite;"></div>
                </template>
            </div>
            
            <div x-show="!listingsLoading">
                @if($listings->count() > 0)
                    <div :style="'display: grid; gap: 25px; grid-template-columns: ' + (filters.view === 'grid' ? 'repeat(auto-fill, minmax(280px, 1fr))' : '1fr')">
                        @foreach($listings as $product)
                            <x-marketplace-card :product="$product" :view="request('view', 'grid')" />
                        @endforeach
                    </div>
                    
                    <div style="margin-top: 40px;">
                        {{ $listings->appends(request()->query())->links() }}
                    </div>
                @else
                    <div style="text-align: center; padding: 100px 20px; backgroundColor: var(--bgSecondary); border-radius: 24px; box-shadow: 0 4px 12px rgba(0,0,0,0.03);">
                        <i class="ph-bold ph-shopping-bag" style="fontSize: 64px; color: var(--textMuted); marginBottom: 20px;"></i>
                        <h2 style="fontSize: 24px; fontWeight: 800; color: var(--textPrimary); marginBottom: 10px;">Aucun article trouvé</h2>
                        <p style="color: var(--textSecondary); marginBottom: 30px;">Essayez d'ajuster vos filtres pour trouver ce que vous cherchez.</p>
                        <button @click="handleReset" style="padding: 12px 30px; backgroundColor: var(--coral); color: var(--bgSecondary); border: none; border-radius: 12px; fontWeight: 700; cursor: pointer;">Réinitialiser les filtres</button>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
