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
        Schema::create('budget_category', function (Blueprint $table) {
            $table->id();
            $table->foreignId('budget_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });

        // Migrate existing category_id data to pivot table
        $budgets = \Illuminate\Support\Facades\DB::table('budgets')->get();
        foreach ($budgets as $budget) {
            if ($budget->category_id) {
                \Illuminate\Support\Facades\DB::table('budget_category')->insert([
                    'budget_id' => $budget->id,
                    'category_id' => $budget->category_id,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
        }

        // Now drop the column from budgets
        Schema::table('budgets', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropColumn('category_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('budgets', function (Blueprint $table) {
            $table->foreignId('category_id')->nullable()->constrained()->cascadeOnDelete();
        });

        // Try to revert data
        $budgetCategories = \Illuminate\Support\Facades\DB::table('budget_category')->get();
        foreach ($budgetCategories as $bc) {
            \Illuminate\Support\Facades\DB::table('budgets')
                ->where('id', $bc->budget_id)
                ->update(['category_id' => $bc->category_id]);
        }

        Schema::dropIfExists('budget_category');
    }
};
