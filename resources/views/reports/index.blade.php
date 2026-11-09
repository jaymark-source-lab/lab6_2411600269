@extends('layouts.fittrack')

@section('title', 'Reports')

@push('styles')
<style>
    /* hide app chrome when printing a report */
    @media print {
        .ft-sidebar, .ft-topbar, .ft-report-actions, .ft-report-tabs, .ft-report-filter { display: none !important; }
        .ft-content { padding: 0; }
    }
</style>
@endpush

@section('content')
    @php
        // badge styles
        $typeBadge = [
            'stock_in'   => 'badge-completed',
            'stock_out'  => 'badge-missed',
            'adjustment' => 'badge-scheduled',
        ];
        $statusBadge = [
            'danger'  => 'badge-missed',
            'warning' => 'badge-scheduled',
            'success' => 'badge-completed',
        ];
    @endphp

    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <div>
            <h4 class="fw-bold mb-0">{{ $reports[$report] }} Report</h4>
            <div class="small text-muted">
                Generated {{ now()->format('M j, Y g:i A') }}
                @if ($report === 'movement')
                    · {{ \Carbon\Carbon::parse($from)->format('M j, Y') }} to {{ \Carbon\Carbon::parse($to)->format('M j, Y') }}
                @endif
            </div>
        </div>
        <div class="d-flex gap-2 ft-report-actions">
            <a href="{{ route('reports.export', ['report' => $report, 'from' => $from, 'to' => $to]) }}" class="btn ft-btn-export">
                <i class="bi bi-download"></i> Export CSV
            </a>
            <button type="button" class="btn btn-light" onclick="window.print()">
                <i class="bi bi-printer"></i> Print / PDF
            </button>
        </div>
    </div>

    {{-- report tabs --}}
    <div class="d-flex flex-wrap gap-2 mb-3 ft-report-tabs">
        @foreach ($reports as $key => $label)
            <a href="{{ route('reports.index', ['report' => $key, 'from' => $from, 'to' => $to]) }}"
               class="btn {{ $report === $key ? 'ft-btn-primary' : 'btn-light' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>

    {{-- ===== Stock Movement ===== --}}
    @if ($report === 'movement')
        <form method="GET" action="{{ route('reports.index') }}" class="ft-filter-bar ft-report-filter">
            <input type="hidden" name="report" value="movement">
            <div class="ft-filter-group">
                <label for="from">From</label>
                <input type="date" name="from" id="from" class="form-control" value="{{ $from }}">
            </div>
            <div class="ft-filter-group">
                <label for="to">To</label>
                <input type="date" name="to" id="to" class="form-control" value="{{ $to }}">
            </div>
            <div class="ft-filter-group">
                <button type="submit" class="btn ft-btn-primary">Apply</button>
            </div>
        </form>

        <div class="row g-3 mb-3">
            <div class="col-6 col-lg-3">
                <div class="ft-stat-card">
                    <div class="ft-stat-label">Transactions</div>
                    <div class="ft-stat-value">{{ $summary['count'] }}</div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="ft-stat-card">
                    <div class="ft-stat-label">Units In</div>
                    <div class="ft-stat-value text-success">+{{ $summary['stock_in'] }}</div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="ft-stat-card">
                    <div class="ft-stat-label">Units Out</div>
                    <div class="ft-stat-value text-danger">−{{ $summary['stock_out'] }}</div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="ft-stat-card">
                    <div class="ft-stat-label">Net Adjustments</div>
                    <div class="ft-stat-value">{{ $summary['adjustments'] > 0 ? '+' : '' }}{{ $summary['adjustments'] }}</div>
                </div>
            </div>
        </div>

        <div class="ft-panel">
            <div class="table-responsive">
                <table class="table ft-table align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Product</th>
                            <th>Type</th>
                            <th class="text-end">Qty</th>
                            <th>Reference</th>
                            <th>By</th>
                            <th>Notes</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($transactions as $transaction)
                            <tr>
                                <td class="small text-nowrap">{{ $transaction->created_at->format('M j, Y g:i A') }}</td>
                                <td>{{ $transaction->product->name }} <div class="small text-muted">{{ $transaction->product->sku }}</div></td>
                                <td><span class="ft-badge {{ $typeBadge[$transaction->type] }} text-nowrap">{{ App\Models\InventoryTransaction::TYPES[$transaction->type] }}</span></td>
                                <td class="text-end fw-semibold {{ $transaction->quantity > 0 ? 'text-success' : 'text-danger' }}">
                                    {{ $transaction->quantity > 0 ? '+' : '' }}{{ $transaction->quantity }}
                                </td>
                                <td class="small">{{ $transaction->reference_number ?: '—' }}</td>
                                <td class="small">{{ $transaction->user->name }}</td>
                                <td class="small text-muted">{{ $transaction->notes ?: '—' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="text-center text-muted py-4">No transactions in this period.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    {{-- ===== Low Stock ===== --}}
    @if ($report === 'low-stock')
        <div class="ft-panel">
            <div class="table-responsive">
                <table class="table ft-table align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Category</th>
                            <th class="text-end">Quantity</th>
                            <th class="text-end">Reorder Level</th>
                            <th class="text-end">Shortfall</th>
                            <th>Supplier</th>
                            <th>Status</th>
                            <th class="text-end ft-report-actions">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($products as $product)
                            <tr>
                                <td>{{ $product->name }} <div class="small text-muted">{{ $product->sku }}</div></td>
                                <td>{{ $product->category }}</td>
                                <td class="text-end fw-bold">{{ $product->quantity }}</td>
                                <td class="text-end">{{ $product->reorder_level }}</td>
                                <td class="text-end text-danger">{{ max($product->reorder_level - $product->quantity, 0) }}</td>
                                <td>{{ $product->supplier ?: '—' }}</td>
                                <td><span class="ft-badge {{ $statusBadge[$product->stock_status_color] }} text-nowrap">{{ $product->stock_status }}</span></td>
                                <td class="text-end ft-report-actions">
                                    <a href="{{ route('transactions.create', ['product_id' => $product->id]) }}" class="btn btn-sm ft-btn-primary text-nowrap">
                                        <i class="bi bi-plus-lg"></i> Restock
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="text-center text-muted py-4">All products are above their reorder level. 🎉</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    {{-- ===== Inventory Valuation ===== --}}
    @if ($report === 'valuation')
        <div class="row g-3">
            <div class="col-lg-5">
                <div class="ft-panel">
                    <div class="ft-panel-title"><i class="bi bi-pie-chart"></i> By Category</div>
                    <table class="table ft-table align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Category</th>
                                <th class="text-end">Units</th>
                                <th class="text-end">Value</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($byCategory as $category => $row)
                                <tr>
                                    <td>{{ $category }} <div class="small text-muted">{{ $row['products'] }} products</div></td>
                                    <td class="text-end">{{ number_format($row['units']) }}</td>
                                    <td class="text-end">${{ number_format($row['value'], 2) }}</td>
                                </tr>
                            @endforeach
                            <tr class="fw-bold">
                                <td>Total</td>
                                <td class="text-end">{{ number_format($totalUnits) }}</td>
                                <td class="text-end">${{ number_format($totalValue, 2) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="col-lg-7">
                <div class="ft-panel">
                    <div class="ft-panel-title"><i class="bi bi-list-ul"></i> By Product</div>
                    <div class="table-responsive">
                        <table class="table ft-table align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th class="text-end">Qty</th>
                                    <th class="text-end">Unit Price</th>
                                    <th class="text-end">Value</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($products as $product)
                                    <tr>
                                        <td>{{ $product->name }} <div class="small text-muted">{{ $product->category }}</div></td>
                                        <td class="text-end">{{ $product->quantity }}</td>
                                        <td class="text-end">${{ number_format($product->unit_price, 2) }}</td>
                                        <td class="text-end fw-semibold">${{ number_format($product->quantity * $product->unit_price, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection