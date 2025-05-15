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
        Schema::create('motor_to_motor_part', function (Blueprint $table) {
            $table->foreignId('motor_id')->constrained()->onDelete('cascade');
            $table->foreignId('motor_part_id')->constrained()->onDelete('cascade');
            $table->primary(['motor_id', 'motor_part_id']);
            $table->index('motor_id');
            $table->index('motor_part_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('motor_to_motor_part');
    }
};
