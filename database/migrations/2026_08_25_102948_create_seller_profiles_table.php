<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('seller_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('shop_name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->text('address')->nullable();
            $table->string('province')->nullable();
            $table->string('city')->nullable();
            $table->string('district')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('operating_hours')->nullable();
            $table->boolean('pickup_available')->default(true);
            $table->boolean('delivery_available')->default(true);
            $table->boolean('custom_order_available')->default(false);
            $table->boolean('is_verified')->default(false);
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();

            $table->index(['latitude', 'longitude']);
            $table->index('is_verified');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seller_profiles');
    }
};
