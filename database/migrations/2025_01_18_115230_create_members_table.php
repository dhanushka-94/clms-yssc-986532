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
        Schema::create('members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('membership_number')->unique();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('nic')->unique();
            $table->string('phone');
            $table->string('address');
            $table->date('date_of_birth');
            $table->date('joined_date');
            $table->enum('status', ['active', 'inactive', 'suspended']);
            $table->decimal('membership_fee', 10, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('members');
    }
};
