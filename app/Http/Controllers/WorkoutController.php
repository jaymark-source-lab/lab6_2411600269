<?php

namespace App\Http\Controllers;

use App\Models\Workout;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class WorkoutController extends Controller
{
    /**
     * Types & statuses available across the app (used for filter dropdowns).
     */
    public static array $types = ['Strength', 'Cardio', 'HIIT', 'Cycling', 'Full Body'];
    public static array $statuses = ['completed', 'scheduled', 'missed'];

    public function index(Request $request)
    {
        $query = Workout::query()
            ->ofType($request->get('type'))
            ->ofStatus($request->get('status'))
            ->search($request->get('search'));

        if ($request->filled('min_calories')) {
            $query->where('calories', '>=', (int) $request->min_calories);
        }

        if ($request->filled('max_calories')) {
            $query->where('calories', '<=', (int) $request->max_calories);
        }

        $workouts = $query->orderByDesc('workout_date')->paginate(10)->withQueryString();

        return view('workouts.index', [
            'workouts' => $workouts,
            'types' => self::$types,
            'statuses' => self::$statuses,
        ]);
    }

    public function create()
    {
        return view('workouts.create', [
            'types' => self::$types,
            'statuses' => self::$statuses,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validated($request);

        Workout::create($validated);

        return redirect()->route('workouts.index')->with('success', 'Workout added successfully.');
    }

    public function show(Workout $workout)
    {
        return view('workouts.show', compact('workout'));
    }

    public function edit(Workout $workout)
    {
        return view('workouts.edit', [
            'workout' => $workout,
            'types' => self::$types,
            'statuses' => self::$statuses,
        ]);
    }

    public function update(Request $request, Workout $workout)
    {
        $validated = $this->validated($request);

        $workout->update($validated);

        return redirect()->route('workouts.index')->with('success', 'Workout updated successfully.');
    }

    public function destroy(Workout $workout)
    {
        $workout->delete();

        return redirect()->route('workouts.index')->with('success', 'Workout deleted successfully.');
    }

    /**
     * JSON feed consumed by the dashboard's JavaScript (charts, client-side
     * filtering/search, CSV export, and the simulated real-time updates).
     */
    public function data()
    {
        $workouts = Workout::orderByDesc('workout_date')->get([
            'id', 'name', 'type', 'workout_date', 'duration_minutes',
            'calories', 'steps', 'heart_rate', 'status',
        ]);

        return response()->json($workouts);
    }

    /**
     * Streams the currently filtered inventory of workouts as a CSV file.
     * Mirrors the client-side exportToCSV() from Lab 4, but also available
     * as a real server-generated download.
     */
    public function exportCsv(Request $request): StreamedResponse
    {
        $query = Workout::query()
            ->ofType($request->get('type'))
            ->ofStatus($request->get('status'))
            ->search($request->get('search'));

        $filename = 'workouts-export-' . now()->format('Y-m-d_His') . '.csv';

        $columns = ['Activity', 'Type', 'Date', 'Duration (min)', 'Calories', 'Steps', 'Heart Rate', 'Status'];

        $callback = function () use ($query, $columns) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, $columns);

            $query->orderByDesc('workout_date')->chunk(200, function ($rows) use ($handle) {
                foreach ($rows as $row) {
                    fputcsv($handle, [
                        $row->name,
                        $row->type,
                        $row->workout_date->format('M d, Y'),
                        $row->duration_minutes,
                        $row->calories,
                        $row->steps,
                        $row->heart_rate,
                        ucfirst($row->status),
                    ]);
                }
            });

            fclose($handle);
        };

        return response()->streamDownload($callback, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|in:' . implode(',', self::$types),
            'workout_date' => 'required|date',
            'duration_minutes' => 'required|integer|min:1',
            'calories' => 'required|integer|min:0',
            'steps' => 'nullable|integer|min:0',
            'heart_rate' => 'nullable|integer|min:0',
            'status' => 'required|string|in:' . implode(',', self::$statuses),
            'notes' => 'nullable|string',
        ]);
    }
}
