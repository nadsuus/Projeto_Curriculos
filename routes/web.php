<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CandidaturaController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\CandidaturaAdminController;

// Página inicial → redireciona para o formulário de candidaturas
Route::get('/', fn () => redirect()->route('candidaturas.create'));

// Formulário e envio
Route::get('/candidaturas/create', [CandidaturaController::class, 'create'])
    ->name('candidaturas.create');

Route::post('/candidaturas', [CandidaturaController::class, 'store'])
    ->name('candidaturas.store')
    ->middleware('throttle:5,1');

// Rotas Breeze (autenticação)
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Admin router

Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/candidaturas', [CandidaturaAdminController::class, 'index'])
        ->name('candidaturas.index');

    Route::get('/candidaturas/{candidatura}/download', [CandidaturaAdminController::class, 'download'])
        ->name('candidaturas.download');
});

require __DIR__.'/auth.php';