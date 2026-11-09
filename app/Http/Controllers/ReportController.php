<?php

namespace App\Http\Controllers;

use App\Models\InventoryTransaction;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ReportController extends Controller
{
    // available reports (key => label)
    public const REPORTS = [
        'movement'  => 'Stock Movement',
        'low-stock' => 'Low Stock',
        'valuation' => 'Inventory Valuation',
    ];

    // show the selected report
    public function index(Request $request)
    {
        [$report, $from, $to] = $this->filters($request);

        return view('reports.index', array_merge($this->buildReport($report, $from, $to), [
            'report'  => $report,
            'from'    => $from,
            'to'      => $to,
            'reports' => self::REPORTS,
        ]));
    }

    // download the selected report as CSV
    public function export(Request $request)
    {
        [$report, $from, $to] = $this->filters($request);

        $rows = $this->csvRows($report, $this->buildReport($report, $from, $to));
        $filename = "{$report}-report-" . now()->format('Y-m-d') . '.csv';

        return response()->streamDownload(function () use ($rows) {
            $output = fopen('php://output', 'w');

            foreach ($rows as $row) {
                fputcsv($output, $row);
            }

            fclose($output);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    // read the report type and date range (default: last 30 days)
    private function filters(Request $request): array
    {
        $validated = $request->validate([
            'report' => ['nullable', Rule::in(array_keys(self::REPORTS))],
            'from'   => ['nullable', 'date'],
            'to'     => ['nullable', 'date'],
        ]);

        $report = $validated['report'] ?? 'movement';
        $from = $validated['from'] ?? now()->subDays(30)->toDateString();
        $to = $validated['to'] ?? now()->toDateString();

        // swap if entered backwards
        if ($from > $to) {
            [$from, $to] = [$to, $from];
        }

        return [$report, $from, $to];
    }

    // pick which report to build
    private function buildReport(string $report, string $from, string $to): array
    {
        return match ($report) {
            'low-stock' => $this->lowStock(),
            'valuation' => $this->valuation(),
            default     => $this->movement($from, $to),
        };
    }

    // all transactions in the date range + totals
    private function movement(string $from, string $to): array
    {
        $transactions = InventoryTransaction::with(['product', 'user'])
            ->whereDate('created_at', '>=', $from)
            ->whereDate('created_at', '<=', $to)
            ->recent()
            ->get();

        return [
            'transactions' => $transactions,
            'summary'      => [
                'count'       => $transactions->count(),
                'stock_in'    => $transactions->where('type', 'stock_in')->sum('quantity'),
                'stock_out'   => abs($transactions->where('type', 'stock_out')->sum('quantity')),
                'adjustments' => $transactions->where('type', 'adjustment')->sum('quantity'),
            ],
        ];
    }

    // products at or below reorder level
    private function lowStock(): array
    {
        return [
            'products' => Product::lowStock()->orderBy('quantity')->get(),
        ];
    }

    // value of all stock, per product and per category
    private function valuation(): array
    {
        $products = Product::orderBy('category')->orderBy('name')->get();

        $byCategory = $products->groupBy('category')->map(fn ($items) => [
            'products' => $items->count(),
            'units'    => $items->sum('quantity'),
            'value'    => $items->sum(fn ($p) => $p->quantity * $p->unit_price),
        ]);

        return [
            'products'   => $products,
            'byCategory' => $byCategory,
            'totalUnits' => $products->sum('quantity'),
            'totalValue' => $byCategory->sum('value'),
        ];
    }

    // turn a report into CSV rows (first row = headers)
    private function csvRows(string $report, array $data): array
    {
        return match ($report) {
            'low-stock' => array_merge(
                [['Product', 'SKU', 'Category', 'Quantity', 'Reorder Level', 'Shortfall', 'Supplier']],
                $data['products']->map(fn ($p) => [
                    $p->name, $p->sku, $p->category, $p->quantity, $p->reorder_level,
                    max($p->reorder_level - $p->quantity, 0), $p->supplier,
                ])->all()
            ),
            'valuation' => array_merge(
                [['Product', 'SKU', 'Category', 'Quantity', 'Unit Price', 'Total Value']],
                $data['products']->map(fn ($p) => [
                    $p->name, $p->sku, $p->category, $p->quantity,
                    number_format($p->unit_price, 2, '.', ''),
                    number_format($p->quantity * $p->unit_price, 2, '.', ''),
                ])->all(),
                [['TOTAL', '', '', $data['totalUnits'], '', number_format($data['totalValue'], 2, '.', '')]]
            ),
            default => array_merge(
                [['Date', 'Product', 'SKU', 'Type', 'Quantity', 'Reference', 'Recorded By', 'Notes']],
                $data['transactions']->map(fn ($t) => [
                    $t->created_at->format('Y-m-d H:i'), $t->product->name, $t->product->sku,
                    InventoryTransaction::TYPES[$t->type], $t->quantity,
                    $t->reference_number, $t->user->name, $t->notes,
                ])->all()
            ),
        };
    }
}