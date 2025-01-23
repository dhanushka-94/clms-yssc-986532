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
        Schema::create('inter_bank_transfers', function (Blueprint $table) {
            $table->id();
            $table->string('transfer_number')->unique();
            $table->foreignId('from_account_id')->constrained('bank_accounts')->onDelete('cascade');
            $table->foreignId('to_account_id')->constrained('bank_accounts')->onDelete('cascade');
            $table->decimal('amount', 15, 2);
            $table->string('description');
            $table->date('transfer_date');
            $table->string('reference_number')->nullable();
            $table->enum('status', ['pending', 'completed', 'failed', 'cancelled']);
            $table->json('attachments')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inter_bank_transfers');
    }
};
