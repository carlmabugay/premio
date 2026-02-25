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
        Schema::create('redemptions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('merchant_id');
            $table->uuid('customer_id');
            $table->string('external_id')->nullable();
            $table->integer('points');
            $table->enum('status', ['pending', 'completed', 'failed'])->default('pending');
            $table->json('meta_data')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['merchant_id', 'customer_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('redemptions');
    }
};
