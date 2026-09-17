@php
    $old = fn ($field, $default = null) => old($field, $workout->$field ?? $default);
@endphp

<div class="mb-3">
    <label class="form-label">Workout Name</label>
    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ $old('name') }}" required>
    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label class="form-label">Type</label>
        <select name="type" class="form-select @error('type') is-invalid @enderror" required>
            <option value="">Select type</option>
            @foreach ($types as $type)
                <option value="{{ $type }}" @selected($old('type') === $type)>{{ $type }}</option>
            @endforeach
        </select>
        @error('type') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label">Status</label>
        <select name="status" class="form-select @error('status') is-invalid @enderror" required>
            @foreach ($statuses as $status)
                <option value="{{ $status }}" @selected($old('status', $workout->status ?? 'scheduled') === $status)>{{ ucfirst($status) }}</option>
            @endforeach
        </select>
        @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label class="form-label">Date</label>
        <input type="date" name="workout_date" class="form-control @error('workout_date') is-invalid @enderror"
               value="{{ $old('workout_date', $workout->workout_date?->format('Y-m-d') ?? '') }}" required>
        @error('workout_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label">Duration (minutes)</label>
        <input type="number" name="duration_minutes" min="1" class="form-control @error('duration_minutes') is-invalid @enderror" value="{{ $old('duration_minutes') }}" required>
        @error('duration_minutes') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>

<div class="row">
    <div class="col-md-4 mb-3">
        <label class="form-label">Calories</label>
        <input type="number" name="calories" min="0" class="form-control @error('calories') is-invalid @enderror" value="{{ $old('calories') }}" required>
        @error('calories') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-4 mb-3">
        <label class="form-label">Steps</label>
        <input type="number" name="steps" min="0" class="form-control @error('steps') is-invalid @enderror" value="{{ $old('steps') }}">
        @error('steps') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-4 mb-3">
        <label class="form-label">Avg. Heart Rate (bpm)</label>
        <input type="number" name="heart_rate" min="0" class="form-control @error('heart_rate') is-invalid @enderror" value="{{ $old('heart_rate') }}">
        @error('heart_rate') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>

<div class="mb-3">
    <label class="form-label">Notes</label>
    <textarea name="notes" rows="3" class="form-control @error('notes') is-invalid @enderror">{{ $old('notes') }}</textarea>
    @error('notes') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>
