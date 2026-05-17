@props([
    'label' => null,
    'options' => [],
    'value' => null,
    'multiple' => false,
    'placeholder' => 'Choisir...',
    'icon' => null,
    'searchable' => false,
    'renderType' => 'dropdown',
    'name' => null
])

<div x-data="{ 
    isOpen: false, 
    searchTerm: '', 
    value: @js($value),
    options: @js($options),
    get filteredOptions() {
        if (!this.searchTerm) return this.options;
        return this.options.filter(opt => opt.label.toLowerCase().includes(this.searchTerm.toLowerCase()));
    },
    isSelected(optValue) {
        if (this.multiple) {
            return Array.isArray(this.value) && this.value.includes(optValue);
        }
        return this.value == optValue;
    },
    toggle(optValue) {
        if (this.multiple) {
            if (!Array.isArray(this.value)) this.value = [];
            if (this.value.includes(optValue)) {
                this.value = this.value.filter(v => v != optValue);
            } else {
                this.value.push(optValue);
            }
        } else {
            this.value = optValue;
            this.isOpen = false;
        }
        $dispatch('change', this.value);
    }
}" class="relative w-full">

    @if($label)
        <label class="block text-sm font-semibold mb-2" style="color: var(--textPrimary);">{{ $label }}</label>
    @endif

    @if($renderType === 'dropdown')
        <div @click="isOpen = !isOpen" 
             class="flex items-center gap-2 p-3 rounded-xl border border-[var(--border)] bg-[var(--bgSecondary)] cursor-pointer justify-between">
            <div class="flex items-center gap-2 flex-1 overflow-hidden flex-wrap">
                @if($icon) <span class="text-[var(--textSecondary)] flex items-center">{!! $icon !!}</span> @endif
                
                <template x-if="multiple && Array.isArray(value) && value.length > 0">
                    <div class="flex flex-wrap gap-1">
                        <template x-for="val in value" :key="val">
                            <div class="flex items-center gap-1 px-2 py-1 bg-[var(--bgTertiary)] rounded-md border border-[var(--border)] text-xs text-[var(--textPrimary)]">
                                <span x-text="options.find(o => (o.value || o.id) == val)?.label || val"></span>
                                <i class="ph ph-x cursor-pointer" @click.stop="toggle(val)"></i>
                            </div>
                        </template>
                    </div>
                </template>
                
                <template x-if="!(multiple && Array.isArray(value) && value.length > 0)">
                    <span class="text-sm" :style="value ? 'color: var(--textPrimary)' : 'color: var(--textMuted)'"
                          x-text="options.find(o => (o.value || o.id) == value)?.label || '{{ $placeholder }}'">
                    </span>
                </template>
            </div>
            <i class="ph ph-caret-right text-sm text-[var(--textMuted)] transition-transform" :class="isOpen ? 'rotate-90' : ''"></i>
        </div>

        <div x-show="isOpen" @click.away="isOpen = false"
             class="absolute top-[calc(100%+5px)] left-0 right-0 bg-[var(--bgSecondary)] rounded-xl shadow-lg z-[1000] max-h-[300px] overflow-y-auto border border-[var(--border)]"
             x-transition>
            @if($searchable)
                <div class="p-2 border-b border-[var(--filterBorder)] sticky top-0 bg-[var(--bgSecondary)]">
                    <div class="relative">
                        <i class="ph ph-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-[var(--textMuted)]"></i>
                        <input type="text" x-model="searchTerm" placeholder="Rechercher..."
                               class="w-full pl-9 pr-3 py-2 rounded-lg border border-[var(--border)] text-sm bg-[var(--bgTertiary)] outline-none focus:border-[var(--coral)]">
                    </div>
                </div>
            @endif

            <div class="p-1">
                <template x-for="opt in filteredOptions" :key="opt.id || opt.value">
                    <div @click="toggle(opt.value || opt.id)"
                         class="flex items-center justify-between p-3 rounded-lg cursor-pointer transition-colors hover:bg-[var(--bgTertiary)]"
                         :class="isSelected(opt.value || opt.id) ? 'bg-[var(--bgTertiary)]' : ''">
                        <div class="flex items-center gap-3">
                            <template x-if="opt.icon">
                                <span x-html="opt.icon"></span>
                            </template>
                            <span class="text-sm" :class="isSelected(opt.value || opt.id) ? 'font-semibold text-[var(--coral)]' : 'text-[var(--textPrimary)]'" x-text="opt.label"></span>
                        </div>
                        <template x-if="isSelected(opt.value || opt.id)">
                            <i class="ph ph-check text-[var(--coral)]"></i>
                        </template>
                    </div>
                </template>
            </div>
        </div>
    @elseif($renderType === 'pills')
        <div class="flex flex-wrap gap-2">
            <template x-for="opt in options" :key="opt.id || opt.value">
                <button type="button" @click="toggle(opt.value || opt.id)"
                        class="px-3 py-1.5 rounded-full border text-xs font-semibold transition-all"
                        :class="isSelected(opt.value || opt.id) ? 'bg-[var(--coral)] border-transparent text-white' : 'bg-[var(--bgSecondary)] border-[var(--border)] text-[var(--textSecondary)]'"
                        x-text="opt.label">
                </button>
            </template>
        </div>
    @endif

    @if($name)
        <input type="hidden" name="{{ $name }}" :value="multiple ? JSON.stringify(value) : value">
    @endif
</div>
