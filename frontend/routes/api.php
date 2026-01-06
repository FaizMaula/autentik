<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ResultController;
use App\Http\Controllers\CertificateController;


Route::middleware('api')->group(function () {
    //
});

// API endpoint for checking certificate status (used by polling)
Route::post('/certificates/check-status', [CertificateController::class, 'checkStatus'])
    ->middleware('web');
