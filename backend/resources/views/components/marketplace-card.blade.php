@props(['product', 'view' => 'grid'])

@php
    $displayName = $product->user->name ?? 'User';
    $initials = collect(explode(' ', $displayName))->map(fn($n) => strtoupper(substr($n, 0, 1)))->take(2)->join('');
    
    $hash = 0;
    foreach (str_split($displayName) as $char) {
        $hash = ord($char) + (($hash << 5) - $hash);
    }
    $h = abs($hash) % 360;
    $avatarColor = "hsl($h, 65%, 45%)";

    $thumbnailUrl = $product->thumbnail ? (str_starts_with($product->thumbnail->url, 'http') ? $product->thumbnail->url : asset('storage/' . str_replace('public/', '', $product->thumbnail->file_path))) : asset('images/placeholder.png');
    
    $gallery = $product->gallery ?? [];
    $allImages = collect([$thumbnailUrl]);
    foreach($gallery as $img) {
        $allImages->push(str_starts_with($img->url ?? '', 'http') ? $img->url : asset('storage/' . str_replace('public/', '', $img->file_path ?? $img->path)));
    }
    $allImages = $allImages->filter()->values();

    $isFree = !$product->price || $product->price == 0;
    $priceDisplay = $isFree ? 'GRATUIT' : number_format($product->price) . ' MAD';
    $priceColor = $isFree ? 'var(--success)' : 'var(--coral)';
    $modeLabel = $isFree ? 'GRATUIT' : 'À VENDRE';
    
    $badgeBg = $product->listing_mode === 'sell' ? 'var(--primary)' : 'var(--success)';
@endphp

@if($view === 'list')
    <div onclick="window.location.href='/announcements/{{ $product->slug }}'" 
         style="display: flex; background-color: var(--bgSecondary); border-radius: 20px; overflow: hidden; text-decoration: none; color: inherit; box-shadow: 0 4px 12px rgba(0,0,0,0.04); transition: transform 0.2s; cursor: pointer;">
        <div style="width: 280px; height: 210px; position: relative; flex-shrink: 0;">
            <img src="{{ $allImages[0] }}" alt="{{ $product->title }}" style="width: 100%; height: 100%; object-fit: cover;">
            <div style="position: absolute; top: 15px; left: 15px; padding: 5px 12px; border-radius: 8px; background-color: {{ $badgeBg }}; color: var(--bgSecondary); font-size: 11px; font-weight: 900;">
                {{ $modeLabel }}
            </div>
        </div>
        <div style="padding: 25px; flex: 1; display: flex; flex-direction: column;">
            <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                <div style="display: flex; align-items: center; gap: 8px;">
                    <div style="width: 32px; height: 32px; border-radius: 50%; background-color: {{ $avatarColor }}; color: var(--bgSecondary); display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 800;">
                        {{ $initials }}
                    </div>
                    <span style="font-size: 14px; font-weight: 700; color: var(--textPrimary);">{{ $displayName }}</span>
                </div>
                <button style="background: none; border: none; cursor: pointer; padding: 5px;">
                    <i class="ph ph-heart" style="font-size: 20px; color: var(--coral);"></i>
                </button>
            </div>
            <h3 style="font-size: 20px; font-weight: 800; color: var(--textPrimary); margin-bottom: 12px;">{{ $product->title }}</h3>
            <div style="display: flex; gap: 10px; margin-bottom: 20px;">
                <span style="padding: 5px 12px; background-color: var(--bgTertiary); border-radius: 8px; font-size: 12px; font-weight: 600; color: var(--textSecondary);">{{ $product->condition }}</span>
                <span style="padding: 5px 12px; background-color: var(--bgTertiary); border-radius: 8px; font-size: 12px; font-weight: 600; color: var(--textSecondary);">{{ $product->age_range }}</span>
            </div>
            <div style="margin-top: auto; display: flex; justify-content: space-between; align-items: center;">
                <div style="font-size: 24px; font-weight: 900; color: {{ $priceColor }};">
                    {{ $priceDisplay }}
                </div>
                <div style="display: flex; align-items: center; gap: 15px;">
                    <div style="display: flex; align-items: center; gap: 6px; color: var(--textMuted); font-size: 13px;">
                        <i class="ph-bold ph-map-pin" style="font-size: 14px;"></i> Agadir, Maroc
                    </div>
                    <button style="padding: 8px 20px; background-color: var(--coral); color: var(--bgSecondary); border-radius: 12px; font-weight: 700; border: none; cursor: pointer; font-size: 14px; display: flex; align-items: center; gap: 8px;">
                        <i class="ph-bold ph-chat-line"></i> Contact
                    </button>
                </div>
            </div>
        </div>
    </div>
@else
    <div x-data="{ 
            currentImage: 0, 
            images: @json($allImages),
            isHovered: false
         }"
         @mouseenter="isHovered = true"
         @mouseleave="isHovered = false"
         onclick="if(!event.target.closest('button')) window.location.href='/announcements/{{ $product->slug }}'"
         style="background-color: var(--bgSecondary); border-radius: 20px; overflow: hidden; text-decoration: none; color: inherit; box-shadow: 0 4px 12px rgba(0,0,0,0.05); transition: all 0.3s ease; display: flex; flex-direction: column; position: relative; cursor: pointer;">
        
        {{-- Seller Row --}}
        <div style="padding: 12px 15px; display: flex; align-items: center; justify-content: space-between;">
            <div style="display: flex; align-items: center; gap: 8px;">
                <div style="width: 28px; height: 28px; border-radius: 50%; background-color: {{ $avatarColor }}; color: var(--bgSecondary); display: flex; align-items: center; justify-content: center; font-size: 10px; font-weight: 800;">
                    {{ $initials }}
                </div>
                <div style="display: flex; flex-direction: column;">
                    <span style="font-size: 12px; font-weight: 700; color: var(--textPrimary);">{{ $displayName }}</span>
                    <span style="font-size: 10px; color: var(--textMuted);">il y a 2h</span>
                </div>
            </div>
            <button style="padding: 6px 12px; background-color: var(--coral); color: var(--bgSecondary); border-radius: 10px; font-weight: 700; border: none; cursor: pointer; font-size: 11px; display: flex; align-items: center; gap: 4px; min-width: auto; height: 30px;">
                <i class="ph-bold ph-chat-line"></i> Contact
            </button>
        </div>

        {{-- Image Carousel --}}
        <div style="position: relative; height: 200px; background-color: var(--bgTertiary);">
            <template x-for="(img, index) in images" :key="index">
                <img x-show="currentImage === index" :src="img" alt="{{ $product->title }}" style="width: 100%; height: 100%; object-fit: cover; position: absolute; top: 0; left: 0;">
            </template>
            
            <div style="position: absolute; top: 12px; left: 12px; padding: 5px 10px; border-radius: 8px; background-color: {{ $badgeBg }}; color: var(--bgSecondary); font-size: 10px; font-weight: 900; z-index: 2;">
                {{ $modeLabel }}
            </div>
            
            <button @click.stop="/* favorite logic */" style="position: absolute; top: 10px; right: 10px; background: none; border: none; display: flex; align-items: center; justify-content: center; cursor: pointer; padding: 5px; z-index: 2;">
                <i class="ph ph-heart" style="font-size: 18px; color: var(--coral);"></i>
            </button>

            <template x-if="images.length > 1">
                <div>
                    <div style="position: absolute; bottom: 10px; left: 10px; padding: 4px 8px; border-radius: 6px; background-color: rgba(0,0,0,0.5); color: var(--bgSecondary); font-size: 10px; font-weight: 600; z-index: 2; display: flex; align-items: center; gap: 4px;">
                        <i class="ph ph-camera"></i> <span x-text="(currentImage + 1) + '/' + images.length"></span>
                    </div>
                    <div x-show="isHovered">
                        <button @click.stop="currentImage = (currentImage - 1 + images.length) % images.length" style="position: absolute; left: 8px; top: 50%; transform: translateY(-50%); background-color: rgba(255,255,255,0.9); border: none; border-radius: 50%; width: 30px; height: 30px; display: flex; align-items: center; justify-content: center; cursor: pointer; z-index: 3;">
                            <i class="ph ph-caret-left" style="font-size: 18px;"></i>
                        </button>
                        <button @click.stop="currentImage = (currentImage + 1) % images.length" style="position: absolute; right: 8px; top: 50%; transform: translateY(-50%); background-color: rgba(255,255,255,0.9); border: none; border-radius: 50%; width: 30px; height: 30px; display: flex; align-items: center; justify-content: center; cursor: pointer; z-index: 3;">
                            <i class="ph ph-caret-right" style="font-size: 18px;"></i>
                        </button>
                    </div>
                </div>
            </template>
        </div>

        {{-- Info Section --}}
        <div style="padding: 15px; flex: 1; display: flex; flex-direction: column;">
            <div style="display: flex; align-items: center; gap: 4px; color: var(--textMuted); font-size: 11px; margin-bottom: 6px;">
                <i class="ph-bold ph-map-pin" style="font-size: 12px;"></i> <span>Agadir, Maroc</span>
            </div>
            <h3 style="font-size: 15px; font-weight: 800; color: var(--textPrimary); margin-bottom: 10px; height: 40px; overflow: hidden;">{{ $product->title }}</h3>
            <div style="display: flex; gap: 6px; margin-bottom: 15px;">
                <span style="font-size: 10px; padding: 4px 8px; background-color: var(--bgTertiary); border-radius: 6px; color: var(--textSecondary); font-weight: 700;">{{ $product->condition }}</span>
                <span style="font-size: 10px; padding: 4px 8px; background-color: var(--bgTertiary); border-radius: 6px; color: var(--textSecondary); font-weight: 700;">{{ $product->age_range }}</span>
            </div>
            <div style="display: flex; align-items: center; justify-content: space-between; margin-top: auto;">
                <div style="font-size: 20px; font-weight: 900; color: {{ $priceColor }};">
                    {{ $priceDisplay }}
                </div>
                <div style="width: 24px; height: 24px; border-radius: 50%; background-color: var(--coral); color: var(--bgSecondary); display: flex; align-items: center; justify-content: center; font-size: 10px; font-weight: 800;">
                    {{ substr($displayName, 0, 1) }}
                </div>
            </div>
        </div>
    </div>
@endif
