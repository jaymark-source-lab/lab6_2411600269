@extends('layouts.fittrack')

@section('title', 'Overview')

@section('content')
    <h4 class="fw-bold mb-1">Good {{ now()->hour < 12 ? 'morning' : (now()->hour < 18 ? 'afternoon' : 'evening') }}, {{ auth()->user()->name ?? 'Admin' }}!</h4>
    <p class="text-muted mb-4">Here's your inventory and fitness summary for today.</p>

    {{-- ================= Inventory (live) ================= --}}
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
        <h5 class="fw-bold mb-0"><i class="bi bi-box-seam"></i> Inventory Overview</h5>
        <div class="d-flex align-items-center gap-2 small text-muted">
            <span><span class="ft-live-dot" id="live-dot"></span><span id="refresh-status">Loading...</span></span>
            <button type="button" id="refresh-now" class="btn btn-sm btn-light"><i class="bi bi-arrow-clockwise"></i> Refresh</button>
        </div>
    </div>

    {{-- Inventory stat cards --}}
    <div class="row g-3">
        <div class="col-6 col-lg-3">
            <div class="ft-stat-card">
                <div class="ft-stat-icon teal"><i class="bi bi-box-seam"></i></div>
                <div class="ft-stat-label">Total Products</div>
                <div class="ft-stat-value" id="inv-total-products">—</div>
                <div class="ft-stat-delta" id="inv-total-units"></div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="ft-stat-card">
                <div class="ft-stat-icon orange"><i class="bi bi-exclamation-triangle"></i></div>
                <div class="ft-stat-label">Low Stock Items</div>
                <div class="ft-stat-value" id="inv-low-stock">—</div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="ft-stat-card">
                <div class="ft-stat-icon purple"><i class="bi bi-x-octagon"></i></div>
                <div class="ft-stat-label">Out of Stock</div>
                <div class="ft-stat-value" id="inv-out-of-stock">—</div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="ft-stat-card">
                <div class="ft-stat-icon teal-light"><i class="bi bi-cash-stack"></i></div>
                <div class="ft-stat-label">Inventory Value</div>
                <div class="ft-stat-value" id="inv-value">—</div>
                <div class="ft-stat-delta" id="inv-transactions-today"></div>
            </div>
        </div>
    </div>

    {{-- Low stock alerts + recent transactions --}}
    <div class="row g-3 mt-1">
        <div class="col-lg-5">
            <div class="ft-panel">
                <div class="ft-panel-title">
                    <i class="bi bi-bell-fill text-warning"></i> Low Stock Alerts
                    <span class="badge rounded-pill bg-danger" id="low-stock-count">0</span>
                </div>
                <ul class="list-group list-group-flush" id="low-stock-list">
                    <li class="list-group-item text-muted">Loading...</li>
                </ul>
            </div>
        </div>
        <div class="col-lg-7">
            <div class="ft-panel">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div class="ft-panel-title mb-0"><i class="bi bi-arrow-left-right"></i> Recent Transactions</div>
                    <a href="{{ route('transactions.index') }}" class="small text-decoration-none">View all</a>
                </div>
                <div class="table-responsive">
                    <table class="table ft-table align-middle mb-0">
                        <thead>
                            <tr>
                                <th>When</th>
                                <th>Product</th>
                                <th>Type</th>
                                <th class="text-end">Qty</th>
                                <th>By</th>
                            </tr>
                        </thead>
                        <tbody id="recent-transactions-body">
                            <tr><td colspan="5" class="text-center text-muted py-3">Loading...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- ================= Fitness ================= --}}
    <h5 class="fw-bold mt-5 mb-3"><i class="bi bi-activity"></i> Fitness Summary</h5>

    {{-- Stat cards --}}
    <div class="row g-3">
        <div class="col-6 col-lg-3">
            <div class="ft-stat-card">
                <div class="ft-stat-icon teal"><i class="bi bi-shoe-prints"></i></div>
                <div class="ft-stat-label">Steps Today</div>
                <div class="ft-stat-value" id="stat-steps">{{ number_format($stepsToday) }}</div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="ft-stat-card">
                <div class="ft-stat-icon orange"><i class="bi bi-fire"></i></div>
                <div class="ft-stat-label">Calories Burned</div>
                <div class="ft-stat-value" id="stat-calories">{{ number_format($caloriesToday) }} kcal</div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="ft-stat-card">
                <div class="ft-stat-icon teal-light"><i class="bi bi-heart-pulse"></i></div>
                <div class="ft-stat-label">Workouts This Week</div>
                <div class="ft-stat-value" id="stat-workouts">{{ $workoutsThisWeek }}</div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="ft-stat-card">
                <div class="ft-stat-icon purple"><i class="bi bi-activity"></i></div>
                <div class="ft-stat-label">Avg. Heart Rate</div>
                <div class="ft-stat-value" id="stat-heart-rate">{{ $avgHeartRate }} bpm</div>
            </div>
        </div>
    </div>

    {{-- Missed workouts alert --}}
    <div class="ft-alert-banner" id="missed-alert" style="{{ $missedWorkouts->isEmpty() ? 'display:none;' : '' }}">
        <i class="bi bi-exclamation-triangle-fill"></i>
        <div>
            <strong>You have <span id="missed-count">{{ $missedWorkouts->count() }}</span> missed workouts</strong>
            <ul id="missed-list">
                @foreach ($missedWorkouts as $missed)
                    <li>{{ $missed->name }} ({{ $missed->type }}, {{ $missed->workout_date->format('M j, Y') }})</li>
                @endforeach
            </ul>
        </div>
    </div>

    {{-- Charts --}}
    <div class="row g-3 mt-1">
        <div class="col-lg-7">
            <div class="ft-panel">
                <div class="ft-panel-title"><i class="bi bi-bar-chart-fill text-warning"></i> Calories Burned by Workout Type</div>
                <canvas id="caloriesByTypeChart" height="220"></canvas>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="ft-panel">
                <div class="ft-panel-title"><i class="bi bi-pie-chart-fill"></i> Workout Status Distribution</div>
                <canvas id="statusDistributionChart" height="220"></canvas>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="ft-filter-bar mt-4">
        <div class="ft-filter-group">
            <label for="filter-type">Workout Type</label>
            <select id="filter-type" class="form-select">
                <option value="">All</option>
                @foreach ($types as $type)
                    <option value="{{ $type }}">{{ $type }}</option>
                @endforeach
            </select>
        </div>
        <div class="ft-filter-group">
            <label>Status</label>
            <div class="ft-status-pills" id="status-pills">
                <button type="button" class="btn btn-sm btn-outline-secondary active-pill" data-status="">All</button>
                <button type="button" class="btn btn-sm btn-outline-secondary" data-status="completed">Completed</button>
                <button type="button" class="btn btn-sm btn-outline-secondary" data-status="scheduled">Scheduled</button>
                <button type="button" class="btn btn-sm btn-outline-secondary" data-status="missed">Missed</button>
            </div>
        </div>
        <div class="ft-filter-group">
            <label for="filter-min-calories">Min Calories</label>
            <input type="number" id="filter-min-calories" class="form-control" placeholder="e.g. 100">
        </div>
        <div class="ft-filter-group">
            <label for="filter-max-calories">Max Calories</label>
            <input type="number" id="filter-max-calories" class="form-control" placeholder="e.g. 500">
        </div>
        <div class="ft-filter-group">
            <button type="button" id="reset-filters" class="btn btn-light"><i class="bi bi-arrow-counterclockwise"></i> Reset</button>
        </div>
    </div>

    {{-- Recent activity table --}}
    <div class="ft-panel">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
            <div class="ft-panel-title mb-0"><i class="bi bi-clock-history"></i> Recent Activity</div>
            <div class="d-flex gap-2 align-items-center">
                <div class="ft-search-wrap">
                    <i class="bi bi-search"></i>
                    <input type="text" id="search-input" class="form-control" placeholder="Search by workout name...">
                </div>
                <button type="button" id="export-csv" class="btn ft-btn-export"><i class="bi bi-download"></i> Export CSV</button>
            </div>
        </div>

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
                        <th></th>
                    </tr>
                </thead>
                <tbody id="activity-table-body">
                    {{-- Rendered client-side by fittrack-dashboard.js --}}
                </tbody>
            </table>
            <p class="text-muted text-center py-3 d-none" id="no-results">No workouts match your filters.</p>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        window.FT_WORKOUTS_DATA_URL = @json(route('workouts.data'));
        window.FT_WORKOUT_SHOW_URL_BASE = @json(url('/workouts'));

        // inventory API endpoints for the live dashboard
        window.FT_INVENTORY_API = {
            stats: @json(route('api.dashboard.stats')),
            lowStock: @json(route('api.products.lowStock')),
            recent: @json(route('api.transactions.recent')),
        };
    </script>
    <script src="{{ asset('js/fittrack-dashboard.js') }}"></script>
    <script src="{{ asset('js/inventory-dashboard.js') }}"></script>
@endpush