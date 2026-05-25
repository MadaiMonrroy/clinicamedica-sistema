<?php

namespace App\Http\Controllers;

use App\Models\Medicamento;
use App\Models\Paciente;
use App\Models\PacienteAlergia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PacienteAlergiaController extends Controller
{
    /**
     * Guardar nueva alergia.
     *
     * Si tipo = medicamento:
     *   - Si viene medicamento_id → vincula al catálogo, descripcion = nombre del med
     *   - Si no viene medicamento_id → guarda descripcion libre, medicamento_id = null
     *     (el admin puede vincularlo después desde el catálogo)
     *
     * Si tipo != medicamento:
     *   - Guarda descripcion libre, medicamento_id = null
     */
    public function store(Request $request, Paciente $paciente)
    {
        $request->validate([
            'tipo'           => ['required', 'in:medicamento,alimento,ambiental,otro'],
            'descripcion'    => ['required', 'string', 'max:255'],
            'medicamento_id' => ['nullable', 'exists:medicamentos,id'],
            'severidad'      => ['nullable', 'in:leve,moderada,grave'],
            'reaccion'       => ['nullable', 'string'],
        ]);

        $medicamentoId = null;

        // Si es medicamento y viene del catálogo, vincular
        if ($request->tipo === 'medicamento' && $request->filled('medicamento_id')) {
            $medicamentoId = $request->medicamento_id;
        }

        PacienteAlergia::create([
            'paciente_id'    => $paciente->id,
            'tipo'           => $request->tipo,
            'descripcion'    => trim($request->descripcion),
            'medicamento_id' => $medicamentoId, // null si no está en catálogo
            'severidad'      => $request->severidad ?: null,
            'reaccion'       => $request->reaccion  ?: null,
            'registrado_por' => Auth::id(),
        ]);

        return back()->with('success', 'Alergia registrada correctamente.');
    }

    /**
     * Eliminar alergia.
     */
    public function destroy(PacienteAlergia $alergia)
    {
        $alergia->delete();

        return back()->with('success', 'Alergia eliminada.');
    }
}