<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Main promotions table
        if (!Schema::hasTable('promotions')) {
            Schema::create('promotions', function (Blueprint $table) {
                $table->id();
                $table->string('name'); // e.g., "Summer Sale"
                $table->string('code')->unique(); // e.g., "SUMMER20"
                $table->enum('type', ['percentage', 'fixed_amount'])->default('percentage');
                $table->decimal('value', 10, 2); // 20% = 20.00 or $50 = 50.00
                $table->decimal('min_purchase_amount', 10, 2)->nullable(); // Minimum cart value
                $table->integer('max_uses')->nullable(); // Null = unlimited
                $table->integer('uses_count')->default(0); // Track usage
                $table->enum('applies_to', ['all', 'categories', 'products'])->default('all');
                $table->boolean('is_active')->default(true);
                $table->timestamp('starts_at')->nullable();
                $table->timestamp('expires_at')->nullable();
                $table->timestamps();
            });
        }

        // Promotion categories (for category-specific promotions)
        if (!Schema::hasTable('promotion_categories')) {
            Schema::create('promotion_categories', function (Blueprint $table) {
                $table->id();
                $table->foreignId('promotion_id')->constrained('promotions')->cascadeOnDelete();
                $table->unsignedBigInteger('category_id'); // Assuming categories table exists
                $table->timestamps();
            });
        }

        // Promotion products (for product-specific promotions)
        if (!Schema::hasTable('promotion_products')) {
            Schema::create('promotion_products', function (Blueprint $table) {
                $table->id();
                $table->foreignId('promotion_id')->constrained('promotions')->cascadeOnDelete();
                $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('promotion_products');
        Schema::dropIfExists('promotion_categories');
        Schema::dropIfExists('promotions');
    }
};
