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
        Schema::table('appointments', function (Blueprint $table) {
            $table->string('transaction_id')->nullable()->index()->after('payment_method');
            $table->timestamp('paid_at')->nullable()->after('transaction_id');
            $table->timestamp('reminder_sent_at')->nullable()->after('paid_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropColumn(['transaction_id', 'paid_at', 'reminder_sent_at']);
        });
    }
};
