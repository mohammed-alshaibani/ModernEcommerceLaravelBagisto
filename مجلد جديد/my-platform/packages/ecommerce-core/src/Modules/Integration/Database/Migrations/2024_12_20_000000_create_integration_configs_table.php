<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('integration_configs', function (Blueprint $table) {
            $table->id();
            $table->string('module'); // payment, shipping, sms
            $table->string('provider'); // stripe, moyasar, aramex
            $table->string('key_name'); // api_key, secret_key, username
            $table->text('encrypted_value'); // The actual key, encrypted
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['module', 'provider', 'key_name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('integration_configs');
    }
};
