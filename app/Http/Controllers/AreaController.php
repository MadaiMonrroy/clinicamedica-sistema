<?php

namespace App\Http\Controllers;

use App\Models\Area;
use Illuminate\Http\Request;

class AreaController extends Controller
{
    public function index()
    {
        $areas = Area::orderBy('tipo')->orderBy('nombre')->get();

        $stats = [
            'total'     => $areas->count(),
            'activas'   => $areas->where('estado', 'activo')->count(),
            'inactivas' => $areas->where('estado', 'inactivo')->count(),
        ];

        return view('admin.areas.index', compact('areas', 'stats'));
    }

    public function create()
    {
        return view('admin.areas.create', [
            'tiposSugeridos' => Area::TIPOS_SUGERIDOS,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:100',
            'codigo' => 'required|string|max:20|unique:areas,codigo|regex:/^[A-Z0-9_-]+$/i',
            'tipo'   => 'required|string|max:50',
        ], [
            'codigo.unique' => 'Este código ya existe. Usa uno diferente.',
            'codigo.regex'  => 'El código solo puede tener letras, números, guión y guión bajo.',
        ]);

        Area::create([
            'nombre' => $request->nombre,
            'codigo' => strtoupper($request->codigo),
            'tipo'   => strtolower(trim($request->tipo)),
            'estado' => 'activo',
        ]);

        return redirect()
            ->route('admin.areas.index')
            ->with('success', "Área \"{$request->nombre}\" creada correctamente.");
    }

    public function edit(Area $area)
    {
        return view('admin.areas.edit', [
            'area'           => $area,
            'tiposSugeridos' => Area::TIPOS_SUGERIDOS,
            'estados'        => Area::ESTADOS,
        ]);
    }

    public function update(Request $request, Area $area)
    {
        $request->validate([
            'nombre' => 'required|string|max:100',
            'codigo' => 'required|string|max:20|regex:/^[A-Z0-9_-]+$/i|unique:areas,codigo,' . $area->id,
            'tipo'   => 'required|string|max:50',
            'estado' => 'required|in:activo,inactivo',
        ], [
            'codigo.unique' => 'Este código ya existe. Usa uno diferente.',
            'codigo.regex'  => 'El código solo puede tener letras, números, guión y guión bajo.',
        ]);

        $area->update([
            'nombre' => $request->nombre,
            'codigo' => strtoupper($request->codigo),
            'tipo'   => strtolower(trim($request->tipo)),
            'estado' => $request->estado,
        ]);

        return redirect()
            ->route('admin.areas.index')
            ->with('success', "Área \"{$area->nombre}\" actualizada.");
    }

   public function destroy(Area $area)
{
    // En lugar de eliminar, desactiva
    $area->update(['estado' => 'inactivo']);

    return redirect()
        ->route('admin.areas.index')
        ->with('success', "Área \"{$area->nombre}\" desactivada. Puedes reactivarla cuando quieras.");
}

    /**
     * Toggle rápido activo ↔ inactivo desde el index.
     * Requiere: Route::patch('/{area}/toggle', ...)->name('toggle')
     */
    public function toggle(Area $area)
    {
        $nuevoEstado = $area->estado === 'activo' ? 'inactivo' : 'activo';
        $area->update(['estado' => $nuevoEstado]);

        $msg = $nuevoEstado === 'activo'
            ? "Área \"{$area->nombre}\" activada."
            : "Área \"{$area->nombre}\" desactivada.";

        return back()->with('success', $msg);
    }
}