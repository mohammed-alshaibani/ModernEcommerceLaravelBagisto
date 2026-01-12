<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('product_variants')) {
            Schema::create('product_variants', function (Blueprint $table) {
                $table->id();
                $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
                $table->string('sku')->unique()->nullable();
                $table->decimal('price', 10, 2)->nullable(); // Override product price
                $table->integer('stock')->default(0);
                $table->json('options'); // e.g. {"Color": "Red", "Size": "M"} or IDs {"1": "5"}
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('product_media')) {
            Schema::create('product_media', function (Blueprint $table) {
                $table->id();
                $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
                $table->string('type')->default('image'); // image, video, external_video
                $table->string('path')->nullable(); // Local path or S3 key
                $table->string('url')->nullable(); // For external videos
                $table->boolean('is_featured')->default(false);
                $table->integer('sort_order')->default(0);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('product_media');
        Schema::dropIfExists('product_variants');
    }
};
