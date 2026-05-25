<?php

namespace App\Http\Controllers;

use App\Models\Medicamento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MedicamentoController extends Controller
{
    /**
     * Catálogo de medicamentos.
     * Acceso: admin y medico
     */
    public function index(Request $request)
    {
        $query = Medicamento::with(['creadoPor', 'actualizadoPor'])
            ->orderBy('nombre');

        // Filtro por estado
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        // Filtro por completitud
        if ($request->filtro === 'incompletos') {
            $query->where(function ($q) {
                $q->whereNull('presentacion')
                  ->orWhereNull('concentracion')
                  ->orWhereNull('via_administracion');
            });
        }

        // Búsqueda por nombre
        if ($request->filled('buscar')) {
            $query->where('nombre', 'ilike', '%' . $request->buscar . '%');
        }

        $medicamentos = $query->paginate(20)->withQueryString();

        return view('admin.medicamentos.index', compact('medicamentos'));
    }

    /**
     * Formulario de nuevo medicamento.
     * Acceso: admin y medico
     */
    public function create()
    {
        return view('admin.medicamentos.create');
    }

    /**
     * Guardar nuevo medicamento.
     * Acceso: admin y medico
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre'            => ['required', 'string', 'max:150'],
            'presentacion'      => ['nullable', 'string', 'max:100'],
            'concentracion'     => ['nullable', 'string', 'max:100'],
            'via_administracion'=> ['nullable', 'string', 'max:100'],
            'cod_medicamento'   => ['nullable', 'string', 'max:30', 'unique:medicamentos,cod_medicamento'],
        ]);

        $data['estado']      = 'activo';
        $data['creado_por']  = Auth::id();

        $medicamento = Medicamento::create($data);

        // Si viene de una receta o alergia, redirigir de vuelta con el ID
        if ($request->filled('redirect_to')) {
            return redirect($request->redirect_to)
                ->with('success', "Medicamento \"{$medicamento->nombre}\" agregado al catálogo.")
                ->with('nuevo_medicamento_id', $medicamento->id);
        }

        return redirect()
            ->route('medicamentos.index')
            ->with('success', "Medicamento \"{$medicamento->nombre}\" creado correctamente.");
    }

    /**
     * Ver detalle de medicamento.
     */
    public function show(Medicamento $medicamento)
    {
        $medicamento->load(['creadoPor', 'actualizadoPor']);

        return view('admin.medicamentos.show', compact('medicamento'));
    }

    /**
     * Formulario de edición.
     * Acceso: solo admin
     */
    public function edit(Medicamento $medicamento)
    {
        $this->soloAdmin();

        return view('admin.medicamentos.edit', compact('medicamento'));
    }

    /**
     * Actualizar medicamento.
     * Acceso: solo admin
     */
    public function update(Request $request, Medicamento $medicamento)
    {
        $this->soloAdmin();

        $data = $request->validate([
            'nombre'            => ['required', 'string', 'max:150'],
            'presentacion'      => ['nullable', 'string', 'max:100'],
            'concentracion'     => ['nullable', 'string', 'max:100'],
            'via_administracion'=> ['nullable', 'string', 'max:100'],
            'cod_medicamento'   => ['nullable', 'string', 'max:30', 'unique:medicamentos,cod_medicamento,' . $medicamento->id],
            'estado'            => ['required', 'in:activo,inactivo'],
        ]);

        $data['actualizado_por'] = Auth::id();

        $medicamento->update($data);

        return redirect()
            ->route('medicamentos.index')
            ->with('success', "Medicamento \"{$medicamento->nombre}\" actualizado.");
    }

    /**
     * Desactivar medicamento (soft delete lógico).
     * Acceso: solo admin
     */
    public function desactivar(Medicamento $medicamento)
    {
        $this->soloAdmin();

        $medicamento->update([
            'estado'         => 'inactivo',
            'actualizado_por'=> Auth::id(),
        ]);

        return back()->with('success', "Medicamento desactivado.");
    }

    /**
     * Reactivar medicamento.
     * Acceso: solo admin
     */
    public function activar(Medicamento $medicamento)
    {
        $this->soloAdmin();

        $medicamento->update([
            'estado'         => 'activo',
            'actualizado_por'=> Auth::id(),
        ]);

        return back()->with('success', "Medicamento reactivado.");
    }

    // ── Helper privado ────────────────────────────────────────────────────────

    private function soloAdmin(): void
    {
        if (Auth::user()->rol !== 'admin') {
            abort(403, 'Solo los administradores pueden realizar esta acción.');
        }
    }
}