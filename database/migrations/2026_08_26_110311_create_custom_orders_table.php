<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('custom_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('buyer_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('seller_id')->constrained('seller_profiles')->cascadeOnDelete();
            $table->string('title');
            $table->text('description');
            $table->decimal('budget', 12, 2)->nullable();
            $table->date('deadline')->nullable();
            $table->enum('status', ['pending', 'discussing', 'quoted', 'approved', 'rejected', 'completed', 'cancelled'])->default('pending');
            $table->decimal('quoted_price', 12, 2)->nullable();
            $table->text('seller_notes')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });

        Schema::create('custom_order_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('custom_order_id')->constrained('custom_orders')->cascadeOnDelete();
            $table->string('image_path');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('custom_order_images');
        Schema::dropIfExists('custom_orders');
    }
};
