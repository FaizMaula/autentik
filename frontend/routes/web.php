<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\App;
use App\Http\Controllers\CertificateController;
use App\Http\Controllers\ResultController;
use App\Http\Controllers\AuthController;

// Locale switch
Route::get('/locale/{lang}', function (string $lang) {
    $lang = in_array($lang, ['id','en']) ? $lang : 'id';
    Session::put('locale', $lang);
    return back();
});

// Home page - redirect admin to admin dashboard
Route::get('/', function () {
    if (auth()->check() && auth()->user()->isAdmin()) {
        return redirect()->route('admin.dashboard');
    }
    return view('home');
});

// Certificate form routes - only for users, not admins
Route::middleware(['auth', 'user'])->group(function () {

    Route::get('/form', [CertificateController::class, 'create'])
        ->name('certificate.create');

    Route::post('/form', [CertificateController::class, 'store'])
        ->name('certificate.store');
    
    Route::get('/history', [CertificateController::class, 'history'])
        ->name('certificate.history');

});

// Results page

// Route::post('/certificate/store', [CertificateController::class, 'store'])->name('certificate.store');
Route::get('certificate/result/{id}', [CertificateController::class, 'showResult'])
    ->name('result.show');

Route::get('certificate/api/result/{id}', [CertificateController::class, 'apiResult']);

Route::prefix('api')->group(function () {
    Route::get('/results', [CertificateController::class, 'apiAllResults']);
    Route::get('/results/{id}', [CertificateController::class, 'apiResult']);
});

// Register
Route::get('/register', [AuthController::class, 'showRegister'])
    ->name('register');

Route::post('/register', [AuthController::class, 'register'])
    ->name('register.store');

// Login
Route::get('/login', [AuthController::class, 'showLogin'])
    ->name('login');

Route::post('/login', [AuthController::class, 'login'])
    ->name('login.store');

// Logout
Route::post('/logout', [AuthController::class, 'logout'])
    ->name('logout');


// Include backend routes
require __DIR__ . '/backend.php';

// Include admin routes
require __DIR__ . '/admin.php';
