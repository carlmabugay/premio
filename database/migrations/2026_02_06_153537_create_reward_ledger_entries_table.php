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
            $table->enum('type', ['earn', 'redeem', 'adjustment'])->default('earn');
            $table->string('reference_type')->nullable(); // reward_issue, redemption
            $table->uuid('reference_id');
            $table->integer('points');
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
