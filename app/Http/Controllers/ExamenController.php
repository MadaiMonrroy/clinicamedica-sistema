<?php

namespace App\Http\Controllers;

use App\Models\Examen;
use App\Models\CategoriaExamen;
use Illuminate\Http\Request;

class ExamenController extends Controller
{
    public function index(Request $request)
    {
        $query = Examen::with('categoriaExamen')
            ->when($request->filled('buscar'), function ($q) use ($request) {
                $b = $request->buscar;
                $q->where(function ($sub) use ($b) {
                    $sub->where('nombre_examen', 'ilike', "%{$b}%")
                        ->orWhere('cod_examen',    'ilike', "%{$b}%");
                });
            })
            ->when($request->filled('categoria_id'), function ($q) use ($request) {
                $q->where('categoria_id', $request->categoria_id);
            })
            ->when($request->filled('estado'), function ($q) use ($request) {
                $q->where('estado', $request->estado);
            })
            ->orderBy('nombre_examen');

        $examenes     = $query->paginate(10)->withQueryString();
        $categorias   = CategoriaExamen::activas()->orderBy('nombre')->get();
        $totalActivos = Examen::where('estado', 'activo')->count();

        if ($request->ajax()) {
            return response()->json([
                'tabla' => view('admin.examenes.tabla', compact('examenes'))->render(),
            ]);
        }

        return view('admin.examenes.index', compact('examenes', 'categorias', 'totalActivos'));
    }

    public function create()
    {
        $categorias = CategoriaExamen::activas()
            ->with('examenesActivos')
            ->orderBy('nombre')
            ->get();

        return view('admin.examenes.create', compact('categorias'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'categoria_id'    => ['nullable', 'exists:categorias_examenes,id'],
            'categoria_nueva' => ['nullable', 'string', 'max:100'],
            'nombre_examen'   => ['required', 'string', 'max:150'],
            'cod_examen'      => ['nullable', 'string', 'max:30', 'unique:examenes,cod_examen'],
            'descripcion'     => ['nullable', 'string'],
            'costo_ref'       => ['nullable', 'numeric', 'min:0'],
            'estado'          => ['required', 'in:activo,inactivo'],
        ]);

        $categoriaId = $data['categoria_id'] ?? null;

        if (!$categoriaId && !empty($data['categoria_nueva'])) {
            $nombreCat    = trim($data['categoria_nueva']);
            $prefijo      = strtoupper(substr(preg_replace('/[^a-zA-Z]/', '', $nombreCat), 0, 3));
            $prefijoFinal = $prefijo;
            $i = 2;
            while (CategoriaExamen::where('codigo_prefijo', $prefijoFinal)->exists()) {
                $prefijoFinal = $prefijo . $i++;
            }
            $cat         = CategoriaExamen::firstOrCreate(
                ['nombre' => $nombreCat],
                ['codigo_prefijo' => $prefijoFinal, 'estado' => 'activo']
            );
            $categoriaId = $cat->id;
        }

        $data['categoria_id'] = $categoriaId;

        if ($categoriaId) {
            $cat = CategoriaExamen::find($categoriaId);
            $data['tipo_examen'] = $cat->nombre;
        }

        if (empty($data['cod_examen']) && $categoriaId) {
            $data['cod_examen'] = $this->generarCodigo($categoriaId);
        }

        unset($data['categoria_nueva']);
        Examen::create($data);

        return redirect()->route('admin.examenes.index')
            ->with('success', 'Examen registrado correctamente.');
    }

    public function edit(Examen $examen)
    {
        $categorias = CategoriaExamen::activas()
            ->with('examenesActivos')
            ->orderBy('nombre')
            ->get();

        return view('admin.examenes.edit', compact('examen', 'categorias'));
    }

    public function update(Request $request, Examen $examen)
    {
        $data = $request->validate([
            'categoria_id'    => ['nullable', 'exists:categorias_examenes,id'],
            'categoria_nueva' => ['nullable', 'string', 'max:100'],
            'nombre_examen'   => ['required', 'string', 'max:150'],
            'cod_examen'      => ['nullable', 'string', 'max:30', 'unique:examenes,cod_examen,' . $examen->id],
            'descripcion'     => ['nullable', 'string'],
            'costo_ref'       => ['nullable', 'numeric', 'min:0'],
            'estado'          => ['required', 'in:activo,inactivo'],
        ]);

        $categoriaId = $data['categoria_id'] ?? null;

        if (!$categoriaId && !empty($data['categoria_nueva'])) {
            $nombreCat    = trim($data['categoria_nueva']);
            $prefijo      = strtoupper(substr(preg_replace('/[^a-zA-Z]/', '', $nombreCat), 0, 3));
            $prefijoFinal = $prefijo;
            $i = 2;
            while (CategoriaExamen::where('codigo_prefijo', $prefijoFinal)->exists()) {
                $prefijoFinal = $prefijo . $i++;
            }
            $cat         = CategoriaExamen::firstOrCreate(
                ['nombre' => $nombreCat],
                ['codigo_prefijo' => $prefijoFinal, 'estado' => 'activo']
            );
            $categoriaId = $cat->id;
        }

        $data['categoria_id'] = $categoriaId;

        if ($categoriaId) {
            $cat = CategoriaExamen::find($categoriaId);
            $data['tipo_examen'] = $cat->nombre;

            // Si cambió la categoría, regenerar el código automáticamente
            if ($categoriaId != $examen->categoria_id) {
                $data['cod_examen'] = $this->generarCodigo($categoriaId);
            }
        }

        unset($data['categoria_nueva']);
        $examen->update($data);

        return redirect()->route('admin.examenes.index')
            ->with('success', 'Examen actualizado correctamente.');
    }

    public function toggle(Examen $examen)
    {
        $examen->update([
            'estado' => $examen->estado === 'activo' ? 'inactivo' : 'activo',
        ]);

        return back()->with('success', 'Estado actualizado.');
    }

    public function destroy(Examen $examen)
    {
        if ($examen->solicitudes()->exists() || $examen->adjuntosLaboratorio()->exists()) {
            return back()->with('error', 'No se puede eliminar: el examen tiene registros asociados.');
        }
        $examen->delete();
        return redirect()->route('admin.examenes.index')->with('success', 'Examen eliminado.');
    }

    private function generarCodigo(int $categoriaId): string
    {
        $categoria = CategoriaExamen::find($categoriaId);
        $prefijo   = $categoria->codigo_prefijo;

        $ultimo = Examen::where('cod_examen', 'like', $prefijo . '-%')
            ->selectRaw("MAX(CAST(SPLIT_PART(cod_examen, '-', 2) AS INTEGER)) as max_num")
            ->value('max_num');

        return $prefijo . '-' . str_pad(($ultimo ?? 0) + 1, 3, '0', STR_PAD_LEFT);
    }
}