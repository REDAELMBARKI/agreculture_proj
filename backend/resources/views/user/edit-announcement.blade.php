@extends('layouts.main')

@section('title', 'Edit Item')

@section('content')
<div class="min-h-screen bg-[var(--bgPrimary)] py-12 px-4">
    <div class="max-w-4xl mx-auto">
        <div class="mb-8 flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-[var(--textPrimary)]">Edit Item</h1>
                <p class="text-[var(--textSecondary)]">Update your listing details.</p>
            </div>
            <a href="{{ route('user.listings') }}" class="text-[var(--textSecondary)] hover:text-[var(--textPrimary)] font-semibold flex items-center gap-2">
                <i class="ph ph-arrow-left"></i>
                Back to Listings
            </a>
        </div>

        @if($errors->any())
        <div class="mb-8 p-4 bg-red-100 border border-red-200 text-red-700 rounded-2xl">
            <ul class="list-disc list-inside text-sm">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form action="{{ route('user.announcements.update', $announcement->slug) }}" method="POST" enctype="multipart/form-data" class="space-y-8">
            @csrf
            @method('PUT')

            <!-- Step 1: Category -->
            <div class="bg-white p-8 rounded-3xl shadow-sm border border-[var(--border)]">
                <h3 class="text-xl font-bold text-[var(--textPrimary)] mb-6 flex items-center gap-2">
                    <span class="w-8 h-8 bg-[var(--primary)] text-white rounded-full flex items-center justify-center text-sm">1</span>
                    Select Category
                </h3>
                
                <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                    @foreach($categories as $category)
                    <label class="cursor-pointer">
                        <input type="radio" name="super_category_id" value="{{ $category['id'] }}" class="peer hidden" required 
                               {{ (old('super_category_id') ?? $announcement->super_category_id) == $category['id'] ? 'checked' : '' }}>
                        <div class="p-6 border-2 border-[var(--border)] rounded-2xl peer-checked:border-[var(--primary)] peer-checked:bg-[var(--bgSecondary)] transition-all text-center h-full flex flex-col items-center justify-center">
                            <i class="ph ph-{{ $category['icon'] ?: 'package' }} text-3xl mb-2 block text-[var(--textSecondary)] peer-checked:text-[var(--primary)]"></i>
                            <span class="font-bold text-[var(--textPrimary)] text-sm">{{ $category['label'] }}</span>
                        </div>
                    </label>
                    @endforeach
                </div>
            </div>

            <!-- Step 2: Basic Info -->
            <div class="bg-white p-8 rounded-3xl shadow-sm border border-[var(--border)]">
                <h3 class="text-xl font-bold text-[var(--textPrimary)] mb-6 flex items-center gap-2">
                    <span class="w-8 h-8 bg-[var(--primary)] text-white rounded-full flex items-center justify-center text-sm">2</span>
                    Basic Information
                </h3>

                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-bold text-[var(--textPrimary)] mb-2">Title</label>
                        <input type="text" name="title" required value="{{ old('title') ?? $announcement->title }}" placeholder="What are you selling?" class="w-full px-4 py-3 rounded-xl border border-[var(--border)] focus:border-[var(--primary)] outline-none transition-colors">
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-[var(--textPrimary)] mb-2">Description</label>
                        <textarea name="description" rows="4" placeholder="Describe your item..." class="w-full px-4 py-3 rounded-xl border border-[var(--border)] focus:border-[var(--primary)] outline-none transition-colors">{{ old('description') ?? $announcement->description }}</textarea>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-bold text-[var(--textPrimary)] mb-2">Price</label>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-[var(--textSecondary)]">MAD</span>
                                <input type="number" name="price" step="0.01" value="{{ old('price') ?? $announcement->price }}" required class="w-full pl-14 pr-4 py-3 rounded-xl border border-[var(--border)] focus:border-[var(--primary)] outline-none transition-colors">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-[var(--textPrimary)] mb-2">Condition</label>
                            <select name="condition" class="w-full px-4 py-3 rounded-xl border border-[var(--border)] focus:border-[var(--primary)] outline-none transition-colors">
                                @foreach(['new' => 'New', 'like_new' => 'Like New', 'good' => 'Good', 'fair' => 'Fair'] as $value => $label)
                                    <option value="{{ $value }}" {{ (old('condition') ?? $announcement->condition) == $value ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Step 3: Photos -->
            <div class="bg-white p-8 rounded-3xl shadow-sm border border-[var(--border)]">
                <h3 class="text-xl font-bold text-[var(--textPrimary)] mb-6 flex items-center gap-2">
                    <span class="w-8 h-8 bg-[var(--primary)] text-white rounded-full flex items-center justify-center text-sm">3</span>
                    Update Photos
                </h3>
                
                @if($announcement->gallery->count() > 0 || $announcement->thumbnail)
                    <div class="flex gap-4 mb-6 overflow-x-auto pb-4">
                        @if($announcement->thumbnail)
                            <div class="relative w-24 h-24 shrink-0">
                                <img src="{{ $announcement->thumbnail->url }}" class="w-full h-full object-cover rounded-xl border border-[var(--border)]">
                                <span class="absolute top-0 right-0 bg-[var(--primary)] text-white text-[10px] px-1 rounded-bl-lg">Cover</span>
                            </div>
                        @endif
                        @foreach($announcement->gallery as $img)
                            <div class="w-24 h-24 shrink-0">
                                <img src="{{ $img->url }}" class="w-full h-full object-cover rounded-xl border border-[var(--border)]">
                            </div>
                        @endforeach
                    </div>
                @endif

                <div class="border-2 border-dashed border-[var(--border)] rounded-2xl p-10 text-center">
                    <i class="ph ph-cloud-arrow-up text-5xl text-[var(--textSecondary)] mb-4"></i>
                    <p class="text-[var(--textSecondary)] mb-4">Upload new photos to replace current ones. JPG, PNG formats supported.</p>
                    <input type="file" name="images[]" multiple accept="image/*" class="hidden" id="imageInput">
                    <label for="imageInput" class="px-6 py-2 bg-[var(--bgTertiary)] text-[var(--textPrimary)] rounded-xl font-bold cursor-pointer hover:bg-[var(--border)] transition-all">Select Photos</label>
                </div>
            </div>

            <div class="flex justify-end gap-4">
                <button type="button" onclick="window.history.back()" class="px-8 py-3 rounded-xl font-bold text-[var(--textSecondary)] hover:bg-[var(--bgTertiary)] transition-all">Cancel</button>
                <button type="submit" class="px-12 py-3 bg-[var(--primary)] text-white rounded-xl font-bold hover:bg-[var(--primaryHover)] transition-all shadow-lg shadow-coral/20">Update Item</button>
            </div>
        </form>
    </div>
</div>
@endsection
