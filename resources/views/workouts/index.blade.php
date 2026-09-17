@extends('layouts.fittrack')

@section('title', 'Workouts')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <h4 class="fw-bold mb-0">Workouts</h4>
        <a href="{{ route('workouts.create') }}" class="btn ft-btn-primary">
            <i class="bi bi-plus-lg"></i> Log Workout
        </a>
    </div>

    <form method="GET" action="{{ route('workouts.index') }}" class="ft-filter-bar">
        <div class="ft-filter-group">
            <label for="type">Workout Type</label>
            <select name="type" id="type" class="form-select" onchange="this.form.submit()">
                <option value="">All</option>
                @foreach ($types as $type)
                    <option value="{{ $type }}" @selected(request('type') === $type)>{{ $type }}</option>
                @endforeach
            </select>
        </div>
        <div class="ft-filter-group">
            <label for="status">Status</label>
            <select name="status" id="status" class="form-select" onchange="this.form.submit()">
                <option value="">All</option>
                @foreach ($statuses as $status)
                    <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst($status) }}</option>
                @endforeach
            </select>
        </div>
        <div class="ft-filter-group">
            <label for="min_calories">Min Calories</label>
            <input type="number" name="min_calories" id="min_calories" class="form-control" value="{{ request('min_calories') }}" placeholder="e.g. 100">
        </div>
        <div class="ft-filter-group">
            <label for="max_calories">Max Calories</label>
            <input type="number" name="max_calories" id="max_calories" class="form-control" value="{{ request('max_calories') }}" placeholder="e.g. 500">
        </div>
        <div class="ft-filter-group flex-grow-1">
            <label for="search">Search</label>
            <div class="ft-search-wrap">
                <i class="bi bi-search"></i>
                <input type="text" name="search" id="search" class="form-control" value="{{ request('search') }}" placeholder="Search by name...">
            </div>
        </div>
        <div class="ft-filter-group">
            <button type="submit" class="btn ft-btn-primary">Filter</button>
            <a href="{{ route('workouts.index') }}" class="btn btn-light">Reset</a>
        </div>
    </form>

    <div class="ft-panel">
        <div class="table-responsive">
            <table class="table ft-table align-middle">
                <thead>
                    <tr>
                        <th>Activity</th>
                        <th>Type</th>
                        <th>Date</th>
                        <th>Duration</th>
                        <th>Calories</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($workouts as $workout)
                        <tr class="{{ $workout->isMissed() ? 'table-danger bg-opacity-10' : '' }}">
                            <td>{{ $workout->name }}</td>
                            <td>{{ $workout->type }}</td>
                            <td>{{ $workout->workout_date->format('M j, Y') }}</td>
                            <td>{{ $workout->duration_minutes }} min</td>
                            <td>{{ $workout->calories }} kcal</td>
                            <td><span class="ft-badge {{ $workout->statusBadgeClass() }}">{{ ucfirst($workout->status) }}</span></td>
                            <td class="text-end">
                                <a href="{{ route('workouts.show', $workout) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-eye"></i></a>
                                <a href="{{ route('workouts.edit', $workout) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-pencil"></i></a>
                                <form action="{{ route('workouts.destroy', $workout) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this workout?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">No workouts found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $workouts->links() }}
        </div>
    </div>
@endsection
