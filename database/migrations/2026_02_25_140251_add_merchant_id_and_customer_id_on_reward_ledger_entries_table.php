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
        Schema::table('reward_ledger_entries', function (Blueprint $table) {
            $table->foreignUuid('merchant_id')->after('id')->constrained();
            $table->foreignUuid('customer_id')->after('merchant_id')->constrained();

            $table->index(['merchant_id', 'customer_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
