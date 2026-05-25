<?php

namespace App\Http\Controllers;

use App\Models\Paciente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Medicamento;
class PacienteController extends Controller
{
    /**
     * Lista general de pacientes.
     */
    public function index(Request $request)
{
    // Carga TODOS los pacientes para la búsqueda cliente
    // withCount para no hacer N queries de ingresos en la tabla
    $pacientes = Paciente::withCount('ingresos')
        ->orderBy('apellido_paterno')
        ->orderBy('apellido_materno')
        ->orderBy('nombres')
        ->get();
 
    // Stats para las tarjetas del header
    $totalPacientes = $pacientes->count();
    $totalActivos   = $pacientes->where('estado', 'activo')->count();
    $totalInactivos = $pacientes->where('estado', 'inactivo')->count();
    $totalIngresos  = $pacientes->sum('ingresos_count');
 
    return view('recepcion.pacientes.index', compact(
        'pacientes',
        'totalPacientes',
        'totalActivos',
        'totalInactivos',
        'totalIngresos',
    ));
}

    /**
     * Formulario de creación de paciente.
     */
    public function create()
    {
        return view('recepcion.pacientes.create');
    }
/**
 * Busca un paciente por CI exacto — usado por AJAX en el formulario de creación.
 */
public function buscarPorCi(Request $request)
{
    $ci = trim((string) $request->get('ci'));

    if (empty($ci) || strlen($ci) < 4) {
        return response()->json(['encontrado' => false]);
    }

    $paciente = Paciente::where('ci', $ci)->first();

    if (!$paciente) {
        return response()->json(['encontrado' => false]);
    }

    return response()->json([
        'encontrado' => true,
        'paciente' => [
            'id'              => $paciente->id,
            'nombre_completo' => $paciente->nombre_completo,
            'ci'              => $paciente->ci,
            'telefono'        => $paciente->telefono ?? 'Sin teléfono',
            'email'           => $paciente->email ?? 'Sin correo',
            'sexo'            => $paciente->sexo,
            'edad'            => $paciente->edad,
            'estado'          => $paciente->estado,
            'url_historial'   => route('recepcion.pacientes.show', $paciente->id),
            'url_ingreso'     => route('recepcion.ingresos.create', ['paciente_id' => $paciente->id]),
        ],
    ]);
}
    /**
     * Guarda un nuevo paciente.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'ci'               => ['required', 'string', 'max:30', 'unique:pacientes,ci'],
    'nombres'          => ['required', 'string', 'max:150'],
    'apellido_paterno' => ['required', 'string', 'max:100'],
    'apellido_materno' => ['nullable', 'string', 'max:100'],  // antes nullable
    'telefono'         => ['required', 'string', 'max:30'],   // antes nullable
    'direccion'        => ['required', 'string', 'max:255'],  // antes nullable
    'fecha_nacimiento' => ['required', 'date'],               // antes nullable
    'sexo'             => ['required', 'in:M,F,OTRO'],
    'email'            => ['nullable', 'email', 'max:150'],
    'estado'           => ['nullable', 'in:activo,inactivo'],
    'observacion'      => ['nullable', 'string'],
        ]);

        $data['user_id'] = Auth::id();
        $data['estado'] = $data['estado'] ?? 'activo';

        $paciente = Paciente::create($data);

        return redirect()
            ->route('recepcion.pacientes.show', $paciente)
            ->with('success', 'Paciente registrado correctamente.');
    }

    /**
     * Búsqueda rápida para recepción.
     * Ideal para buscar por CI antes de registrar.
     */
    public function search(Request $request)
    {
        $term = trim((string) $request->get('q'));

        $pacientes = Paciente::query()
            ->when($term, function ($query) use ($term) {
                $query->where('ci', 'like', "%{$term}%")
                    ->orWhere('nombres', 'like', "%{$term}%")
                    ->orWhere('apellido_paterno', 'like', "%{$term}%")
                    ->orWhere('apellido_materno', 'like', "%{$term}%");
            })
            ->orderBy('apellido_paterno')
            ->orderBy('apellido_materno')
            ->orderBy('nombres')
            ->limit(20)
            ->get();

        return view('recepcion.pacientes.search', compact('pacientes', 'term'));
    }

    /**
     * Muestra detalle del paciente.
     */
    public function show(Paciente $paciente)
{
    // 1. Cargamos los medicamentos activos para el selector de alergias
    $medicamentos = Medicamento::where('estado', 'activo')
        ->orderBy('nombre')
        ->get();

    // 2. Cargamos las relaciones necesarias: 
    // Últimos 10 ingresos Y todas las alergias con sus relaciones
    $paciente->load([
        'ingresos' => fn ($query) => $query->latest()->limit(10),
        'alergias.medicamento',
        'alergias.registradoPor'
    ]);

    // 3. Pasamos las variables a la vista
    return view('recepcion.pacientes.show', compact('paciente', 'medicamentos'));
}

    /**
     * Formulario para editar paciente.
     */
    public function edit(Paciente $paciente)
    {
        return view('recepcion.pacientes.edit', compact('paciente'));
    }

    /**
     * Actualiza paciente.
     */
    public function update(Request $request, Paciente $paciente)
{
    $data = $request->validate([
        'ci'               => ['required', 'string', 'max:30', 'unique:pacientes,ci,' . $paciente->id],
        'nombres'          => ['required', 'string', 'max:150'],
        'apellido_paterno' => ['required', 'string', 'max:100'],
        'apellido_materno' => ['nullable', 'string', 'max:100'],
        'telefono'         => ['required', 'string', 'max:30'],
        'direccion'        => ['required', 'string', 'max:255'],
        'fecha_nacimiento' => ['required', 'date'],
        'sexo'             => ['required', 'in:M,F,OTRO'],
        'email'            => ['nullable', 'email', 'max:150'],
        'estado'           => ['required', 'in:activo,inactivo'],
        'observacion'      => ['nullable', 'string'],
    ]);

    $paciente->update($data);

    return redirect()
        ->route('recepcion.pacientes.show', $paciente)
        ->with('success', 'Paciente actualizado correctamente.');
}

}