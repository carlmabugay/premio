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
        Schema::create('reward_ledger_entries', function (Blueprint $table) {
            $table->uuid('id')->primary();
            //            $table->foreignUuid('reward_rule_id')->references('id')->on('reward_rules')->onDelete('cascade');
            $table->string('subject_type');
            $table->string('subject_id');
            $table->float('points');
            $table->string('reason')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reward_ledger_entries');
    }
};
