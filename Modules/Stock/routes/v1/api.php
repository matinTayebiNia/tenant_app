<?php

use Illuminate\Support\Facades\Route;
use Modules\Stock\Http\Controllers\V1\StockLevelController;
use Modules\Stock\Http\Controllers\V1\StockMovementController;


Route::prefix('stock-movements')
    ->name('stock_movements.')
    ->group(function () {
        Route::post('/', StockMovementController::class)
            ->name('store');
    });


Route::prefix('stock-levels')->name('stock_levels.')
    ->group(function () {
        Route::get('/', StockLevelController::class)
            ->name('index');
    });
