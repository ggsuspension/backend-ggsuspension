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
        Schema::create('package_detail_seal', function (Blueprint $table) {
            $table->id();
            $table->foreignId('package_detail_id')->constrained()->onDelete('cascade');
            $table->foreignId('seal_id')->constrained('seals')->onDelete('cascade');
            $table->integer('quantity')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('package_detail_seal');
    }
};
