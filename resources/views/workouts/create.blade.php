@extends('layouts.fittrack')

@section('title', 'Log Workout')

@section('content')
    <h4 class="fw-bold mb-3">Log a New Workout</h4>

    <div class="ft-panel" style="max-width: 640px;">
        <form method="POST" action="{{ route('workouts.store') }}">
            @csrf
            @include('workouts._form', ['workout' => null])

            <div class="mt-3">
                <button type="submit" class="btn ft-btn-primary">Save Workout</button>
                <a href="{{ route('workouts.index') }}" class="btn btn-light">Cancel</a>
            </div>
        </form>
    </div>
@endsection
