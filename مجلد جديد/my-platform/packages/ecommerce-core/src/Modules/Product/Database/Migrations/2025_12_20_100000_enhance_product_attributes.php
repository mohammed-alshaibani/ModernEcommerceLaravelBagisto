<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Create attribute values table (e.g., "Red", "Blue", "M", "L")
        if (!Schema::hasTable('product_attribute_values')) {
            Schema::create('product_attribute_values', function (Blueprint $table) {
                $table->id();
                $table->foreignId('attribute_id')->constrained('product_attributes')->cascadeOnDelete();
                $table->string('value'); // e.g., "Red", "Blue", "Medium"
                $table->string('label')->nullable(); // Display label (optional)
                $table->integer('sort_order')->default(0);
                $table->timestamps();
            });
        }

        // Create pivot table for variant-attribute relationship
        if (!Schema::hasTable('product_variant_attributes')) {
            Schema::create('product_variant_attributes', function (Blueprint $table) {
                $table->id();
                $table->foreignId('variant_id')->constrained('product_variants')->cascadeOnDelete();
                $table->foreignId('attribute_value_id')->constrained('product_attribute_values')->cascadeOnDelete();
                $table->timestamps();

                // Unique constraint to prevent duplicate attribute values per variant
                $table->unique(['variant_id', 'attribute_value_id'], 'variant_attribute_unique');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('product_variant_attributes');
        Schema::dropIfExists('product_attribute_values');
    }
};
