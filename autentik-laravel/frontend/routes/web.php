<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\App;
use App\Http\Controllers\CertificateController;
use App\Http\Controllers\ResultController;

// Locale switch
Route::get('/locale/{lang}', function (string $lang) {
    $lang = in_array($lang, ['id','en']) ? $lang : 'id';
    Session::put('locale', $lang);
    return back();
});

// Set locale for home page
Route::get('/', function () {
    App::setLocale(Session::get('locale', 'id'));
    return view('home');
});

// Certificate form routes
Route::get('/form', [CertificateController::class, 'create'])->name('certificate.create');  // Menampilkan form
Route::post('/form', [CertificateController::class, 'store'])->name('certificate.store');   // Menyimpan data form

// Results page

Route::post('/certificate/store', [CertificateController::class, 'store'])->name('certificate.store');
Route::get('certificate/result/{id}', [CertificateController::class, 'showResult'])
    ->name('result.show');

Route::get('certificate/api/result/{id}', [CertificateController::class, 'apiResult']);

Route::prefix('api')->group(function () {
    Route::get('/results', [CertificateController::class, 'apiAllResults']);
    Route::get('/results/{id}', [CertificateController::class, 'apiResult']);
});



// Include backend routes
require __DIR__ . '/backend.php';
