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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string("nama");
            $table->string("plat_motor");
            $table->string("noWA");
            $table->string("gerai")->nullable();
            $table->string("sudah_chat");
            $table->string("sumber_info");
            $table->string("status")->default("MENUNGGU ANTRIAN");
            $table->json("sparepart")->nullable();
            $table->integer("sparepart_id")->nullable();
            $table->string("layanan")->nullable();
            $table->string("jenis_motor")->nullable();
            $table->integer("harga_service")->nullable();
            $table->integer("harga_sparepart")->nullable();
            $table->string("bagian_motor")->nullable();
            $table->string("bagian_motor2")->nullable();
            $table->string("motor")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
