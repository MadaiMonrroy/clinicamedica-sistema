<?php

namespace App\Http\Controllers;

use App\Models\Atencion;
use App\Models\Diagnostico;
use Illuminate\Http\Request;

class DiagnosticoController extends Controller
{
    /**
     * Registrar diagnóstico en una atención.
     */
    public function store(Request $request, Atencion $atencion)
    {
        $data = $request->validate([
            'codigo_diagnostico' => ['nullable', 'string', 'max:20'],
            'nombre_diagnostico' => ['required', 'string', 'max:255'],
            'tipo_diagnostico' => ['nullable', 'string', 'max:50'],
            'gravedad' => ['nullable', 'in:leve,moderado,grave,critico'],
            'descripcion' => ['nullable', 'string'],
        ]);

        $data['atencion_id'] = $atencion->id;

        Diagnostico::create($data);

        return back()->with('success', 'Diagnóstico agregado correctamente.');
    }

    /**
     * Actualizar diagnóstico.
     */
    public function update(Request $request, Diagnostico $diagnostico)
    {
        $data = $request->validate([
            'codigo_diagnostico' => ['nullable', 'string', 'max:20'],
            'nombre_diagnostico' => ['required', 'string', 'max:255'],
            'tipo_diagnostico' => ['nullable', 'string', 'max:50'],
            'gravedad' => ['nullable', 'in:leve,moderado,grave,critico'],
            'descripcion' => ['nullable', 'string'],
        ]);

        $diagnostico->update($data);

        return back()->with('success', 'Diagnóstico actualizado.');
    }

    /**
     * Eliminar diagnóstico.
     */
    public function destroy(Diagnostico $diagnostico)
    {
        $diagnostico->delete();

        return back()->with('success', 'Diagnóstico eliminado.');
    }
}