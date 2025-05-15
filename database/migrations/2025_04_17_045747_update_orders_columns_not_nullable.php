<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Set nilai default untuk record yang null
        DB::table('orders')->whereNull('waktu')->update(['waktu' => now()]);
        DB::table('orders')->whereNull('gerai_id')->update(['gerai_id' => 1]);
        DB::table('orders')->whereNull('motor_id')->update(['motor_id' => 1]);
        DB::table('orders')->whereNull('motor_part_id')->update(['motor_part_id' => 1]);
        DB::table('orders')->whereNull('status')->update(['status' => 'PROGRESS']);

        Schema::table('orders', function (Blueprint $table) {
            $table->dateTime('waktu')->nullable(false)->default(now())->change();
            $table->unsignedBigInteger('gerai_id')->nullable(false)->default(1)->change();
            $table->unsignedBigInteger('motor_id')->nullable(false)->default(1)->change();
            $table->unsignedBigInteger('motor_part_id')->nullable(false)->default(1)->change();
            $table->string('status')->nullable(false)->default('PROGRESS')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dateTime('waktu')->nullable()->change();
            $table->unsignedBigInteger('gerai_id')->nullable()->change();
            $table->unsignedBigInteger('motor_id')->nullable()->change();
            $table->unsignedBigInteger('motor_part_id')->nullable()->change();
            $table->string('status')->nullable()->change();
        });
    }
};
