<x-app-layout>
    @php
        $pageWrap     = 'space-y-6';
        $card         = 'rounded-[2rem] border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 shadow-sm';
        $inputClass   = 'block w-full px-4 py-3 bg-white dark:bg-gray-800/40 border border-gray-200 dark:border-gray-700 focus:border-[#44B0B3] focus:ring-1 focus:ring-[#44B0B3] rounded-2xl text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 transition-all text-sm shadow-sm';
        $labelClass   = 'block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2';
        $primaryBtn   = 'inline-flex items-center justify-center rounded-2xl bg-[#44B0B3] hover:bg-[#389a9d] text-white font-bold px-5 py-3 shadow-lg shadow-[#44B0B3]/25 transition';
        $secondaryBtn = 'inline-flex items-center justify-center rounded-2xl border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-800 px-5 py-3 transition';

        // Valores actuales del examen
        $categoriaActualId  = old('categoria_id', $examen->categoria_id ?? '');
        $nombreActual       = old('nombre_examen', $examen->nombre_examen ?? '');
        $costoActual        = old('costo_ref', $examen->costo_ref ?? '');
        $estadoActual       = old('estado', $examen->estado ?? 'activo');
        $descripcionActual  = old('descripcion', $examen->descripcion ?? '');
        $codActual          = $examen->cod_examen ?? '';

        // Categoria actual del examen
        $categoriaActual    = $categorias->firstWhere('id', $categoriaActualId);
        $nombreCatActual    = $categoriaActual?->nombre ?? '';

        // Todos los examenes para validacion antiduplados (excluyendo el actual)
        $todosExamenesJs = $categorias->flatMap(fn($c) =>
            $c->examenesActivos
                ->filter(fn($e) => $e->id !== $examen->id) // excluir el examen actual
                ->map(fn($e) => [
                    'id'          => $e->id,
                    'nombre'      => $e->nombre_examen,
                    'categoria_id'=> $c->id,
                    'categoria'   => $c->nombre,
                ])
        )->values()->toArray();

        $categoriasJs = $categorias->map(fn($c) => [
            'id'      => $c->id,
            'nombre'  => $c->nombre,
            'prefijo' => $c->codigo_prefijo,
            // Excluir el examen actual de la lista de sugerencias para no bloquearse a si mismo
            'examenes'=> $c->examenesActivos
                ->filter(fn($e) => $e->id !== $examen->id)
                ->pluck('nombre_examen')
                ->values()
                ->toArray(),
        ])->values()->toArray();
    @endphp

    <div class="mx-auto {{ $pageWrap }}">

        {{-- CABECERA --}}
        <section class="{{ $card }} p-5 sm:p-6">
            <p class="text-xs font-black uppercase tracking-[0.18em] text-gray-400 dark:text-gray-500">
                Admin / Examenes / Editar
            </p>
            <h1 class="mt-2 text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white">
                Editar examen
            </h1>
            <div class="mt-2 flex items-center gap-3">
                <span class="inline-flex items-center rounded-full bg-[#44B0B3]/10 px-3 py-1 text-xs font-bold text-[#44B0B3] font-mono">
                    {{ $codActual ?: 'Sin codigo' }}
                </span>
                <span class="text-sm font-semibold text-gray-700 dark:text-gray-200">
                    {{ $examen->nombre_examen }}
                </span>
                @if($categoriaActual)
                    <span class="text-xs text-gray-400">— {{ $categoriaActual->nombre }}</span>
                @endif
            </div>
        </section>

        @if($errors->any())
            <div class="rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700 dark:border-red-800 dark:bg-red-900/20 dark:text-red-300">
                <p class="font-semibold mb-1">Corrige los siguientes errores:</p>
                <ul class="list-disc list-inside space-y-0.5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- FORMULARIO --}}
        <form method="POST"
              action="{{ route('admin.examenes.update', $examen) }}"
              class="{{ $card }} overflow-hidden"
              x-data="examenEditComponent()" x-init="init()"
              @submit.prevent="intentarEnviar($el)">
            @csrf
            @method('PUT')

            {{-- Hiddens --}}
            <input type="hidden" name="categoria_id"  :value="categoriaId">
            <input type="hidden" name="nombre_examen" :value="nombreExamenValor">

            <div class="px-5 sm:px-6 py-5 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white">Modificar datos</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    Edita los campos que necesites. El sistema verificara que el nuevo nombre no este duplicado.
                </p>
            </div>

            <div class="px-5 sm:px-6 py-5 space-y-6">

                {{-- CATEGORIA (select fijo — no crear nueva en edicion) --}}
                <div>
                    <label class="{{ $labelClass }}">
                        Categoria <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input
                            type="text"
                            x-model="categoriaInput"
                            @input="filtrarCategorias(); categoriaId = ''"
                            @focus="categoriaOpen = true; filtrarCategorias()"
                            @click.outside="categoriaOpen = false; restaurarCategoria()"
                            autocomplete="off"
                            placeholder="Buscar categoria..."
                            :class="[
                                '{{ $inputClass }} pr-10',
                                categoriaId ? 'border-[#44B0B3]' : ''
                            ]"
                        >
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-4">
                            <svg class="fill-current h-4 w-4 text-gray-400" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                            </svg>
                        </div>

                        <div
                            x-show="categoriaOpen"
                            x-cloak x-transition
                            class="absolute z-50 mt-2 w-full max-h-64 overflow-y-auto rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-xl custom-scroll"
                        >
                            <template x-for="cat in categoriasFiltradas" :key="cat.id">
                                <button type="button" @click="seleccionarCategoria(cat)"
                                    class="flex items-center justify-between w-full px-4 py-2.5 text-left text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition"
                                    :class="cat.id == categoriaId ? 'bg-[#44B0B3]/10 text-[#44B0B3]' : ''"
                                >
                                    <span x-text="cat.nombre"></span>
                                    <span class="text-xs font-mono font-bold text-[#44B0B3] ml-2" x-text="cat.prefijo"></span>
                                </button>
                            </template>
                            <template x-if="categoriasFiltradas.length === 0">
                                <p class="px-4 py-3 text-sm text-gray-400">No se encontraron categorias.</p>
                            </template>
                        </div>
                    </div>

                    {{-- Badge categoria seleccionada --}}
                    <template x-if="categoriaId">
                        <div class="mt-2">
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-[#44B0B3]/10 px-3 py-1 text-xs font-semibold text-[#44B0B3]">
                                <span x-text="categoriaInput"></span>
                                <template x-if="prefijoCat">
                                    <span class="font-mono opacity-70" x-text="'(' + prefijoCat + ')'"></span>
                                </template>
                            </span>
                        </div>
                    </template>

                    @error('categoria_id')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- NOMBRE DEL EXAMEN --}}
                <div>
                    <label class="{{ $labelClass }}">
                        Nombre del examen <span class="text-red-500">*</span>
                    </label>
                    <p class="mb-2 text-xs text-gray-400 dark:text-gray-500">
                        Nombre actual: <strong class="text-gray-600 dark:text-gray-300">{{ $examen->nombre_examen }}</strong>
                    </p>

                    <div class="relative">
                        <input
                            type="text"
                            x-model="nombreInput"
                            @input="onNombreInput()"
                            @keyup="detectarDuplicado()"
                            @focus="nombreOpen = true; filtrarNombres()"
                            @click.outside="nombreOpen = false"
                            autocomplete="off"
                            placeholder="Escribe el nuevo nombre..."
                            :class="[
                                '{{ $inputClass }} pr-10',
                                nombreDuplicadoExacto   ? 'border-red-400 focus:border-red-400 focus:ring-red-400' : '',
                                nombreDuplicadoSimilar && !nombreDuplicadoExacto ? 'border-amber-400 focus:border-amber-400 focus:ring-amber-400' : '',
                                nombreExamenValor && !nombreDuplicadoExacto && !nombreDuplicadoSimilar ? 'border-[#44B0B3]' : ''
                            ]"
                        >
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-4">
                            <svg class="fill-current h-4 w-4 text-gray-400" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                            </svg>
                        </div>

                        {{-- Dropdown sugerencias --}}
                        <div
                            x-show="nombreOpen && nombresFiltrados.length > 0"
                            x-cloak x-transition
                            class="absolute z-50 mt-2 w-full max-h-64 overflow-y-auto rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-xl custom-scroll"
                        >
                            <div class="px-4 py-2 border-b border-gray-100 dark:border-gray-700">
                                <p class="text-xs text-gray-400">
                                    Examenes existentes en esta categoria — seleccionar bloqueara el guardado
                                </p>
                            </div>
                            <template x-for="item in nombresFiltrados" :key="item.nombre">
                                <button type="button" @click="seleccionarExistente(item.nombre)"
                                    class="block w-full px-4 py-2.5 text-left text-sm hover:bg-gray-100 dark:hover:bg-gray-700 transition"
                                    :class="item.esSimilar ? 'text-amber-700 dark:text-amber-300' : 'text-gray-700 dark:text-gray-300'"
                                >
                                    <span x-text="item.nombre"></span>
                                    <template x-if="item.esSimilar">
                                        <span class="ml-1 text-xs opacity-60">(similar)</span>
                                    </template>
                                </button>
                            </template>
                        </div>
                    </div>

                    {{-- Duplicado exacto → BLOQUEADO --}}
                    <template x-if="nombreDuplicadoExacto">
                        <div class="mt-2 rounded-xl border border-red-200 bg-red-50 dark:bg-red-900/20 dark:border-red-800 px-3 py-2.5">
                            <p class="text-xs font-semibold text-red-700 dark:text-red-300">
                                🚫 Nombre duplicado — no se puede guardar
                            </p>
                            <p class="text-xs text-red-600 dark:text-red-400 mt-0.5">
                                "<strong x-text="nombreDuplicadoExacto"></strong>" ya existe en esta categoria.
                                Escribe un nombre diferente para continuar.
                            </p>
                        </div>
                    </template>

                    {{-- Similar → ADVERTENCIA --}}
                    <template x-if="nombreDuplicadoSimilar && !nombreDuplicadoExacto">
                        <div class="mt-2 rounded-xl border border-amber-200 bg-amber-50 dark:bg-amber-900/20 dark:border-amber-800 px-3 py-2.5">
                            <p class="text-xs font-semibold text-amber-700 dark:text-amber-300">
                                ⚠️ Nombre muy similar a uno existente
                            </p>
                            <p class="text-xs text-amber-600 dark:text-amber-400 mt-0.5">
                                Ya existe "<strong x-text="nombreDuplicadoSimilar"></strong>" en esta categoria.
                            </p>
                            <div class="mt-2 flex gap-3">
                                <button type="button" @click="forzarNuevoNombre()"
                                    class="text-xs font-bold text-amber-700 dark:text-amber-300 underline hover:no-underline">
                                    Guardar de todas formas
                                </button>
                            </div>
                        </div>
                    </template>

                    {{-- En otra categoria → INFORMATIVO --}}
                    <template x-if="nombreEnOtraCategoria.length > 0 && !nombreDuplicadoExacto">
                        <div class="mt-2 rounded-xl border border-blue-200 bg-blue-50 dark:bg-blue-900/20 dark:border-blue-800 px-3 py-2.5">
                            <p class="text-xs font-semibold text-blue-700 dark:text-blue-300">
                                ℹ️ Este nombre existe en otra(s) categoria(s)
                            </p>
                            <ul class="mt-1 space-y-0.5">
                                <template x-for="dup in nombreEnOtraCategoria" :key="dup.categoria">
                                    <li class="text-xs text-blue-600 dark:text-blue-400">
                                        — "<span x-text="dup.nombre"></span>" en <strong x-text="dup.categoria"></strong>
                                    </li>
                                </template>
                            </ul>
                            <p class="text-xs text-blue-500 mt-1">Puedes continuar si es un examen diferente para esta categoria.</p>
                        </div>
                    </template>

                    @error('nombre_examen')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- DESCRIPCION --}}
                <div>
                    <label class="{{ $labelClass }}">Descripcion (opcional)</label>
                    <textarea name="descripcion" rows="3"
                              placeholder="Preparacion del paciente, observaciones..."
                              class="{{ $inputClass }}">{{ $descripcionActual }}</textarea>
                </div>

                {{-- COSTO + ESTADO --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                    <div>
                        <label class="{{ $labelClass }}">Costo de referencia</label>
                        <div class="relative" x-data="{ focused: false }">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4">
                                <span class="text-sm font-semibold"
                                      :class="focused ? 'text-[#44B0B3]' : 'text-gray-400 dark:text-gray-500'">
                                    Bs.
                                </span>
                            </div>
                            <input type="number" name="costo_ref"
                                   value="{{ $costoActual }}"
                                   @focus="focused = true" @blur="focused = false"
                                   step="0.01" min="0" placeholder="0.00"
                                   class="{{ $inputClass }} pl-10">
                        </div>
                        <p class="mt-1 text-xs text-gray-400 dark:text-gray-500">Opcional. Solo como referencia interna.</p>
                    </div>

                    <div>
                        <label class="{{ $labelClass }}">Estado <span class="text-red-500">*</span></label>
                        <div class="grid grid-cols-2 gap-3">
                            <label class="relative flex cursor-pointer">
                                <input type="radio" name="estado" value="activo"
                                       {{ $estadoActual === 'activo' ? 'checked' : '' }} class="sr-only peer">
                                <div class="w-full flex items-center gap-3 px-4 py-3 rounded-2xl border border-gray-200 dark:border-gray-700
                                            peer-checked:border-emerald-400 peer-checked:bg-emerald-50 dark:peer-checked:bg-emerald-900/20
                                            hover:bg-gray-50 dark:hover:bg-gray-800/50 transition cursor-pointer">
                                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 shrink-0"></span>
                                    <span class="text-sm font-semibold text-gray-700 dark:text-gray-200">Activo</span>
                                </div>
                            </label>
                            <label class="relative flex cursor-pointer">
                                <input type="radio" name="estado" value="inactivo"
                                       {{ $estadoActual === 'inactivo' ? 'checked' : '' }} class="sr-only peer">
                                <div class="w-full flex items-center gap-3 px-4 py-3 rounded-2xl border border-gray-200 dark:border-gray-700
                                            peer-checked:border-gray-400 peer-checked:bg-gray-100 dark:peer-checked:bg-gray-700/50
                                            hover:bg-gray-50 dark:hover:bg-gray-800/50 transition cursor-pointer">
                                    <span class="w-2.5 h-2.5 rounded-full bg-gray-400 shrink-0"></span>
                                    <span class="text-sm font-semibold text-gray-700 dark:text-gray-200">Inactivo</span>
                                </div>
                            </label>
                        </div>
                    </div>

                </div>

            </div>

            {{-- FOOTER --}}
            <div class="px-5 sm:px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900">
                <div class="flex items-center justify-between gap-3">
                    <p class="text-xs text-gray-400 dark:text-gray-500">
                        Los campos con <span class="text-red-500">*</span> son obligatorios.
                    </p>
                    <div class="flex items-center gap-3">
                        <a href="{{ route('admin.examenes.index') }}" class="{{ $secondaryBtn }}">Cancelar</a>
                        <button type="submit"
                                :disabled="nombreDuplicadoExacto"
                                :class="nombreDuplicadoExacto ? 'opacity-50 cursor-not-allowed' : ''"
                                class="{{ $primaryBtn }}">
                            Guardar cambios
                        </button>
                    </div>
                </div>
            </div>

        </form>
    </div>

    <script>
    function examenEditComponent() {
        return {
            // Datos del servidor
            todasCategorias:    @json($categoriasJs),
            todosExamenes:      @json($todosExamenesJs),
            categoriaIdInicial: {{ $categoriaActualId ?: 'null' }},
            nombreInicial:      '{{ addslashes($nombreActual) }}',
            nombreCatInicial:   '{{ addslashes($nombreCatActual) }}',

            // Categoria
            categoriaInput:      '',
            categoriaId:         '',
            prefijoCat:          '',
            categoriaOpen:       false,
            categoriasFiltradas: [],
            examenesCategoria:   [],

            // Nombre
            nombreInput:            '',
            nombreExamenValor:      '',
            nombreOpen:             false,
            nombresFiltrados:       [],
            nombreDuplicadoExacto:  null,
            nombreDuplicadoSimilar: null,
            nombreEnOtraCategoria:  [],
            forzarNuevo:            false,

            init() {
                // Precargar categoria actual
                if (this.categoriaIdInicial) {
                    const cat = this.todasCategorias.find(c => c.id == this.categoriaIdInicial);
                    if (cat) {
                        this.categoriaInput    = cat.nombre;
                        this.categoriaId       = cat.id;
                        this.prefijoCat        = cat.prefijo;
                        this.examenesCategoria = cat.examenes;
                    }
                }

                // Precargar nombre actual
                this.nombreInput       = this.nombreInicial;
                this.nombreExamenValor = this.nombreInicial;

                this.categoriasFiltradas = [...this.todasCategorias];
            },

            // ── NORMALIZACIÓN ──
            norm(text) {
                return (text || '')
                    .toString()
                    .normalize('NFD')
                    .replace(/[\u0300-\u036f]/g, '')
                    .replace(/[^a-z0-9\s]/gi, ' ')
                    .replace(/\s+/g, ' ')
                    .trim()
                    .toLowerCase();
            },

            esSimilar(a, b) {
                return this.norm(a) === this.norm(b);
            },

            esMuyParecido(a, b) {
                const na = this.norm(a), nb = this.norm(b);
                return na.includes(nb) || nb.includes(na) || this.levenshtein(na, nb) <= 2;
            },

            levenshtein(a, b) {
                const m = a.length, n = b.length;
                const dp = Array.from({ length: m + 1 }, (_, i) =>
                    Array.from({ length: n + 1 }, (_, j) => i === 0 ? j : j === 0 ? i : 0)
                );
                for (let i = 1; i <= m; i++)
                    for (let j = 1; j <= n; j++)
                        dp[i][j] = a[i-1] === b[j-1]
                            ? dp[i-1][j-1]
                            : 1 + Math.min(dp[i-1][j-1], dp[i-1][j], dp[i][j-1]);
                return dp[m][n];
            },

            // ── CATEGORIA ──
            filtrarCategorias() {
                const q = this.norm(this.categoriaInput);
                this.categoriasFiltradas = q
                    ? this.todasCategorias.filter(c => this.norm(c.nombre).includes(q) || this.esMuyParecido(c.nombre, this.categoriaInput))
                    : [...this.todasCategorias];
            },

            seleccionarCategoria(cat) {
                this.categoriaInput    = cat.nombre;
                this.categoriaId       = cat.id;
                this.prefijoCat        = cat.prefijo;
                this.examenesCategoria = cat.examenes;
                this.categoriaOpen     = false;
                // Al cambiar categoria, re-validar el nombre actual
                this.detectarDuplicado();
            },

            restaurarCategoria() {
                // Si no hay seleccion valida, volver a la anterior
                if (!this.categoriaId) {
                    const cat = this.todasCategorias.find(c => c.id == this.categoriaIdInicial);
                    if (cat) {
                        this.categoriaInput    = cat.nombre;
                        this.categoriaId       = cat.id;
                        this.prefijoCat        = cat.prefijo;
                        this.examenesCategoria = cat.examenes;
                    }
                }
            },

            // ── NOMBRE ──
            onNombreInput() {
                this.nombreExamenValor = this.nombreInput;
                this.forzarNuevo       = false;
                this.detectarDuplicado();
                this.filtrarNombres();
            },

            filtrarNombres() {
                const q = this.norm(this.nombreInput);
                this.nombresFiltrados = (this.examenesCategoria || [])
                    .filter(n => !q || this.norm(n).includes(q) || this.esMuyParecido(n, this.nombreInput))
                    .map(n => ({
                        nombre:    n,
                        esSimilar: this.esSimilar(n, this.nombreInput) && this.norm(n) !== this.norm(this.nombreInput),
                    }));
            },

            detectarDuplicado() {
                const inputNorm = this.norm(this.nombreInput);
                this.nombreDuplicadoExacto  = null;
                this.nombreDuplicadoSimilar = null;
                this.nombreEnOtraCategoria  = [];

                if (!inputNorm) return;

                // 1. Exacto en misma categoria
                const exacto = (this.examenesCategoria || []).find(n =>
                    this.norm(n) === inputNorm
                );
                if (exacto) {
                    this.nombreDuplicadoExacto = exacto;
                    return;
                }

                // 2. Similar en misma categoria
                if (!this.forzarNuevo) {
                    const similar = (this.examenesCategoria || []).find(n =>
                        this.norm(n) !== inputNorm && this.esSimilar(n, this.nombreInput)
                    );
                    if (similar) this.nombreDuplicadoSimilar = similar;
                }

                // 3. En otra categoria (solo informativo)
                const enOtras = this.todosExamenes.filter(e =>
                    e.categoria_id != this.categoriaId &&
                    (this.norm(e.nombre) === inputNorm || this.esSimilar(e.nombre, this.nombreInput))
                );
                this.nombreEnOtraCategoria = enOtras.map(e => ({
                    nombre: e.nombre, categoria: e.categoria,
                }));
            },

            // Al hacer clic en un item existente del dropdown → bloquear
            seleccionarExistente(nombre) {
                this.nombreInput          = nombre;
                this.nombreExamenValor    = nombre;
                this.nombreOpen           = false;
                this.nombreDuplicadoExacto = nombre; // bloqueado inmediatamente
                this.nombreDuplicadoSimilar= null;
            },

            forzarNuevoNombre() {
                this.forzarNuevo            = true;
                this.nombreDuplicadoSimilar = null;
                this.nombreExamenValor      = this.nombreInput.trim();
            },

            // ── SUBMIT ──
            intentarEnviar(form) {
                if (this.nombreDuplicadoExacto) return;

                if (!this.categoriaId) {
                    alert('Selecciona una categoria.');
                    return;
                }

                if (!this.nombreExamenValor.trim()) {
                    alert('Escribe el nombre del examen.');
                    return;
                }

                form.submit();
            },
        }
    }
    </script>

    <style>
        [x-cloak] { display: none !important; }
        .custom-scroll::-webkit-scrollbar { width: 6px; }
        .custom-scroll::-webkit-scrollbar-track { background: transparent; }
        .custom-scroll::-webkit-scrollbar-thumb { background: rgba(148,163,184,.35); border-radius: 999px; }
        .dark .custom-scroll::-webkit-scrollbar-thumb { background: rgba(71,85,105,.55); }
        input[type=number]::-webkit-inner-spin-button,
        input[type=number]::-webkit-outer-spin-button { -webkit-appearance: none; margin: 0; }
        input[type=number] { -moz-appearance: textfield; }
    </style>

</x-app-layout>