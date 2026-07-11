<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sensor_readings', function (Blueprint $table) {
            $table->id();
            $table->decimal('temperature', 5, 2)->comment('Temperature in Celsius');
            $table->decimal('humidity', 5, 2)->comment('Humidity in percentage');
            $table->integer('mq2')->comment('MQ2 gas sensor raw value');
            $table->integer('mq5')->comment('MQ5 gas sensor raw value');
            $table->integer('dust')->comment('Dust sensor raw value');
            $table->integer('estimated_aqi')->comment('Estimated Air Quality Index');
            $table->string('gas_status')->default('SAFE')->comment('SAFE or DANGER');
            $table->string('air_status')->default('Good')->comment('Good, Moderate, Unhealthy, Hazardous');
            $table->timestamps();

            $table->index('created_at');
            $table->index('gas_status');
            $table->index('air_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sensor_readings');
    }
};
