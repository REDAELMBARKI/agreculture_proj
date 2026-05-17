@extends('layouts.main')

@section('title', 'Post New Item')

@section('content')
@push('styles')
<link rel="stylesheet" href="{{ asset('css/add_announcement.css') }}">
@endpush

<div class="announcement-page" x-data="announcementForm()">
    <div class="announcement-shell">
        <div class="announcement-card">
            <h3>Post New Item</h3>
            <p class="subtitle">Fill in the details to list your item in the marketplace.</p>

            <!-- Stepper -->
            <div class="aa-stepper">
                <div class="aa-step-container" :class="step >= 1 ? 'done' : (step === 1 ? 'active' : 'future')">
                    <div class="aa-step-circle">1</div>
                    <div class="aa-step-label">Category</div>
                </div>
                <div class="aa-step-container" :class="step >= 2 ? 'done' : (step === 2 ? 'active' : 'future')">
                    <div class="aa-step-connector"></div>
                    <div class="aa-step-circle">2</div>
                    <div class="aa-step-label">Details</div>
                </div>
                <div class="aa-step-container" :class="step >= 3 ? 'done' : (step === 3 ? 'active' : 'future')">
                    <div class="aa-step-connector"></div>
                    <div class="aa-step-circle">3</div>
                    <div class="aa-step-label">Variants</div>
                </div>
                <div class="aa-step-container" :class="step >= 4 ? 'done' : (step === 4 ? 'active' : 'future')">
                    <div class="aa-step-connector"></div>
                    <div class="aa-step-circle">4</div>
                    <div class="aa-step-label">Finish</div>
                </div>
            </div>

            <form action="{{ route('user.announcements.store') }}" method="POST" enctype="multipart/form-data" id="announcement-form">
                @csrf
                
                <!-- Step 1: Category -->
                <div x-show="step === 1" class="aa-section">
                    <h4 style="margin-bottom: 15px; font-weight: 600;">Choose a Category</h4>
                    <div class="aa-icon-grid">
                        @foreach($categories as $cat)
                        <label class="aa-icon-card" :class="form.super_category_id == {{ $cat['id'] }} ? 'active' : ''">
                            <input type="radio" name="super_category_id" x-model="form.super_category_id" value="{{ $cat['id'] }}" class="aa-hidden-input" required>
                            <i class="ph ph-{{ $cat['icon'] ?: 'package' }}" style="font-size: 32px;"></i>
                            <span>{{ $cat['label'] }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>

                <!-- Step 2: Details -->
                <div x-show="step === 2" class="aa-section">
                    <div class="aa-two-buttons" style="margin-bottom: 20px;">
                        <label class="aa-toggle-card" :class="form.listing_mode === 'sell' ? 'active' : ''">
                            <input type="radio" name="listing_mode" x-model="form.listing_mode" value="sell" class="aa-hidden-input">
                            For Sale
                        </label>
                        <label class="aa-toggle-card" :class="form.listing_mode === 'donate' ? 'active' : ''">
                            <input type="radio" name="listing_mode" x-model="form.listing_mode" value="donate" class="aa-hidden-input">
                            Donation
                        </label>
                    </div>

                    <div class="aa-field" style="margin-bottom: 15px;">
                        <label>Title</label>
                        <input type="text" name="title" x-model="form.title" class="aa-input" placeholder="What are you listing?">
                    </div>

                    <div class="aa-field" style="margin-bottom: 15px;">
                        <label>Description</label>
                        <textarea name="description" x-model="form.description" class="aa-input aa-textarea" placeholder="Describe your item..."></textarea>
                    </div>

                    <div x-show="form.listing_mode === 'sell'" class="aa-field">
                        <label>Price (MAD)</label>
                        <input type="number" name="price" x-model="form.price" class="aa-input" placeholder="0.00">
                    </div>
                </div>

                <!-- Step 3: Variants & Photos -->
                <div x-show="step === 3" class="aa-section">
                    <div class="aa-two-col" style="margin-bottom: 15px;">
                        <div class="aa-field">
                            <label>Condition</label>
                            <select name="condition" x-model="form.condition" class="aa-input">
                                <option value="">Select Condition</option>
                                @foreach($conditions as $cond)
                                    <option value="{{ $cond['value'] }}">{{ $cond['label'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="aa-field">
                            <label>Age Range</label>
                            <select name="age_range" x-model="form.age_range" class="aa-input">
                                <option value="">Select Age Range</option>
                                @foreach($ageRanges as $age)
                                    <option value="{{ $age['value'] }}">{{ $age['label'] }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="aa-media-uploader">
                        <label>Photos (Up to 8)</label>
                        <div class="aa-photos-row" style="margin-top: 10px;">
                            <template x-for="(img, index) in imagePreviews" :key="index">
                                <div class="aa-thumb">
                                    <img :src="img" alt="Preview">
                                    <button type="button" @click="removeImage(index)" class="aa-thumb-delete">×</button>
                                </div>
                            </template>
                            <label class="aa-ghost-uploader" x-show="imagePreviews.length < 8">
                                <input type="file" name="images[]" multiple @change="handleImageUpload" class="aa-hidden-input" accept="image/*">
                                <i class="ph ph-plus"></i>
                                <span>Add</span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Step 4: Finish -->
                <div x-show="step === 4" class="aa-section">
                    <div class="aa-field" style="margin-bottom: 15px;">
                        <label>City</label>
                        <select name="city_id" x-model="form.city_id" class="aa-input">
                            <option value="">Select City</option>
                            @foreach($cities as $city)
                                <option value="{{ $city['id'] }}">{{ $city['label'] }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="aa-field" style="margin-bottom: 15px;">
                        <label>Pickup Address</label>
                        <input type="text" name="pickup_address" x-model="form.pickup_address" class="aa-input" placeholder="Where should the buyer pick it up?">
                    </div>

                    <div class="aa-field">
                        <label>WhatsApp / Phone</label>
                        <input type="text" name="contact_phone" x-model="form.contact_phone" class="aa-input" placeholder="06XXXXXXXX">
                    </div>
                </div>

                <div class="actions">
                    <button type="button" x-show="step > 1" @click="step--" class="secondary">Back</button>
                    <div style="flex: 1"></div>
                    <button type="button" x-show="step < 4" @click="step++" class="primary" :disabled="!canContinue()">Continue</button>
                    <button type="submit" x-show="step === 4" class="publish" :disabled="!canPublish()">Publish</button>
                </div>
            </form>
        </div>

        <div class="announcement-preview">
            <h4>Live Preview</h4>
            <div class="preview-card">
                <div class="preview-main-image">
                    <template x-if="imagePreviews.length > 0">
                        <img :src="imagePreviews[0]" alt="Preview">
                    </template>
                    <template x-if="imagePreviews.length === 0">
                        <div class="preview-empty-image">
                            <i class="ph ph-image"></i>
                            <span>No image</span>
                        </div>
                    </template>
                    <div class="preview-main-badge" x-show="form.listing_mode === 'donate'">Donation</div>
                </div>

                <h5 x-text="form.title || 'Product Title'"></h5>
                <div class="preview-price" x-show="form.listing_mode === 'sell'">
                    <span x-text="form.price ? form.price + ' MAD' : '0.00 MAD'"></span>
                </div>

                <div class="preview-meta">
                    <div class="preview-row" x-show="form.condition">
                        <i class="ph ph-shield-check"></i>
                        <span x-text="'Condition: ' + getConditionLabel()"></span>
                    </div>
                    <div class="preview-row" x-show="form.city_id">
                        <i class="ph ph-map-pin"></i>
                        <span x-text="'Location: ' + getCityLabel()"></span>
                    </div>
                </div>

                <div class="preview-description">
                    <p x-text="form.description || 'Description will appear here...'"></p>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function announcementForm() {
        return {
            step: 1,
            form: {
                super_category_id: '',
                listing_mode: 'sell',
                title: '',
                description: '',
                price: '',
                condition: '',
                age_range: '',
                city_id: '',
                pickup_address: '',
                contact_phone: '{{ $user->phone ?? "" }}'
            },
            imagePreviews: [],
            categories: @json($categories),
            conditions: @json($conditions),
            cities: @json($cities),

            handleImageUpload(e) {
                const files = Array.from(e.target.files);
                if (this.imagePreviews.length + files.length > 8) {
                    alert('Maximum 8 images allowed');
                    return;
                }

                files.forEach(file => {
                    const reader = new FileReader();
                    reader.onload = (ex) => {
                        this.imagePreviews.push(ex.target.result);
                    };
                    reader.readAsDataURL(file);
                });
            },

            removeImage(index) {
                this.imagePreviews.splice(index, 1);
            },

            canContinue() {
                if (this.step === 1) return this.form.super_category_id !== '';
                if (this.step === 2) return this.form.title.length > 3;
                if (this.step === 3) return this.form.condition !== '' && this.form.age_range !== '';
                return true;
            },

            canPublish() {
                return this.form.city_id !== '' && this.form.pickup_address !== '' && this.form.contact_phone !== '';
            },

            getConditionLabel() {
                const cond = this.conditions.find(c => c.value === this.form.condition);
                return cond ? cond.label : '';
            },

            getCityLabel() {
                const city = this.cities.find(c => c.id == this.form.city_id);
                return city ? city.label : '';
            }
        }
    }
</script>
@endpush
@endsection
