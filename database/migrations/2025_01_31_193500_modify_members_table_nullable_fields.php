<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->string('nic')->nullable()->change();
            $table->string('phone')->nullable()->change();
            $table->string('whatsapp_number')->nullable()->change();
            $table->string('address')->nullable()->change();
            $table->date('date_of_birth')->nullable()->change();
            $table->date('joined_date')->nullable()->change();
            $table->enum('membership_type', ['regular', 'lifetime', 'honorary', 'student'])->nullable()->change();
            $table->string('designation')->nullable()->change();
            $table->decimal('membership_fee', 10, 2)->nullable()->change();
            $table->enum('status', ['active', 'inactive', 'suspended'])->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->string('nic')->nullable(false)->change();
            $table->string('phone')->nullable(false)->change();
            $table->string('whatsapp_number')->nullable(false)->change();
            $table->string('address')->nullable(false)->change();
            $table->date('date_of_birth')->nullable(false)->change();
            $table->date('joined_date')->nullable(false)->change();
            $table->enum('membership_type', ['regular', 'lifetime', 'honorary', 'student'])->nullable(false)->change();
            $table->string('designation')->nullable(false)->change();
            $table->decimal('membership_fee', 10, 2)->nullable(false)->change();
            $table->enum('status', ['active', 'inactive', 'suspended'])->nullable(false)->change();
        });
    }
}; 