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
            $table->foreignUuid('event_id')
                ->references('id')
                ->on('events')
                ->cascadeOnDelete();

            $table->foreignId('reward_rule_id')
                ->references('id')
                ->on('reward_rules')
                ->cascadeOnDelete();

            $table->string('reward_type');
            $table->decimal('reward_value', 10, 2)->default(0);

            $table->timestamps();

            $table->unique(['event_id', 'reward_rule_id']);

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
