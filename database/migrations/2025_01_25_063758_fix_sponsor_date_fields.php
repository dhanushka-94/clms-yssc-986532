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
        Schema::table('sponsors', function (Blueprint $table) {
            if (Schema::hasColumn('sponsors', 'contract_start_date')) {
                $table->dropColumn('contract_start_date');
            }
            if (Schema::hasColumn('sponsors', 'contract_end_date')) {
                $table->dropColumn('contract_end_date');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sponsors', function (Blueprint $table) {
            if (!Schema::hasColumn('sponsors', 'contract_start_date')) {
                $table->date('contract_start_date');
            }
            if (!Schema::hasColumn('sponsors', 'contract_end_date')) {
                $table->date('contract_end_date');
            }
        });
    }
};
