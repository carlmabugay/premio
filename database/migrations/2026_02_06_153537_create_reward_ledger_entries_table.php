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
            $table->uuid('event_id');
            $table->foreignId('reward_rule_id')->constrained();
            $table->enum('type', ['earn', 'adjust'])->default('earn');
            $table->enum('subject_type', ['customer', 'order'])->default('customer');
            $table->string('subject_id');
            $table->float('amount');
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
