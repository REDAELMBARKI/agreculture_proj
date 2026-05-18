<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();

            $table->string('item_name');
            $table->string('item_condition')->nullable(); // fresh, dried, processed
            $table->decimal('item_quantity', 10, 2)->default(1);
            $table->string('item_quantity_unit')->nullable();
            $table->date('harvest_date')->nullable();
            $table->string('region')->nullable();
            $table->string('item_brand')->nullable();
            $table->string('item_material')->nullable();
            $table->string('item_season')->nullable();

            // JSON arrays (sizes and colors vary per item)
            $table->json('item_sizes')->nullable();   // ["XS","S","M"] or ["3-6M"]
            $table->json('item_colors')->nullable();  // ["red","blue"]

            $table->timestamps();

            // No gallery column - images in media table (mediable = product_item, role = gallery)
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_items');
    }
};