<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('analytics_visits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('session_id')->index();
            $table->string('ip_address', 45)->nullable();
            $table->string('url')->index();
            $table->string('referer')->nullable();
            $table->string('user_agent')->nullable();
            $table->string('device_type')->nullable(); // mobile, desktop, tablet
            $table->timestamp('created_at')->useCurrent();
        });

        Schema::create('analytics_events', function (Blueprint $table) {
            $table->id();
            $table->string('session_id')->index();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('event_name')->index(); // add_to_cart, search, etc.
            $table->json('event_data')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });

        Schema::create('analytics_conversions', function (Blueprint $table) {
            $table->id();
            $table->string('session_id')->index();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->decimal('amount', 12, 2);
            $table->string('source')->nullable(); // marketing, direct, social
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('analytics_conversions');
        Schema::dropIfExists('analytics_events');
        Schema::dropIfExists('analytics_visits');
    }
};
