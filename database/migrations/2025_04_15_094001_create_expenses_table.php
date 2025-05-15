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
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gerai_id')->nullable()->constrained()->onDelete('set null');
            $table->integer('amount');
            $table->string('description')->nullable();
            $table->dateTime('date');
            $table->timestamps();
            $table->index('gerai_id');
            $table->index('date');
            $table->string('category');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
