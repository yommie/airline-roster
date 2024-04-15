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
        Schema::create('day_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId("user_id")->constrained()->onDelete("cascade");
            $table->dateTime("date");
            $table->string("activity_type");
            $table->integer("activity_type_id")->nullable();
            $table->dateTime("activity_start")->nullable();
            $table->dateTime("activity_end")->nullable();
            $table->json("extra_data");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('day_activities');
    }
};
