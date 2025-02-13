<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('financial_transactions', function (Blueprint $table) {
            // Add indexes for better query performance
            $table->index(['transactionable_type', 'transactionable_id']);
            $table->index('transaction_date');
            $table->index('type');
            $table->index('status');
            $table->index('category');
        });
    }

    public function down()
    {
        Schema::table('financial_transactions', function (Blueprint $table) {
            $table->dropIndex(['transactionable_type', 'transactionable_id']);
            $table->dropIndex(['transaction_date']);
            $table->dropIndex(['type']);
            $table->dropIndex(['status']);
            $table->dropIndex(['category']);
        });
    }
}; 