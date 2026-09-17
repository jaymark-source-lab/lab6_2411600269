@extends('layouts.fittrack')

@section('title', 'Workout Details')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold mb-0">{{ $workout->name }}</h4>
        <span class="ft-badge {{ $workout->statusBadgeClass() }}">{{ ucfirst($workout->status) }}</span>
    </div>

    <div class="ft-panel" style="max-width: 640px;">
        <dl class="row mb-0">
            <dt class="col-sm-4 text-muted">Type</dt>
            <dd class="col-sm-8">{{ $workout->type }}</dd>

            <dt class="col-sm-4 text-muted">Date</dt>
            <dd class="col-sm-8">{{ $workout->workout_date->format('F j, Y') }}</dd>

            <dt class="col-sm-4 text-muted">Duration</dt>
            <dd class="col-sm-8">{{ $workout->duration_minutes }} minutes</dd>

            <dt class="col-sm-4 text-muted">Calories</dt>
            <dd class="col-sm-8">{{ $workout->calories }} kcal</dd>

            <dt class="col-sm-4 text-muted">Steps</dt>
            <dd class="col-sm-8">{{ $workout->steps ? number_format($workout->steps) : '—' }}</dd>

            <dt class="col-sm-4 text-muted">Avg. Heart Rate</dt>
            <dd class="col-sm-8">{{ $workout->heart_rate ? $workout->heart_rate . ' bpm' : '—' }}</dd>

            @if ($workout->notes)
                <dt class="col-sm-4 text-muted">Notes</dt>
                <dd class="col-sm-8">{{ $workout->notes }}</dd>
            @endif
        </dl>

        <div class="mt-4">
            <a href="{{ route('workouts.edit', $workout) }}" class="btn ft-btn-primary"><i class="bi bi-pencil"></i> Edit</a>
            <a href="{{ route('workouts.index') }}" class="btn btn-light">Back to Workouts</a>
        </div>
    </div>
@endsection
