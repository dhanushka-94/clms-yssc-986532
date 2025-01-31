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
            $table->text('notes')->nullable();
            // Change sponsorship_type to text field if it exists
            if (Schema::hasColumn('sponsors', 'sponsorship_type')) {
                $table->string('sponsorship_type')->change();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sponsors', function (Blueprint $table) {
            $table->dropColumn('notes');
            // We don't need to revert sponsorship_type as it's just a type change
        });
    }
};
