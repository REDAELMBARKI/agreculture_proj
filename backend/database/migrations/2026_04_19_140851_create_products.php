<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {   
        if (! Schema::hasTable('addresses')) {
            Schema::create('addresses', function (Blueprint $table) {
                $table->id();
    
                // Works for users (home address) and products (pickup location)
                $table->morphs('addressable');
    
                $table->foreignId('city_id')->constrained('cities')->cascadeOnDelete();
                $table->string('district')->nullable();
                $table->string('address_line')->nullable();  // street / landmark
                $table->decimal('lat', 10, 7)->nullable();   // for map pin
                $table->decimal('lng', 10, 7)->nullable();
                $table->boolean('is_default')->default(false); // user's default address
    
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('products')) {
            Schema::create('products', function (Blueprint $table) {
                $table->id();
                $table->foreignId('super_category_id')->nullable()->constrained('categories')->nullOnDelete();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->string("slug")->unique();

                $table->string('title');
                $table->text('description')->nullable();

                // Pricing
                $table->decimal('price', 10, 2)->nullable();
                $table->string('currency', 10)->default('MAD');
                $table->boolean('price_negotiable')->default(false);
                
                $table->string('contact_phone', 32);
                $table->enum('handover_method', ['pickup', 'delivery', 'both'])->nullable();

                // Status lifecycle
                $table->enum('status', [
                    'reserved',
                    'sold',
                    'closed',
                    'published',
                    'draft'
                ])->default('published');

                $table->string('condition')->nullable();  // new, like_new, good, fair — for single items
                $table->string('gender')->nullable();     // boy, girl, unisex — for single items
                $table->string('age_range')->nullable();  // 0-3m, 3-6m, 1-3y — for single items
                $table->string('brand')->nullable();
                $table->string('season')->nullable();
                $table->json('sizes')->nullable();
                $table->json('colors')->nullable();

                // Stats
                $table->unsignedInteger('views_count')->default(0);
                $table->unsignedInteger('favorites_count')->default(0);

                $table->timestamps();
                $table->softDeletes();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('addresses');
        Schema::dropIfExists('products');
    }
};