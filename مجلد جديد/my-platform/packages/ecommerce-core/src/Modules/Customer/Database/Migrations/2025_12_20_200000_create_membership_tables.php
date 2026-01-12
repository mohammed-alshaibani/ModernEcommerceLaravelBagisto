<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('membership_levels', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Gold, Silver, Platinum
            $table->string('slug')->unique();
            $table->integer('min_points')->default(0);
            $table->decimal('discount_percentage', 5, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('customer_loyalty', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->integer('points_balance')->default(0);
            $table->foreignId('membership_level_id')->nullable()->constrained('membership_levels');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customer_loyalty');
        Schema::dropIfExists('membership_levels');
    }
};
