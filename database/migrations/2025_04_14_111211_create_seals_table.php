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
        Schema::create('seals', function (Blueprint $table) {
            $table->id();
            $table->string("category");
            $table->string("name");
            $table->integer("sparepart_id");
            $table->integer('price');
            $table->integer('qty');
            $table->unsignedBigInteger('motor_id')->nullable();
            $table->foreign('motor_id')->references('id')->on('motors')->onDelete('cascade');
            $table->foreignId('gerai_id')->constrained()->onDelete('cascade');
            $table->index('gerai_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seals');
    }
};
