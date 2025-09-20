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
        Schema::create('service_queues', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_profile_id')
                ->constrained('customer_profiles')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->foreignId('customer_motor_id')
                ->constrained('customer_motors')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->unsignedBigInteger('service_type_id')->nullable();
            $table->json('services');
            $table->timestamp('checked_in_at')->useCurrent();
            $table->timestamp('completed_at')->nullable();
            $table->string('status')->default('Waiting');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_queues');
    }
};
