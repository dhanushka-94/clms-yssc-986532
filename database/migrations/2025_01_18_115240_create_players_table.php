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
        Schema::create('players', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('player_id')->unique();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('nic')->unique();
            $table->string('phone');
            $table->string('address');
            $table->date('date_of_birth');
            $table->date('joined_date');
            $table->string('position');
            $table->string('jersey_number')->unique();
            $table->decimal('contract_amount', 10, 2);
            $table->date('contract_start_date');
            $table->date('contract_end_date');
            $table->enum('status', ['active', 'injured', 'suspended', 'inactive']);
            $table->text('achievements')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('players');
    }
};
