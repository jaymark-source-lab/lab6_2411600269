<?php

namespace Database\Seeders;

use App\Models\Workout;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class WorkoutSeeder extends Seeder
{
    public function run(): void
    {
        $today = Carbon::today();

        $workouts = [
            ['name' => 'Upper Body Strength', 'type' => 'Strength',   'days_ago' => 0,  'duration' => 45, 'calories' => 280, 'steps' => 1200, 'heart_rate' => 128, 'status' => 'completed'],
            ['name' => 'Leg Day',             'type' => 'Strength',   'days_ago' => 2,  'duration' => 40, 'calories' => 260, 'steps' => 900,  'heart_rate' => 132, 'status' => 'missed'],
            ['name' => 'Sprint Intervals',     'type' => 'HIIT',       'days_ago' => 4,  'duration' => 25, 'calories' => 310, 'steps' => 2400, 'heart_rate' => 158, 'status' => 'missed'],
            ['name' => 'Trail Cycling',        'type' => 'Cycling',    'days_ago' => 8,  'duration' => 60, 'calories' => 520, 'steps' => 0,    'heart_rate' => 141, 'status' => 'missed'],
            ['name' => 'Cycling Loop',         'type' => 'Cycling',    'days_ago' => 1,  'duration' => 50, 'calories' => 430, 'steps' => 0,    'heart_rate' => 138, 'status' => 'completed'],
            ['name' => 'HIIT Blast',           'type' => 'HIIT',       'days_ago' => 3,  'duration' => 30, 'calories' => 360, 'steps' => 2100, 'heart_rate' => 152, 'status' => 'completed'],
            ['name' => 'Full Body Circuit',    'type' => 'Full Body',  'days_ago' => 5,  'duration' => 48, 'calories' => 340, 'steps' => 1500, 'heart_rate' => 136, 'status' => 'completed'],
            ['name' => 'Morning Jog',          'type' => 'Cardio',     'days_ago' => 0,  'duration' => 35, 'calories' => 300, 'steps' => 4200, 'heart_rate' => 140, 'status' => 'completed'],
            ['name' => 'Core & Abs',           'type' => 'Strength',   'days_ago' => 6,  'duration' => 20, 'calories' => 150, 'steps' => 300,  'heart_rate' => 120, 'status' => 'completed'],
            ['name' => 'Recovery Cycling',     'type' => 'Cycling',    'days_ago' => -1, 'duration' => 40, 'calories' => 250, 'steps' => 0,    'heart_rate' => 110, 'status' => 'scheduled'],
            ['name' => 'Push Day',             'type' => 'Strength',   'days_ago' => -2, 'duration' => 45, 'calories' => 270, 'steps' => 800,  'heart_rate' => 130, 'status' => 'scheduled'],
            ['name' => 'Tabata HIIT',          'type' => 'HIIT',       'days_ago' => -3, 'duration' => 20, 'calories' => 280, 'steps' => 1600, 'heart_rate' => 155, 'status' => 'scheduled'],
        ];

        foreach ($workouts as $w) {
            Workout::create([
                'name' => $w['name'],
                'type' => $w['type'],
                'workout_date' => $today->copy()->subDays($w['days_ago']),
                'duration_minutes' => $w['duration'],
                'calories' => $w['calories'],
                'steps' => $w['steps'],
                'heart_rate' => $w['heart_rate'],
                'status' => $w['status'],
            ]);
        }
    }
}
