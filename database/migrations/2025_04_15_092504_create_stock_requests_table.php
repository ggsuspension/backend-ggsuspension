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
        Schema::create('stock_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gerai_id')->constrained()->onDelete('cascade');
            $table->foreignId('sparepart_id')->constrained()->onDelete('cascade');
            $table->integer('qty_requested');
            $table->string('status')->default('PENDING');
            $table->timestamps();
            $table->dateTime('approved_at')->nullable();
            $table->index('gerai_id');
            $table->index('sparepart_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_requests');
    }
};
