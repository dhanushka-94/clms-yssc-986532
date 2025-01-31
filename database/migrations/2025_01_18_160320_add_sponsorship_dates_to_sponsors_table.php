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
            if (!Schema::hasColumn('sponsors', 'sponsorship_start_date')) {
                $table->date('sponsorship_start_date')->after('sponsorship_amount');
            }
            if (!Schema::hasColumn('sponsors', 'sponsorship_end_date')) {
                $table->date('sponsorship_end_date')->after('sponsorship_start_date');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sponsors', function (Blueprint $table) {
            if (Schema::hasColumn('sponsors', 'sponsorship_start_date')) {
                $table->dropColumn('sponsorship_start_date');
            }
            if (Schema::hasColumn('sponsors', 'sponsorship_end_date')) {
                $table->dropColumn('sponsorship_end_date');
            }
        });
    }
};
