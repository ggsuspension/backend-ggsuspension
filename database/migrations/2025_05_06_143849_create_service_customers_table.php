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
        Schema::create('service_customers', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string("layanan");
            $table->integer("harga");
            $table->string("jenis_motor");
            $table->string("bagian_motor");
            $table->string("bagian_motor2")->nullable();
            $table->string("motor");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_customers');
    }
};
