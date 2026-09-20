<?php

use Illuminate\Support\Facades\Route;
use Modules\Log\Http\Controllers\V1\Log\IndexController as LogIndex;

Route::get('/logs', LogIndex::class)->name('logs.index');
