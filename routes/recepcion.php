<?php

use App\Http\Controllers\IngresoController;
use App\Http\Controllers\PacienteController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Recepcion\EstadisticasEsperaController;

Route::middleware(['auth', 'verified'])->prefix('recepcion')->name('recepcion.')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Pacientes
    |--------------------------------------------------------------------------
    */
    Route::prefix('pacientes')->name('pacientes.')->group(function () {
        Route::get('/', [PacienteController::class, 'index'])->name('index');
        Route::get('/create', [PacienteController::class, 'create'])->name('create');
        Route::post('/', [PacienteController::class, 'store'])->name('store');
        Route::get('/buscar', [PacienteController::class, 'search'])->name('search');
        Route::get('/buscar-ci', [PacienteController::class, 'buscarPorCi'])->name('buscar-ci');
        Route::get('/{paciente}', [PacienteController::class, 'show'])->name('show');
        Route::get('/{paciente}/edit', [PacienteController::class, 'edit'])->name('edit');
        Route::put('/{paciente}', [PacienteController::class, 'update'])->name('update');
    });

    /*
    |--------------------------------------------------------------------------
    | Ingresos
    |--------------------------------------------------------------------------
    */
    Route::prefix('ingresos')->name('ingresos.')->group(function () {
        Route::get('/', [IngresoController::class, 'index'])->name('index');
        Route::get('/create', [IngresoController::class, 'create'])->name('create');
        Route::post('/', [IngresoController::class, 'store'])->name('store');
        Route::get('/buscar-paciente', [IngresoController::class, 'buscarPaciente'])->name('buscar-paciente');
        Route::get('/{ingreso}', [IngresoController::class, 'show'])->name('show');
        Route::post('/{ingreso}/enviar-a-enfermeria', [IngresoController::class, 'enviarAEnfermeria'])->name('enviar-enfermeria');
        Route::post('/{ingreso}/enviar-a-laboratorio', [IngresoController::class, 'enviarALaboratorio'])->name('enviar-laboratorio');
        Route::patch('/{ingreso}/cancelar', [IngresoController::class, 'cancel'])->name('cancel');
       
    });
     Route::get('/panel-espera', [EstadisticasEsperaController::class, 'panelEspera'])
             ->name('espera.panel');
 
});