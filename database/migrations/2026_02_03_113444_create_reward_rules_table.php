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
        Schema::create('reward_rules', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('event_type');
            $table->enum('reward_type', ['fixed', 'percentage'])->default('fixed');
            $table->integer('reward_value')->default(0);
            $table->integer('cap')->default(0);
            $table->boolean('is_active')->default(true);
            $table->dateTime('starts_at')->nullable();
            $table->dateTime('ends_at')->nullable();
            $table->json('conditions')->nullable();
            $table->smallInteger('priority')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reward_rules');
    }
};
