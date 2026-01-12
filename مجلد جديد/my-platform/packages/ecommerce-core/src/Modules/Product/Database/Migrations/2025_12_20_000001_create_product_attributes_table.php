<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attributes', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g., Color, Size
            $table->string('slug')->unique(); // e.g., color, size
            $table->string('type')->default('select'); // select, color, radio, button
            $table->boolean('is_global')->default(true); // If false, specific to a product? For now, keep global.
            $table->timestamps();
        });

        Schema::create('attribute_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attribute_id')->constrained('attributes')->cascadeOnDelete();
            $table->string('value'); // Red, Small
            $table->string('slug'); // red, small
            $table->string('meta_value')->nullable(); // hex code for color, etc.
            $table->integer('position')->default(0);
            $table->timestamps();
            
            $table->unique(['attribute_id', 'slug']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attribute_values');
        Schema::dropIfExists('attributes');
    }
};
