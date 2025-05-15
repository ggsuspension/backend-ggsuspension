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
        Schema::create('daily_net_revenues', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gerai_id')->constrained('gerais')->onDelete('cascade');
            $table->date('date');
            $table->decimal('total_revenue', 15, 2);
            $table->decimal('total_expenses', 15, 2);
            $table->decimal('net_revenue', 15, 2);
            $table->timestamps();
            $table->unique(['gerai_id', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_net_revenues');
    }
};
