<?php

use App\Http\Controllers\AtencionController;
use App\Http\Controllers\InterconsultaController;
use App\Http\Controllers\DiagnosticoController;
use App\Http\Controllers\LaboratorioController;
use App\Http\Controllers\OrdenMedicaController;
use App\Http\Controllers\RecetaMedicaController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\EnfermeriaController;
use App\Http\Controllers\PacienteAlergiaController; // <--- AGREGA ESTA LÍNEA AL PRINCIPIO
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified', 'role:admin,medico'])->prefix('')->group(function () {


    /*
    |--------------------------------------------------------------------------
    | Enfermeria
    |--------------------------------------------------------------------------
    */
    Route::prefix('enfermeria')->name('enfermeria.')->group(function () {
        Route::get('/', [ EnfermeriaController::class, 'index'])->name('index');
        Route::get('/pendientes', [ EnfermeriaController::class, 'pending'])->name('pending');
        Route::get('/{ingreso}/atender', [ EnfermeriaController::class, 'create'])->name('create');
        Route::post('/{ingreso}', [ EnfermeriaController::class, 'store'])->name('store');
        Route::post('/{ingreso}/generar-ticket', [ EnfermeriaController::class, 'generarTicket'])->name('generar-ticket');
    });

    /*
    |--------------------------------------------------------------------------
    | Tickets / Turnos
    |--------------------------------------------------------------------------
    */
    Route::prefix('tickets')->name('tickets.')->group(function () {
        Route::get('/', [TicketController::class, 'index'])->name('index');
        Route::get('/cola/{area}', [TicketController::class, 'queueByArea'])->name('queue-area');
        Route::get('/{ticket}', [TicketController::class, 'show'])->name('show');
        Route::patch('/{ticket}/llamar', [TicketController::class, 'call'])->name('call');
        Route::patch('/{ticket}/en-turno', [TicketController::class, 'setInTurn'])->name('set-in-turn');
        Route::patch('/{ticket}/finalizar', [TicketController::class, 'finish'])->name('finish');
        Route::patch('/{ticket}/cancelar', [TicketController::class, 'cancel'])->name('cancel');
    });

    /*
    |--------------------------------------------------------------------------
    | Atenciones
    |--------------------------------------------------------------------------
    */
    Route::prefix('atenciones')->name('atenciones.')->group(function () {
        Route::get('/', [AtencionController::class, 'index'])->name('index');
        Route::get('/{ticket}/create', [AtencionController::class, 'create'])->name('create');
        Route::post('/{ticket}', [AtencionController::class, 'store'])->name('store');
        Route::get('/detalle/{atencion}', [AtencionController::class, 'show'])->name('show');
        Route::get('/detalle/{atencion}/edit', [AtencionController::class, 'edit'])->name('edit');
        Route::put('/detalle/{atencion}', [AtencionController::class, 'update'])->name('update');
        Route::patch('/detalle/{atencion}/finalizar', [AtencionController::class, 'finish'])->name('finish');
        Route::patch('/detalle/{atencion}/pasar-observacion', [AtencionController::class, 'pasarAObservacion'])->name('pasar-observacion');
        Route::get('/crear/{atencion}', [RecetaMedicaController::class, 'create'])->name('create');
Route::get('/crear/{atencion}', [OrdenMedicaController::class, 'create'])->name('create');
    });

    /*
    |--------------------------------------------------------------------------
    | Diagnósticos
    |--------------------------------------------------------------------------
    */
    Route::prefix('diagnosticos')->name('diagnosticos.')->group(function () {
        Route::post('/{atencion}', [DiagnosticoController::class, 'store'])->name('store');
        Route::put('/{diagnostico}', [DiagnosticoController::class, 'update'])->name('update');
        Route::delete('/{diagnostico}', [DiagnosticoController::class, 'destroy'])->name('destroy');
    });

    /*
    |--------------------------------------------------------------------------
    | Recetas médicas
    |--------------------------------------------------------------------------
    */
    Route::prefix('recetas')->name('recetas.')->group(function () {
        Route::get('/', [RecetaMedicaController::class, 'index'])->name('index');
        Route::get('/crear/{atencion}', [RecetaMedicaController::class, 'create'])->name('create');
        Route::post('/{atencion}', [RecetaMedicaController::class, 'store'])->name('store');
        Route::get('/{receta}', [RecetaMedicaController::class, 'show'])->name('show');
        Route::put('/{receta}', [RecetaMedicaController::class, 'update'])->name('update');
        Route::patch('/{receta}/anular', [RecetaMedicaController::class, 'annul'])->name('annul');
    });

    /*
    |--------------------------------------------------------------------------
    | Órdenes médicas
    |--------------------------------------------------------------------------
    */
    Route::prefix('ordenes-medicas')->name('ordenes-medicas.')->group(function () {
        Route::get('/', [OrdenMedicaController::class, 'index'])->name('index');
        Route::post('/{atencion}', [OrdenMedicaController::class, 'store'])->name('store');
        Route::get('/{orden}', [OrdenMedicaController::class, 'show'])->name('show');
        Route::put('/{orden}', [OrdenMedicaController::class, 'update'])->name('update');
        Route::patch('/{orden}/completar', [OrdenMedicaController::class, 'complete'])->name('complete');
        Route::patch('/{orden}/cancelar', [OrdenMedicaController::class, 'cancel'])->name('cancel');
        Route::get('/crear/{atencion}', [OrdenMedicaController::class, 'create'])->name('create');
    });

    /*
    |--------------------------------------------------------------------------
    | Laboratorio
    |--------------------------------------------------------------------------
    */
   Route::prefix('laboratorio')->name('laboratorio.')->middleware('auth')->group(function () {
 
    // Cola principal del laboratista
    Route::get('/', [LaboratorioController::class, 'index'])->name('index');
 
    // Ver detalle de ticket (toma de muestra)
    Route::get('/ticket/{ticket}', [LaboratorioController::class, 'show'])->name('show');
 
    // Ver/gestionar resultados de un ticket
    Route::get('/ticket/{ticket}/resultados', [LaboratorioController::class, 'showResultados'])->name('show-resultados');
 
    // Llamar paciente
    Route::post('/ticket/{ticket}/llamar', [LaboratorioController::class, 'llamar'])->name('llamar');
 
    // Registrar toma de muestra
    Route::post('/ticket/{ticket}/muestra', [LaboratorioController::class, 'registrarMuestra'])->name('registrar-muestra');
 
    // Subir resultado PDF/imagen
    Route::post('/ticket/{ticket}/resultado', [LaboratorioController::class, 'subirResultado'])->name('subir-resultado');
 
    // Eliminar adjunto
    Route::delete('/adjunto/{adjunto}', [LaboratorioController::class, 'eliminarAdjunto'])->name('eliminar-adjunto');
 
    // Cerrar ticket (marcar como completado)
    Route::post('/ticket/{ticket}/cerrar', [LaboratorioController::class, 'cerrar'])->name('cerrar');
});
    /*
    |--------------------------------------------------------------------------
    | Interconsultas
    |--------------------------------------------------------------------------
    */
    Route::prefix('interconsultas')->name('interconsultas.')->group(function () {
        Route::get('/', [InterconsultaController::class, 'index'])->name('index');
        Route::get('/crear/{atencion}', [InterconsultaController::class, 'create'])->name('create');
        Route::post('/{atencion}', [InterconsultaController::class, 'store'])->name('store');
        Route::get('/{interconsulta}', [InterconsultaController::class, 'show'])->name('show');
        Route::patch('/{interconsulta}/aceptar', [InterconsultaController::class, 'accept'])->name('accept');
        Route::patch('/{interconsulta}/completar', [InterconsultaController::class, 'complete'])->name('complete');
        Route::patch('/{interconsulta}/cancelar', [InterconsultaController::class, 'cancel'])->name('cancel');
        
    });
   // --- SECCIÓN DE ALERGIAS ---

// Ruta de escape: Si se intenta entrar por GET, redirigimos back (o a una ruta segura)
Route::get('pacientes/{paciente}/alergias', function() {
    return redirect()->back(); 
});

// Tu ruta de guardado actual
Route::post('pacientes/{paciente}/alergias', [PacienteAlergiaController::class, 'store'])
    ->name('pacientes.alergias.store');

Route::delete('alergias/{alergia}', [PacienteAlergiaController::class, 'destroy'])
    ->name('pacientes.alergias.destroy');
    Route::prefix('medicamentos')->name('medicamentos.')->group(function () {
    Route::get('/',                    [MedicamentoController::class, 'index'])->name('index');
    Route::get('/crear',               [MedicamentoController::class, 'create'])->name('create');
    Route::post('/',                   [MedicamentoController::class, 'store'])->name('store');
    Route::get('/{medicamento}',       [MedicamentoController::class, 'show'])->name('show');
    Route::get('/{medicamento}/edit',  [MedicamentoController::class, 'edit'])->name('edit');
    Route::put('/{medicamento}',       [MedicamentoController::class, 'update'])->name('update');
    Route::patch('/{medicamento}/desactivar', [MedicamentoController::class, 'desactivar'])->name('desactivar');
    Route::patch('/{medicamento}/activar',    [MedicamentoController::class, 'activar'])->name('activar');
});
});