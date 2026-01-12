<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('attributes')) {
            Schema::create('attributes', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug')->unique();
                $table->string('type')->default('select');
                $table->boolean('is_global')->default(true);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('attribute_values')) {
            Schema::create('attribute_values', function (Blueprint $table) {
                $table->id();
                $table->foreignId('attribute_id')->constrained('attributes')->cascadeOnDelete();
                $table->string('value');
                $table->string('slug');
                $table->string('meta_value')->nullable();
                $table->integer('position')->default(0);
                $table->timestamps();
                
                $table->unique(['attribute_id', 'slug']);
            });
        }
    }

    public function down(): void
    {
        // Don't drop since this is a fix migration, or drop if rollback
    }
};
