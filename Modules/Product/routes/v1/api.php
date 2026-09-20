<?php


use Illuminate\Support\Facades\Route;
use Modules\Product\Http\Controllers\V1\ProductController;

Route::prefix('products')
    ->name('products.')
    ->controller(ProductController::class)
    ->group(function () {
        Route::get('/', 'index')
            ->name('index');

        Route::get('/{product}', 'show')
            ->middleware('modelBelongsToScope:product')
            ->name('show');

        Route::post('/', 'store')
            ->name('store');

        Route::put('/{product}', 'update')
            ->middleware('modelBelongsToScope:product')
            ->name('update');

        Route::delete('/{product}', 'destroy')
            ->middleware('modelBelongsToScope:product')
            ->name('destroy');

        Route::get('/{sku}/history', 'history')
            ->name('history');

    });
