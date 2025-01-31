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
            $table->string('contact_person')->nullable()->change();
            $table->string('email')->nullable()->change();
            $table->string('phone')->nullable()->change();
            $table->string('whatsapp_number')->nullable()->change();
            $table->string('address')->nullable()->change();
            $table->string('sponsorship_type')->nullable()->change();
            $table->date('sponsorship_start_date')->nullable()->change();
            $table->date('sponsorship_end_date')->nullable()->change();
            $table->string('status')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sponsors', function (Blueprint $table) {
            $table->string('contact_person')->nullable(false)->change();
            $table->string('email')->nullable(false)->change();
            $table->string('phone')->nullable(false)->change();
            $table->string('whatsapp_number')->nullable(false)->change();
            $table->string('address')->nullable(false)->change();
            $table->string('sponsorship_type')->nullable(false)->change();
            $table->date('sponsorship_start_date')->nullable(false)->change();
            $table->date('sponsorship_end_date')->nullable(false)->change();
            $table->string('status')->nullable(false)->change();
        });
    }
};
