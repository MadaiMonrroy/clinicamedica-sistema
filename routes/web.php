<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Services\ArchivoOptimizerService;
use Illuminate\Http\Request;
// Redirige / directo al login
Route::get('/', function () {
    return redirect()->route('login');
});
// GET - muestra el formulario
Route::get('/test-optimizer', function () {
    return view('test-optimizer');
});

// POST - procesa el archivo
Route::post('/test-optimizer', function (Request $request) {
    $request->validate([
        'archivo' => 'required|file|mimes:pdf,jpg,jpeg,png|max:20480'
    ]);

    $optimizer = new ArchivoOptimizerService();
    $resultado = $optimizer->procesar($request->file('archivo'), 'test');

    $tamanioOriginal = $request->file('archivo')->getSize();

    return response()->json([
        'original_kb'  => round($tamanioOriginal / 1024, 2) . ' KB',
        'resultado_kb' => round($resultado['tamanio'] / 1024, 2) . ' KB',
        'ahorro_pct'   => round((1 - $resultado['tamanio'] / $tamanioOriginal) * 100, 1) . '%',
        'ruta'         => $resultado['ruta'],
        'nombre'       => $resultado['nombre'],
    ]);
})->middleware('web');
// Route::get('/welcome', function () {
//     return view('welcome');
// });

Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'edit'])->name('edit');
        Route::patch('/', [ProfileController::class, 'update'])->name('update');
        Route::delete('/', [ProfileController::class, 'destroy'])->name('destroy');
    });
    
});

require __DIR__.'/auth.php';
require __DIR__.'/admin.php';
require __DIR__.'/recepcion.php';
require __DIR__.'/medico.php';