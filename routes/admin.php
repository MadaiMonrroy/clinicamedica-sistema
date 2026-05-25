<?php

use App\Http\Controllers\AreaController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\ExamenController;
use App\Services\ArchivoOptimizerService;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->prefix('admin')->name('admin.')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Gestión de Personal
    |--------------------------------------------------------------------------
    */
    Route::prefix('personal')->name('staff.')->group(function () {
        Route::get('/', [StaffController::class, 'index'])->name('index');
        Route::post('/', [StaffController::class, 'store'])->name('store');
        Route::patch('/{user}', [StaffController::class, 'update'])->name('update');
        Route::patch('/{user}/toggle-active', [StaffController::class, 'toggleActive'])->name('toggle-active');
Route::post('/{user}/resend-access', [StaffController::class, 'resendAccess'])->name('resendAccess');
    });

    /*
    |--------------------------------------------------------------------------
    | Gestión de Áreas
    |--------------------------------------------------------------------------
    */
    Route::prefix('areas')->name('areas.')->group(function () {
        Route::get('/', [AreaController::class, 'index'])->name('index');
        Route::get('/create', [AreaController::class, 'create'])->name('create');
        Route::post('/', [AreaController::class, 'store'])->name('store');
        Route::get('/{area}/edit', [AreaController::class, 'edit'])->name('edit');
        Route::put('/{area}', [AreaController::class, 'update'])->name('update');
        Route::delete('/{area}', [AreaController::class, 'destroy'])->name('destroy');
        Route::patch('/{area}/toggle', [AreaController::class, 'toggle'])->name('toggle');
    });
    Route::prefix('examenes')->name('examenes.')->group(function () {
    Route::get('/', [ExamenController::class, 'index'])->name('index');
    Route::get('/create', [ExamenController::class, 'create'])->name('create');
    Route::post('/', [ExamenController::class, 'store'])->name('store');
    Route::get('/{examen}/edit', [ExamenController::class, 'edit'])->name('edit');
    Route::put('/{examen}', [ExamenController::class, 'update'])->name('update');
    Route::patch('/{examen}/toggle', [ExamenController::class, 'toggle'])->name('toggle');
    Route::delete('/{examen}', [ExamenController::class, 'destroy'])->name('destroy');
});
});