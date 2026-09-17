@extends('layouts.fittrack')

@section('title', 'Edit Workout')

@section('content')
    <h4 class="fw-bold mb-3">Edit Workout</h4>

    <div class="ft-panel" style="max-width: 640px;">
        <form method="POST" action="{{ route('workouts.update', $workout) }}">
            @csrf
            @method('PUT')
            @include('workouts._form')

            <div class="mt-3">
                <button type="submit" class="btn ft-btn-primary">Update Workout</button>
                <a href="{{ route('workouts.index') }}" class="btn btn-light">Cancel</a>
            </div>
        </form>
    </div>
@endsection
