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
        Schema::create('customer_motors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_profile_id')
                ->constrained('customer_profiles')
                ->cascadeOnDelete();
            $table->string("nama_motor");
            $table->string("jenis_motor");
            $table->string("plat_motor");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_motors');
    }
};
