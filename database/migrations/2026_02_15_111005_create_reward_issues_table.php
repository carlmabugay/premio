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
        Schema::create('reward_issues', function (Blueprint $table) {
            $table->id();
            $table->uuid('event_id')->unique();
            $table->unsignedBigInteger('reward_rule_id')->unique();
            $table->string('reward_type');
            $table->decimal('reward_value', 10, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reward_issues');
    }
};
