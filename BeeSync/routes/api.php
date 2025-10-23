<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardChartController;

Route::middleware('auth')->group(function () {
    Route::get('/chart-data', [DashboardChartController::class, 'getChartData'])
        ->name('api.chart.data');
});
