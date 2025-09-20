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
        Schema::create('komstir_pricings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('motor_id')->constrained('motors')->onDelete('cascade');
            $table->string('name');
            $table->string('part_type')->nullable();
            $table->integer('price');
            $table->timestamps();

            $table->unique(['motor_id', 'name', 'part_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('komstir_pricings');
    }
};
