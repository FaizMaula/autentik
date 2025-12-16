<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Backend\DashboardController;

/*
|--------------------------------------------------------------------------
| Backend Routes
|--------------------------------------------------------------------------
|
| Semua route untuk area backend/admin didefinisikan di sini. Secara default
| diprefix dengan /admin dan diberi name prefix admin.*
|
*/

Route::middleware(['web'])
    ->prefix('admin')
    ->as('admin.')
    ->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    });
