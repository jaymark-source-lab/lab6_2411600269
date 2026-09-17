<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class Workout extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'workout_date',
        'duration_minutes',
        'calories',
        'steps',
        'heart_rate',
        'status',
        'notes',
    ];

    protected $casts = [
        'workout_date' => 'date',
        'duration_minutes' => 'integer',
        'calories' => 'integer',
        'steps' => 'integer',
        'heart_rate' => 'integer',
    ];

    // ---- Status helpers -------------------------------------------------

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isMissed(): bool
    {
        return $this->status === 'missed';
    }

    public function isScheduled(): bool
    {
        return $this->status === 'scheduled';
    }

    public function statusBadgeClass(): string
    {
        return match ($this->status) {
            'completed' => 'badge-completed',
            'missed' => 'badge-missed',
            default => 'badge-scheduled',
        };
    }

    // ---- Query scopes -----------------------------------------------------

    public function scopeToday(Builder $query): Builder
    {
        return $query->whereDate('workout_date', Carbon::today());
    }

    public function scopeThisWeek(Builder $query): Builder
    {
        return $query->whereBetween('workout_date', [
            Carbon::now()->startOfWeek(),
            Carbon::now()->endOfWeek(),
        ]);
    }

    public function scopeOfType(Builder $query, ?string $type): Builder
    {
        return $type ? $query->where('type', $type) : $query;
    }

    public function scopeOfStatus(Builder $query, ?string $status): Builder
    {
        return $status ? $query->where('status', $status) : $query;
    }

    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        return $term
            ? $query->where('name', 'like', '%' . $term . '%')
            : $query;
    }
}
