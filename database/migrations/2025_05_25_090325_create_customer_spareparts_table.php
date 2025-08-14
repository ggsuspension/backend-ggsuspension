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
        Schema::create('customer_spareparts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('sparepart_id');
            $table->unsignedBigInteger('gerai_id');
            $table->integer('qty');
            $table->integer('price');
            $table->timestamps();
            // Foreign Keys
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
            $table->foreign('sparepart_id')->references('id')->on('spareparts')->onDelete('cascade');
            $table->foreign('gerai_id')->references('id')->on('gerais')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_spareparts');
    }
};
