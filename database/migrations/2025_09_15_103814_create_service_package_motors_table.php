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
        Schema::create('service_package_motors', function (Blueprint $table) {
            $table->foreignId('service_package_id')->constrained()->onDelete('cascade');
            $table->foreignId('motor_id')->constrained()->onDelete('cascade');

            $table->primary(['service_package_id', 'motor_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_package_motors');
    }
};
