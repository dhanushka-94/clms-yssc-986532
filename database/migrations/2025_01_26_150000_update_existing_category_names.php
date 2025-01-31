<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\FinancialTransaction;
use App\Models\Category;

return new class extends Migration
{
    public function up()
    {
        $transactions = FinancialTransaction::all();
        foreach ($transactions as $transaction) {
            if (is_numeric($transaction->category)) {
                $category = Category::find($transaction->category);
                if ($category) {
                    $transaction->update(['category' => $category->name]);
                }
            }
        }
    }

    public function down()
    {
        // Cannot revert this change as we don't store the original category IDs
    }
}; 