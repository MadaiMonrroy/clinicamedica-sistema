<?php

namespace App\Http\Controllers;

use App\Models\Atencion;
use App\Models\DetalleReceta;
use App\Models\Medicamento;
use App\Models\RecetaMedica;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RecetaMedicaController extends Controller
{
    public function index()
    {
        $recetas = RecetaMedica::with(['atencion.ticket.paciente'])
            ->latest('fecha_receta')
            ->paginate(15);

        return view('medico.recetas.index', compact('recetas'));
    }

    /**
     * Formulario nueva receta.
     * Genera número automático: REC-{TICKET}-{01}
     */
    public function create(Atencion $atencion)
    {
        $atencion->load(['ticket.paciente', 'ticket.area']);

        $ticket       = $atencion->ticket?->numero_ticket ?? 'SIN';
        $totalRecetas = RecetaMedica::where('atencion_id', $atencion->id)->count();
        $correlativo  = str_pad($totalRecetas + 1, 2, '0', STR_PAD_LEFT);
        $numeroReceta = "REC-{$ticket}-{$correlativo}";

        $medicamentos = Medicamento::where('estado', 'activo')
            ->orderBy('nombre')
            ->get();

        return view('medico.recetas.create', compact('atencion', 'numeroReceta', 'medicamentos'));
    }

    /**
     * Guardar receta con medicamentos.
     * Estado inicial siempre: emitida
     */
    public function store(Request $request, Atencion $atencion)
    {
        $request->validate([
            'numero_receta'                     => ['required', 'string', 'max:30'],
            'indicacion_general'                => ['nullable', 'string'],
            'medicamentos'                      => ['required', 'array', 'min:1'],
            'medicamentos.*.nombre'             => ['required', 'string', 'max:150'],
            'medicamentos.*.presentacion'       => ['required', 'string', 'max:100'],
            'medicamentos.*.concentracion'      => ['required', 'string', 'max:100'],
            'medicamentos.*.via_administracion' => ['required', 'string', 'max:100'],
            'medicamentos.*.frecuencia'         => ['required', 'string', 'max:100'],
            'medicamentos.*.duracion'           => ['required', 'string', 'max:100'],
            'medicamentos.*.cantidad'           => ['required', 'string', 'max:50'],
            'medicamentos.*.observacion'        => ['nullable', 'string'],
            'medicamentos.*.medicamento_id'     => ['nullable', 'exists:medicamentos,id'],
        ]);

        DB::transaction(function () use ($request, $atencion) {

            $receta = RecetaMedica::create([
                'atencion_id'        => $atencion->id,
                'numero_receta'      => $request->numero_receta,
                'fecha_receta'       => now(),
                'indicacion_general' => $request->indicacion_general,
                'estado'             => 'emitida',
            ]);

            foreach ($request->medicamentos as $med) {
                $medicamentoId = $med['medicamento_id'] ?: null;

                if ($medicamentoId) {
                    // Existe en catálogo → actualizar datos si estaban incompletos
                    Medicamento::where('id', $medicamentoId)->update([
                        'presentacion'       => $med['presentacion'],
                        'concentracion'      => $med['concentracion'],
                        'via_administracion' => $med['via_administracion'],
                        'actualizado_por'    => Auth::id(),
                    ]);
                } else {
                    // Buscar por nombre o crear nuevo
                    $medicamento = Medicamento::whereRaw(
                        'LOWER(nombre) = LOWER(?)', [trim($med['nombre'])]
                    )->first();

                    if ($medicamento) {
                        $medicamento->update([
                            'presentacion'       => $medicamento->presentacion       ?: $med['presentacion'],
                            'concentracion'      => $medicamento->concentracion      ?: $med['concentracion'],
                            'via_administracion' => $medicamento->via_administracion ?: $med['via_administracion'],
                            'actualizado_por'    => Auth::id(),
                        ]);
                    } else {
                        $medicamento = Medicamento::create([
                            'nombre'             => trim($med['nombre']),
                            'presentacion'       => $med['presentacion'],
                            'concentracion'      => $med['concentracion'],
                            'via_administracion' => $med['via_administracion'],
                            'estado'             => 'activo',
                            'creado_por'         => Auth::id(),
                        ]);
                    }

                    $medicamentoId = $medicamento->id;
                }

                DetalleReceta::create([
                    'receta_id'      => $receta->id,
                    'medicamento_id' => $medicamentoId,
                    'frecuencia'     => $med['frecuencia'],
                    'duracion'       => $med['duracion'],
                    'cantidad'       => $med['cantidad'],
                    'observacion'    => $med['observacion'] ?? null,
                ]);
            }
        });

        $receta = RecetaMedica::where('numero_receta', $request->numero_receta)->first();

        return redirect()
            ->route('recetas.show', $receta)
            ->with('success', 'Receta emitida correctamente.');
    }

    /**
     * Ver detalle de receta.
     */
    public function show(RecetaMedica $receta)
    {
        $receta->load([
            'atencion.ticket.paciente.alergias.medicamento',
            'detalles.medicamento',
        ]);

        $medicamentosConAlergia = $receta->atencion
            ->ticket
            ->paciente
            ->alergias
            ->whereNotNull('medicamento_id')
            ->pluck('medicamento_id')
            ->toArray();

        return view('medico.recetas.show', compact('receta', 'medicamentosConAlergia'));
    }

    /**
     * Formulario editar receta.
     * Solo si está emitida — si está anulada no se puede editar.
     */
    public function edit(RecetaMedica $receta)
    {
        if ($receta->estado === 'anulada') {
            return redirect()
                ->route('recetas.show', $receta)
                ->with('error', 'No se puede editar una receta anulada.');
        }

        $receta->load(['atencion.ticket.paciente', 'detalles.medicamento']);

        $medicamentos = Medicamento::where('estado', 'activo')
            ->orderBy('nombre')
            ->get();

        return view('medico.recetas.edit', compact('receta', 'medicamentos'));
    }

    /**
     * Actualizar indicaciones generales de la receta.
     * Solo si está emitida.
     */
    public function update(Request $request, RecetaMedica $receta)
    {
        if ($receta->estado === 'anulada') {
            return back()->with('error', 'No se puede editar una receta anulada.');
        }

        $receta->update($request->validate([
            'indicacion_general' => ['nullable', 'string'],
        ]));

        return back()->with('success', 'Receta actualizada.');
    }

    /**
     * Anular receta.
     * Solo si está emitida — el médico se equivocó o cambia el tratamiento.
     */
    public function annul(RecetaMedica $receta)
    {
        if ($receta->estado === 'anulada') {
            return back()->with('error', 'Esta receta ya está anulada.');
        }

        $receta->update(['estado' => 'anulada']);

        return back()->with('success', 'Receta anulada correctamente.');
    }
}