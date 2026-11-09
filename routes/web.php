<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\WorkoutController;
use App\Http\Controllers\InventoryTransactionController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\StockAdjustmentController;
use App\Http\Controllers\Api\InventoryApiController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])->name('dashboard');

// logged-in users only
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('products', ProductController::class);

    // stock movements (no edit/update: audit trail)
    Route::resource('transactions', InventoryTransactionController::class)->except(['edit', 'update']);

    // stock adjustments (admin only, checked in the controller)
    Route::get('/adjustments/create', [StockAdjustmentController::class, 'create'])->name('adjustments.create');
    Route::post('/adjustments', [StockAdjustmentController::class, 'store'])->name('adjustments.store');

    // low stock notifications
    Route::post('/notifications/read-all', [NotificationController::class, 'readAll'])->name('notifications.readAll');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'read'])->name('notifications.read');

    // JSON endpoints for the live dashboard
    Route::prefix('api')->name('api.')->group(function () {
        Route::get('/dashboard/stats', [InventoryApiController::class, 'stats'])->name('dashboard.stats');
        Route::get('/products/low-stock', [InventoryApiController::class, 'lowStock'])->name('products.lowStock');
        Route::get('/products/{product}', [InventoryApiController::class, 'product'])->whereNumber('product')->name('products.show');
        Route::get('/transactions/recent', [InventoryApiController::class, 'recentTransactions'])->name('transactions.recent');
    });

    Route::resource('workouts', WorkoutController::class);
    Route::get('/workouts-data', [WorkoutController::class, 'data'])->name('workouts.data');
    Route::get('/workouts-export', [WorkoutController::class, 'exportCsv'])->name('workouts.export');
});

require __DIR__.'/auth.php';