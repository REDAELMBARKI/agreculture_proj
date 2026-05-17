<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('product_items')) {
            Schema::create('product_items', function (Blueprint $table) {
                $table->id();

                $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();

                $table->string('item_name');
                $table->string('item_condition')->nullable(); // new, like_new, good, fair, poor
                $table->string('item_gender')->nullable();    // boy, girl, unisex
                $table->string('recommended_age')->nullable();
                $table->string('item_brand')->nullable();
                $table->string('item_material')->nullable();
                $table->string('item_season')->nullable();
                $table->unsignedInteger('item_quantity')->default(1);

                // JSON arrays (sizes and colors vary per item)
                $table->json('item_sizes')->nullable();   // ["XS","S","M"] or ["3-6M"]
                $table->json('item_colors')->nullable();  // ["red","blue"]

                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('product_items');
    }
};
