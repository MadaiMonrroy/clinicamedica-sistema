<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Password;
class StaffController extends Controller
{
    // Mostrar la lista de todo el personal
    public function index(Request $request)
{
    if (Auth::user()->rol !== 'admin') {
        abort(403, 'Acceso denegado. Solo el Administrador puede ver esta sección.');
    }

    $search = trim((string) $request->get('search'));
    $rol = trim((string) $request->get('rol'));

    $query = User::query();

    if ($search !== '') {
        $query->where(function ($q) use ($search) {
            $q->where('name', 'ilike', "%{$search}%")
              ->orWhere('apellido_paterno', 'ilike', "%{$search}%")
              ->orWhere('apellido_materno', 'ilike', "%{$search}%")
              ->orWhere('ci', 'ilike', "%{$search}%")
              ->orWhere('telefono', 'ilike', "%{$search}%")
              ->orWhere('email', 'ilike', "%{$search}%")
              ->orWhere('especialidad', 'ilike', "%{$search}%")
              ->orWhere('cargo', 'ilike', "%{$search}%");
        });
    }

    if ($rol !== '') {
        $query->where('rol', $rol);
    }

    $staff = $query
        ->orderBy('name')
        ->orderBy('apellido_paterno')
        ->paginate(5)
        ->withQueryString();

    $especialidades = User::query()
        ->whereNotNull('especialidad')
        ->where('especialidad', '!=', '')
        ->select('especialidad')
        ->distinct()
        ->orderBy('especialidad')
        ->pluck('especialidad');

    $cargos = User::query()
        ->whereNotNull('cargo')
        ->where('cargo', '!=', '')
        ->select('cargo')
        ->distinct()
        ->orderBy('cargo')
        ->pluck('cargo');

    if ($request->ajax()) {
        return response()->json([
            'table' => view('admin.staff.partials.table', compact('staff'))->render(),
            'pagination' => view('admin.staff.partials.pagination', compact('staff'))->render(),
        ]);
    }

    return view('admin.staff', compact('staff', 'especialidades', 'cargos', 'search', 'rol'));
}

    public function store(Request $request)
{
    if (Auth::user()->rol !== 'admin') {
        abort(403, 'Acceso denegado.');
    }
    session(['reopen_staff_modal' => true]); // ← AGREGAR ESTA LÍNEA

    $validated = $request->validate([
        'name'             => 'required|string|max:255',
        'apellido_paterno' => 'required|string|max:255',
        'apellido_materno' => 'nullable|string|max:255',
        'ci'               => 'required|string|max:255|unique:users,ci',
        'telefono'         => 'nullable|string|max:255',
        'rol'              => 'required|in:admin,recepcionista,enfermera,medico',
        'especialidad'     => 'nullable|string|max:255',
        'cargo'            => 'nullable|string|max:255',
        'activo'           => 'nullable|boolean',
        'email'            => 'required|string|email|max:255|unique:users,email',
    ]);

    $user = User::create([
        'name'             => $validated['name'],
        'apellido_paterno' => $validated['apellido_paterno'],
        'apellido_materno' => $validated['apellido_materno'] ?? null,
        'ci'               => $validated['ci'],
        'telefono'         => $validated['telefono'] ?? null,
        'rol'              => $validated['rol'],
        'especialidad' => in_array($validated['rol'], ['enfermera', 'recepcionista']) // ← quitar 'admin'
                    ? null
                    : ($request->filled('especialidad')
                        ? mb_convert_case(trim($request->especialidad), MB_CASE_TITLE, "UTF-8")
                        : null),
'cargo'        => in_array($validated['rol'], ['enfermera', 'recepcionista']) // ← quitar 'admin'
                    ? null
                    : ($request->filled('cargo')
                        ? mb_convert_case(trim($request->cargo), MB_CASE_TITLE, "UTF-8")
                        : null),
        'activo'           => $request->boolean('activo', true),
        'email'            => $validated['email'],
        'password'         => Hash::make(Str::random(32)),
    ]);

    Password::sendResetLink(['email' => $user->email]);
session()->forget('reopen_staff_modal'); // ← AGREGAR ESTA LÍNEA

    return back()->with('status', '¡Personal registrado! Se envió un correo de activación a ' . $user->email);
}

public function resendAccess(User $user)
{
    if (Auth::user()->rol !== 'admin') {
        abort(403, 'Acceso denegado.');
    }

    Password::sendResetLink(['email' => $user->email]);

    return back()->with('status', "Se reenvió el correo de acceso a {$user->email}");
}

    // Actualizar datos completos de un empleado
    public function update(Request $request, User $user)
    {
        if (Auth::user()->rol !== 'admin') {
            abort(403, 'Acceso denegado.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'apellido_paterno' => 'required|string|max:255',
            'apellido_materno' => 'nullable|string|max:255',
            'ci' => [
                'required',
                'string',
                'max:255',
                Rule::unique('users', 'ci')->ignore($user->id),
            ],
            'telefono' => 'nullable|string|max:255',
            'rol' => 'required|in:admin,recepcionista,enfermera,medico',
            'especialidad' => 'nullable|string|max:255',
            'cargo' => 'nullable|string|max:255',
            'activo' => 'nullable|boolean',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $data = [
            'name' => $validated['name'],
            'apellido_paterno' => $validated['apellido_paterno'],
            'apellido_materno' => $validated['apellido_materno'] ?? null,
            'ci' => $validated['ci'],
            'telefono' => $validated['telefono'] ?? null,
            'rol' => $validated['rol'],
            'especialidad' => in_array($validated['rol'], ['enfermera', 'recepcionista'])
                    ? null
                    : ($request->filled('especialidad')
                        ? mb_convert_case(trim($request->especialidad), MB_CASE_TITLE, "UTF-8")
                        : null),
'cargo'        => in_array($validated['rol'], ['enfermera', 'recepcionista'])
                    ? null
                    : ($request->filled('cargo')
                        ? mb_convert_case(trim($request->cargo), MB_CASE_TITLE, "UTF-8")
                        : null),
            'activo' => $request->boolean('activo'),
            'email' => $validated['email'],
        ];

        if (!empty($validated['password'])) {
            $data['password'] = Hash::make($validated['password']);
        }

        $user->update($data);

        return back()->with('status', '¡Personal actualizado correctamente!');
    }

    // Activar / Inactivar usuario
    public function toggleActive(User $user)
    {
        if (Auth::user()->rol !== 'admin') {
            abort(403, 'Acceso denegado.');
        }

        $user->update([
            'activo' => !$user->activo,
        ]);

        return back()->with(
            'status',
            $user->activo
                ? '¡Usuario activado correctamente!'
                : '¡Usuario inactivado correctamente!'
        );
    }
}