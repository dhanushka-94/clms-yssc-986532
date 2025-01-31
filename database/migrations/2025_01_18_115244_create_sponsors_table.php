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
        Schema::create('sponsors', function (Blueprint $table) {
            $table->id();
            $table->string('sponsor_id')->unique();
            $table->string('name');
            $table->string('contact_person');
            $table->string('email')->unique();
            $table->string('phone');
            $table->string('whatsapp_number')->nullable();
            $table->string('address');
            $table->enum('sponsorship_type', ['main', 'co', 'other']);
            $table->decimal('sponsorship_amount', 10, 2);
            $table->date('sponsorship_start_date');
            $table->date('sponsorship_end_date');
            $table->enum('status', ['active', 'inactive']);
            $table->string('profile_picture')->nullable();
            $table->json('attachments')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sponsors');
    }
};
