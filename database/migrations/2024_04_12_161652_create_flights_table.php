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
        Schema::create('flights', function (Blueprint $table) {
            $table->id();
            $table->string('flight_number');
            $table->string('departure_location');
            $table->dateTime('departure_time');
            $table->string('arrival_location');
            $table->dateTime('arrival_time');
            $table->integer('block_hours')->comment('store in minutes');
            $table->integer('flight_duration')->comment('store in minutes');
            $table->integer('night_duration')->comment('store in minutes');
            $table->integer('activity_duration')->comment('store in minutes');
            $table->integer('extension_duration')->comment('store in minutes');
            $table->integer('passengers_on_flight')->nullable();
            $table->string('aircraft_registration_number');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('flights');
    }
};
