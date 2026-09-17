<?php

namespace App\Http\Controllers;

use App\Models\Workout;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();
        $weekStart = Carbon::now()->startOfWeek();
        $weekEnd = Carbon::now()->endOfWeek();

        $stepsToday = (int) Workout::today()->sum('steps');
        $caloriesToday = (int) Workout::today()->sum('calories');
        $workoutsThisWeek = (int) Workout::thisWeek()->count();
        $avgHeartRate = (int) round(Workout::thisWeek()->avg('heart_rate') ?? 0);

        $missedWorkouts = Workout::where('status', 'missed')
            ->orderByDesc('workout_date')
            ->take(5)
            ->get();

        $caloriesByType = Workout::select('type', DB::raw('SUM(calories) as total'))
            ->groupBy('type')
            ->orderBy('type')
            ->get();

        $statusDistribution = Workout::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->get();

        $recentActivity = Workout::orderByDesc('workout_date')->take(10)->get();

        return view('dashboard', [
            'stepsToday' => $stepsToday,
            'caloriesToday' => $caloriesToday,
            'workoutsThisWeek' => $workoutsThisWeek,
            'avgHeartRate' => $avgHeartRate,
            'missedWorkouts' => $missedWorkouts,
            'caloriesByType' => $caloriesByType,
            'statusDistribution' => $statusDistribution,
            'recentActivity' => $recentActivity,
            'types' => WorkoutController::$types,
            'statuses' => WorkoutController::$statuses,
        ]);
    }
}
