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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('plat');
            $table->string('no_wa');
            $table->dateTime('waktu');
            $table->foreignId('gerai_id')->constrained()->onDelete('cascade');
            $table->integer('total_harga');
            $table->enum('status', ['PROGRESS', 'FINISHED', 'CANCELLED'])->default('PROGRESS');
            $table->foreignId('motor_id')->constrained()->onDelete('cascade');
            $table->foreignId('motor_part_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            $table->index(['gerai_id', 'motor_id', 'motor_part_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
