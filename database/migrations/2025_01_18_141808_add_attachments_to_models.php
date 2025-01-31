<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * The tables that need the attachments column.
     *
     * @var array
     */
    protected $tables = [
        'members',
        'staff',
        'players',
        'sponsors',
        'bank_accounts',
        'users'
    ];

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add attachments to members table
        Schema::table('members', function (Blueprint $table) {
            if (!Schema::hasColumn('members', 'attachments')) {
                $table->json('attachments')->nullable();
            }
        });

        // Add attachments to staff table
        Schema::table('staff', function (Blueprint $table) {
            if (!Schema::hasColumn('staff', 'attachments')) {
                $table->json('attachments')->nullable();
            }
        });

        // Add attachments to players table
        Schema::table('players', function (Blueprint $table) {
            if (!Schema::hasColumn('players', 'attachments')) {
                $table->json('attachments')->nullable();
            }
        });

        // Add attachments to sponsors table
        Schema::table('sponsors', function (Blueprint $table) {
            if (!Schema::hasColumn('sponsors', 'attachments')) {
                $table->json('attachments')->nullable();
            }
        });

        // Add attachments to bank_accounts table
        Schema::table('bank_accounts', function (Blueprint $table) {
            if (!Schema::hasColumn('bank_accounts', 'attachments')) {
                $table->json('attachments')->nullable();
            }
        });

        // Add attachments to financial_transactions table
        Schema::table('financial_transactions', function (Blueprint $table) {
            if (!Schema::hasColumn('financial_transactions', 'attachments')) {
                $table->json('attachments')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->dropColumn('attachments');
        });

        Schema::table('staff', function (Blueprint $table) {
            $table->dropColumn('attachments');
        });

        Schema::table('players', function (Blueprint $table) {
            $table->dropColumn('attachments');
        });

        Schema::table('sponsors', function (Blueprint $table) {
            $table->dropColumn('attachments');
        });

        Schema::table('bank_accounts', function (Blueprint $table) {
            $table->dropColumn('attachments');
        });

        Schema::table('financial_transactions', function (Blueprint $table) {
            $table->dropColumn('attachments');
        });
    }
};
