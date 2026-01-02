<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\EventController;

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
|
| Here is where you can register admin routes for your application.
| These routes are protected by the 'admin' middleware.
|
*/

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    
    // Dashboard
    Route::get('/', function () {
        return view('admin.dashboard');
    })->name('dashboard');

    // Events management (upload Excel)
    Route::get('/events', [EventController::class, 'index'])->name('events.index');
    Route::get('/events/upload', [EventController::class, 'create'])->name('events.create');
    Route::post('/events', [EventController::class, 'store'])->name('events.store');
    Route::get('/events/{event}', [EventController::class, 'show'])->name('events.show');
    Route::delete('/events/{event}', [EventController::class, 'destroy'])->name('events.destroy');
    
    // Download template
    Route::get('/events/template/download', [EventController::class, 'downloadTemplate'])->name('events.template');

    // All history (admin can see all verification history)
    Route::get('/history', [EventController::class, 'allHistory'])->name('history.all');
    
    // View certificate result (admin can view any user's certificate)
    Route::get('/certificate/{id}/result', [EventController::class, 'showCertificateResult'])->name('result.show');
});
