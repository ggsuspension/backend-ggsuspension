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
        Schema::create('expense_category_gerai', function (Blueprint $table) {
            $table->id();
            $table->foreignId('expense_category_id')->constrained()->onDelete('cascade');
            $table->foreignId('gerai_id')->constrained()->onDelete('cascade');
            $table->decimal('daily_cost', 15, 2)->nullable();
            $table->decimal('monthly_cost', 15, 2)->nullable();
            $table->timestamps();

            $table->unique(['expense_category_id', 'gerai_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expense_category_gerai');
    }
};
