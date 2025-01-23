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
        Schema::table('bank_accounts', function (Blueprint $table) {
            if (!Schema::hasColumn('bank_accounts', 'initial_balance')) {
                $table->decimal('initial_balance', 12, 2)->after('currency');
            }
            if (!Schema::hasColumn('bank_accounts', 'current_balance')) {
                $table->decimal('current_balance', 12, 2)->after('initial_balance');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bank_accounts', function (Blueprint $table) {
            $table->dropColumn(['initial_balance', 'current_balance']);
        });
    }
};
