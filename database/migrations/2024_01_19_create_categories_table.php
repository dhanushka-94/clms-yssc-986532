<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type'); // 'sponsor', 'income', or 'expense'
            $table->string('description')->nullable();
            $table->string('color')->nullable(); // For UI purposes
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            // Ensure name is unique within each type
            $table->unique(['name', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
}; 