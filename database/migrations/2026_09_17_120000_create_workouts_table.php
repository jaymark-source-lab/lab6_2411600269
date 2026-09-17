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
        Schema::create('workouts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type'); // Strength, Cardio, HIIT, Cycling, Full Body
            $table->date('workout_date');
            $table->unsignedInteger('duration_minutes');
            $table->unsignedInteger('calories')->default(0);
            $table->unsignedInteger('steps')->nullable();
            $table->unsignedInteger('heart_rate')->nullable(); // avg bpm for the session
            $table->string('status')->default('scheduled'); // completed, scheduled, missed
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['type']);
            $table->index(['status']);
            $table->index(['workout_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workouts');
    }
};
