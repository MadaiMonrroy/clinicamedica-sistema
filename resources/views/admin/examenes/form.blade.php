<x-app-layout>
    @php
        $pageWrap     = 'space-y-6';
        $card         = 'rounded-[2rem] border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 shadow-sm';
        $inputClass   = 'block w-full px-4 py-3 bg-white dark:bg-gray-800/40 border border-gray-200 dark:border-gray-700 focus:border-[#44B0B3] focus:ring-1 focus:ring-[#44B0B3] rounded-2xl text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 transition-all text-sm shadow-sm';
        $labelClass   = 'block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2';
        $primaryBtn   = 'inline-flex items-center justify-center rounded-2xl bg-[#44B0B3] hover:bg-[#389a9d] text-white font-bold px-5 py-3 shadow-lg shadow-[#44B0B3]/25 transition';
        $secondaryBtn = 'inline-flex items-center justify-center rounded-2xl border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-800 px-5 py-3 transition';

        $esEdicion         = isset($examen);
        $accion            = $esEdicion ? route('admin.examenes.update', $examen) : route('admin.examenes.store');
        $categoriaActualId = old('categoria_id', $examen->categoria_id ?? '');
        $nombreActual      = old('nombre_examen', $examen->nombre_examen ?? '');
        $costoActual       = old('costo_ref', $examen->costo_ref ?? '');
        $estadoActual      = old('estado', $examen->estado ?? 'activo');
        $descripcionActual = old('descripcion', $examen->descripcion ?? '');

        // Pasar TODOS los examenes para validacion antidupliados en el cliente
        // estructura: { id, nombre, categoria_id, categoria_nombre }
        $todosExamenesJs = $categorias->flatMap(fn($c) =>
            $c->examenesActivos->map(fn($e) => [
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
            'examenes'=> $c->examenesActivos->pluck('nombre_examen')->values()->toArray(),
        ])->values()->toArray();

        $categoriaActual = $categorias->firstWhere('id', $categoriaActualId);
        $nombreCatActual = $categoriaActual?->nombre ?? old('categoria_nueva', '');
    @endphp

    <div class="mx-auto {{ $pageWrap }}">

        {{-- CABECERA --}}
        <section class="{{ $card }} p-5 sm:p-6">
            <p class="text-xs font-black uppercase tracking-[0.18em] text-gray-400 dark:text-gray-500">
                Admin / Examenes / {{ $esEdicion ? 'Editar' : 'Nuevo' }}
            </p>
            <h1 class="mt-2 text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white">
                {{ $esEdicion ? 'Editar: ' . $examen->nombre_examen : 'Registrar nuevo examen' }}
            </h1>
            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                {{ $esEdicion
                    ? 'Modifica los datos del examen.'
                    : 'Selecciona o crea la categoria y el nombre del examen. El sistema verificara automaticamente que no exista un duplicado.' }}
            </p>
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
        <form method="POST" action="{{ $accion }}" class="{{ $card }} overflow-hidden"
              x-data="examenFormComponent()" x-init="init()"
              @submit.prevent="intentarEnviar($el)">
            @csrf
            @if($esEdicion) @method('PUT') @endif

            <input type="hidden" name="categoria_id"    :value="categoriaId">
            <input type="hidden" name="categoria_nueva" :value="categoriaNuevaValor">
            <input type="hidden" name="nombre_examen"   :value="nombreExamenValor">

            <div class="px-5 sm:px-6 py-5 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white">Datos del examen</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    El sistema detecta duplicados automaticamente, incluyendo variantes con acentos o espacios diferentes.
                </p>
            </div>

            <div class="px-5 sm:px-6 py-5 space-y-6">

                {{-- CATEGORIA --}}
                <div>
                    <label class="{{ $labelClass }}">
                        Categoria <span class="text-red-500">*</span>
                    </label>

                    <div class="relative">
                        <input
                            type="text"
                            x-model="categoriaInput"
                            @input="onCategoriaInput()"
                            @focus="categoriaOpen = true; filtrarCategorias()"
                            @click.outside="categoriaOpen = false; normalizarCategoria()"
                            autocomplete="off"
                            placeholder="Escribe el nombre de la categoria..."
                            :class="[
                                '{{ $inputClass }} pr-10',
                                categoriaDuplicadaSimilar ? 'border-amber-400 focus:border-amber-400 focus:ring-amber-400' : '',
                                categoriaId || categoriaNuevaValor ? 'border-[#44B0B3]' : ''
                            ]"
                        >
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-4">
                            <svg class="fill-current h-4 w-4" :class="categoriaId ? 'text-[#44B0B3]' : 'text-gray-400'" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                            </svg>
                        </div>

                        {{-- Dropdown --}}
                        <div
                            x-show="categoriaOpen && (categoriasFiltradas.length > 0 || categoriaInput.trim())"
                            x-cloak x-transition
                            class="absolute z-50 mt-2 w-full max-h-72 overflow-y-auto rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-xl custom-scroll"
                        >
                            <template x-if="categoriasFiltradas.length > 0">
                                <div class="px-4 py-2 border-b border-gray-100 dark:border-gray-700">
                                    <p class="text-xs text-gray-400">Categorias existentes</p>
                                </div>
                            </template>

                            <template x-for="cat in categoriasFiltradas" :key="cat.id">
                                <button type="button" @click="seleccionarCategoria(cat)"
                                    class="flex items-center justify-between w-full px-4 py-2.5 text-left text-sm hover:bg-gray-100 dark:hover:bg-gray-700 transition"
                                    :class="cat.esSimilar ? 'text-amber-700 dark:text-amber-300 bg-amber-50/50 dark:bg-amber-900/10' : 'text-gray-700 dark:text-gray-300'"
                                >
                                    <div>
                                        <span x-text="cat.nombre"></span>
                                        <template x-if="cat.esSimilar">
                                            <span class="ml-2 text-xs bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-300 px-1.5 py-0.5 rounded-full">
                                                similar a lo que escribiste
                                            </span>
                                        </template>
                                    </div>
                                    <span class="text-xs font-mono font-bold text-[#44B0B3] ml-2 shrink-0" x-text="cat.prefijo"></span>
                                </button>
                            </template>

                            {{-- Crear nueva — solo si no hay coincidencia exacta ni similar --}}
                            <template x-if="categoriaInput.trim() && !categoriaExacta() && !categoriaDuplicadaSimilar">
                                <button type="button" @click="crearNuevaCategoria()"
                                    class="flex items-center gap-2 w-full px-4 py-3 text-left text-sm text-[#44B0B3] hover:bg-[#44B0B3]/10 border-t border-gray-100 dark:border-gray-700 transition font-semibold">
                                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                    </svg>
                                    Crear nueva categoria "<span x-text="categoriaInput.trim()"></span>"
                                </button>
                            </template>
                        </div>
                    </div>

                    {{-- Alerta de duplicado similar --}}
                    <template x-if="categoriaDuplicadaSimilar">
                        <div class="mt-2 rounded-xl border border-amber-200 bg-amber-50 dark:bg-amber-900/20 dark:border-amber-800 px-3 py-2.5">
                            <p class="text-xs font-semibold text-amber-700 dark:text-amber-300">
                                ⚠️ Posible duplicado detectado
                            </p>
                            <p class="text-xs text-amber-600 dark:text-amber-400 mt-0.5">
                                Ya existe una categoria llamada "<strong x-text="categoriaDuplicadaSimilar.nombre"></strong>".
                                ¿Quieres usar esa en su lugar?
                            </p>
                            <button type="button" @click="seleccionarCategoria(categoriaDuplicadaSimilar)"
                                class="mt-2 text-xs font-bold text-amber-700 dark:text-amber-300 underline hover:no-underline">
                                Si, usar "<span x-text="categoriaDuplicadaSimilar.nombre"></span>"
                            </button>
                        </div>
                    </template>

                    {{-- Badge categoria seleccionada --}}
                    <template x-if="(categoriaId || categoriaNuevaValor) && !categoriaDuplicadaSimilar">
                        <div class="mt-2 flex items-center gap-2">
                            <span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-semibold"
                                  :class="categoriaNuevaValor
                                    ? 'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-300'
                                    : 'bg-[#44B0B3]/10 text-[#44B0B3]'">
                                <template x-if="categoriaNuevaValor">
                                    <span>Nueva categoria: </span>
                                </template>
                                <span x-text="categoriaInput"></span>
                                <template x-if="prefijoCat">
                                    <span class="font-mono opacity-70" x-text="'(' + prefijoCat + ')'"></span>
                                </template>
                            </span>
                            <button type="button" @click="limpiarCategoria()"
                                    class="text-xs text-gray-400 hover:text-red-500 transition">
                                cambiar
                            </button>
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

                    {{-- Info contextual --}}
                    <template x-if="!categoriaId && !categoriaNuevaValor">
                        <p class="mb-2 text-xs text-gray-400 dark:text-gray-500">
                            Primero selecciona o crea una categoria.
                        </p>
                    </template>
                    <template x-if="categoriaId || categoriaNuevaValor">
                        <p class="mb-2 text-xs text-gray-400 dark:text-gray-500">
                            <span x-text="examenesCategoria.length"></span> examen(es) en esta categoria.
                            Escribe para filtrar o ingresa un nombre nuevo.
                        </p>
                    </template>

                    <div class="relative">
                        <input
                            type="text"
                            x-model="nombreInput"
                            @input="onNombreInput()"
                            @keyup="detectarDuplicadoNombre()"
                            @focus="nombreOpen = true; filtrarNombres()"
                            @click.outside="nombreOpen = false"
                            autocomplete="off"
                            placeholder="Buscar o escribir nombre del examen..."
                            :class="[
                                '{{ $inputClass }} pr-10',
                                nombreDuplicadoExacto ? 'border-red-400 focus:border-red-400 focus:ring-red-400' : '',
                                nombreDuplicadoSimilar && !nombreDuplicadoExacto ? 'border-amber-400 focus:border-amber-400 focus:ring-amber-400' : '',
                                nombreExamenValor && !nombreDuplicadoExacto && !nombreDuplicadoSimilar ? 'border-[#44B0B3]' : ''
                            ]"
                        >
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-4">
                            <svg class="fill-current h-4 w-4 text-gray-400" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                            </svg>
                        </div>

                        {{-- Dropdown nombres --}}
                        <div
                            x-show="nombreOpen && nombresFiltrados.length > 0"
                            x-cloak x-transition
                            class="absolute z-50 mt-2 w-full max-h-72 overflow-y-auto rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-xl custom-scroll"
                        >
                            <div class="px-4 py-2 border-b border-gray-100 dark:border-gray-700">
                                <p class="text-xs text-gray-400">
                                    <span x-text="nombresFiltrados.length"></span> resultado(s) — haz clic para seleccionar
                                </p>
                            </div>

                            <template x-for="item in nombresFiltrados" :key="item.nombre">
                                <button type="button" @click="seleccionarNombre(item.nombre)"
                                    class="block w-full px-4 py-2.5 text-left text-sm hover:bg-gray-100 dark:hover:bg-gray-700 transition"
                                    :class="item.esSimilar ? 'text-amber-700 dark:text-amber-300' : 'text-gray-700 dark:text-gray-300'"
                                >
                                    <span x-text="item.nombre"></span>
                                    <template x-if="item.esSimilar">
                                        <span class="ml-1 text-xs opacity-60">(similar)</span>
                                    </template>
                                </button>
                            </template>

                            {{-- Usar como nuevo si no es duplicado exacto --}}
                            <template x-if="nombreInput.trim() && !nombreExactoEnCategoria() && !nombreDuplicadoExacto">
                                <button type="button" @click="confirmarNuevoNombre()"
                                    class="flex items-center gap-2 w-full px-4 py-3 text-left text-sm text-[#44B0B3] hover:bg-[#44B0B3]/10 border-t border-gray-100 dark:border-gray-700 transition font-semibold">
                                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                    </svg>
                                    Registrar "<span x-text="nombreInput.trim()"></span>" como nuevo examen
                                </button>
                            </template>
                        </div>
                    </div>

                    {{-- Error duplicado exacto en misma categoria --}}
                    <template x-if="nombreDuplicadoExacto">
                        <div class="mt-2 rounded-xl border border-red-200 bg-red-50 dark:bg-red-900/20 dark:border-red-800 px-3 py-2.5">
                            <p class="text-xs font-semibold text-red-700 dark:text-red-300">
                                🚫 Este examen ya existe en esta categoria
                            </p>
                            <p class="text-xs text-red-600 dark:text-red-400 mt-0.5">
                                "<strong x-text="nombreDuplicadoExacto"></strong>" ya esta registrado en esta categoria.
                                Si lo que buscas es consultar este examen, puedes verlo en el listado.
                                Para registrar uno nuevo escribe un nombre diferente.
                            </p>
                        </div>
                    </template>

                    {{-- Advertencia duplicado similar en misma categoria --}}
                    <template x-if="nombreDuplicadoSimilar && !nombreDuplicadoExacto">
                        <div class="mt-2 rounded-xl border border-amber-200 bg-amber-50 dark:bg-amber-900/20 dark:border-amber-800 px-3 py-2.5">
                            <p class="text-xs font-semibold text-amber-700 dark:text-amber-300">
                                ⚠️ Posible duplicado en esta categoria
                            </p>
                            <p class="text-xs text-amber-600 dark:text-amber-400 mt-0.5">
                                Ya existe "<strong x-text="nombreDuplicadoSimilar"></strong>" en esta categoria, que es muy similar a lo que escribiste.
                            </p>
                            <div class="mt-2 flex gap-3">
                                <button type="button" @click="seleccionarNombre(nombreDuplicadoSimilar)"
                                    class="text-xs font-bold text-amber-700 dark:text-amber-300 underline hover:no-underline">
                                    Usar el existente
                                </button>
                                <button type="button" @click="forzarNuevoNombre()"
                                    class="text-xs text-gray-500 dark:text-gray-400 underline hover:no-underline">
                                    Registrar de todas formas como nuevo
                                </button>
                            </div>
                        </div>
                    </template>

                    {{-- Advertencia duplicado en OTRA categoria --}}
                    <template x-if="nombreEnOtraCategoria.length > 0 && !nombreDuplicadoExacto">
                        <div class="mt-2 rounded-xl border border-blue-200 bg-blue-50 dark:bg-blue-900/20 dark:border-blue-800 px-3 py-2.5">
                            <p class="text-xs font-semibold text-blue-700 dark:text-blue-300">
                                ℹ️ Este examen existe en otra(s) categoria(s)
                            </p>
                            <ul class="mt-1 space-y-0.5">
                                <template x-for="dup in nombreEnOtraCategoria" :key="dup.categoria">
                                    <li class="text-xs text-blue-600 dark:text-blue-400">
                                        — "<span x-text="dup.nombre"></span>" en <strong x-text="dup.categoria"></strong>
                                    </li>
                                </template>
                            </ul>
                            <p class="text-xs text-blue-500 dark:text-blue-400 mt-1">
                                Puedes continuar si realmente es un examen diferente para esta categoria.
                            </p>
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
                              placeholder="Preparacion del paciente, observaciones, indicaciones especiales..."
                              class="{{ $inputClass }}">{{ $descripcionActual }}</textarea>
                </div>

                {{-- COSTO + ESTADO --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                    <div>
                        <label class="{{ $labelClass }}">Costo de referencia</label>
                        <div class="relative" x-data="{ costo: '{{ $costoActual }}', focused: false }">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4">
                                <span class="text-sm font-semibold"
                                      :class="focused ? 'text-[#44B0B3]' : 'text-gray-400 dark:text-gray-500'">
                                    Bs.
                                </span>
                            </div>
                            <input type="number" name="costo_ref" x-model="costo"
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
                        <button type="submit" class="{{ $primaryBtn }}"
                                :disabled="nombreDuplicadoExacto"
                                :class="nombreDuplicadoExacto ? 'opacity-50 cursor-not-allowed' : ''">
                            {{ $esEdicion ? 'Guardar cambios' : 'Registrar examen' }}
                        </button>
                    </div>
                </div>
            </div>

        </form>
    </div>

    <script>
    function examenFormComponent() {
        return {
            // ── Datos del servidor ──
            todasCategorias:    @json($categoriasJs),
            todosExamenes:      @json($todosExamenesJs),
            categoriaIdInicial: '{{ $categoriaActualId }}',
            nombreInicial:      '{{ addslashes($nombreActual) }}',
            nombreCatInicial:   '{{ addslashes($nombreCatActual) }}',

            // ── Estado categoria ──
            categoriaInput:           '',
            categoriaId:              '',
            categoriaNuevaValor:      '',
            categoriaOpen:            false,
            categoriasFiltradas:      [],
            prefijoCat:               '',
            categoriaDuplicadaSimilar: null,

            // ── Estado nombre ──
            nombreInput:            '',
            nombreExamenValor:      '',
            nombreOpen:             false,
            nombresFiltrados:       [],
            examenesCategoria:      [],
            nombreDuplicadoExacto:  null,
            nombreDuplicadoSimilar: null,
            nombreEnOtraCategoria:  [],
            forzarNuevo:            false,

            init() {
                if (this.categoriaIdInicial) {
                    const cat = this.todasCategorias.find(c => c.id == this.categoriaIdInicial);
                    if (cat) {
                        this.categoriaInput    = cat.nombre;
                        this.categoriaId       = cat.id;
                        this.examenesCategoria = cat.examenes;
                        this.prefijoCat        = cat.prefijo;
                    }
                } else if (this.nombreCatInicial) {
                    this.categoriaInput      = this.nombreCatInicial;
                    this.categoriaNuevaValor = this.nombreCatInicial;
                }

                if (this.nombreInicial) {
                    this.nombreInput       = this.nombreInicial;
                    this.nombreExamenValor = this.nombreInicial;
                }

                this.categoriasFiltradas = [...this.todasCategorias];
            },

            // ─────────────────────────────────────────────────────
            // NORMALIZACIÓN EXHAUSTIVA
            // quita acentos, espacios múltiples, puntuación,
            // convierte a minúsculas
            // ─────────────────────────────────────────────────────
            norm(text) {
                return (text || '')
                    .toString()
                    .normalize('NFD')
                    .replace(/[\u0300-\u036f]/g, '') // quitar acentos
                    .replace(/[^a-z0-9\s]/gi, ' ')   // puntuacion → espacio
                    .replace(/\s+/g, ' ')             // espacios multiples → uno
                    .trim()
                    .toLowerCase();
            },

            // Similitud: true si la diferencia es solo acentos/espacios/puntuacion
            esSimilar(a, b) {
                return this.norm(a) === this.norm(b);
            },

            // Similitud laxa: contiene o es muy parecido (para sugerencias)
            esMuyParecido(a, b) {
                const na = this.norm(a);
                const nb = this.norm(b);
                return na.includes(nb) || nb.includes(na) || this.levenshtein(na, nb) <= 2;
            },

            // Distancia de Levenshtein simple
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

            // ─────────────────────────────────────────────────────
            // CATEGORIA
            // ─────────────────────────────────────────────────────
            onCategoriaInput() {
                this.filtrarCategorias();
                this.categoriaId         = '';
                this.categoriaNuevaValor = '';
                this.prefijoCat          = '';
                this.examenesCategoria   = [];
                this.limpiarNombre();
                this.detectarDuplicadoCategoria();
            },

            filtrarCategorias() {
                const q = this.norm(this.categoriaInput);
                if (!q) {
                    this.categoriasFiltradas = this.todasCategorias.map(c => ({ ...c, esSimilar: false }));
                    return;
                }
                this.categoriasFiltradas = this.todasCategorias
                    .filter(c => this.norm(c.nombre).includes(q) || this.esMuyParecido(c.nombre, this.categoriaInput))
                    .map(c => ({ ...c, esSimilar: this.esSimilar(c.nombre, this.categoriaInput) && c.nombre !== this.categoriaInput }));
            },

            detectarDuplicadoCategoria() {
                const input = this.categoriaInput.trim();
                if (!input) { this.categoriaDuplicadaSimilar = null; return; }

                const similar = this.todasCategorias.find(c =>
                    c.nombre !== input && this.esSimilar(c.nombre, input)
                );
                this.categoriaDuplicadaSimilar = similar || null;
            },

            seleccionarCategoria(cat) {
                this.categoriaInput               = cat.nombre;
                this.categoriaId                  = cat.id;
                this.categoriaNuevaValor          = '';
                this.examenesCategoria            = cat.examenes;
                this.prefijoCat                   = cat.prefijo;
                this.categoriaOpen                = false;
                this.categoriaDuplicadaSimilar    = null;
                this.limpiarNombre();
            },

            crearNuevaCategoria() {
                const nombre = this.categoriaInput.trim();
                this.categoriaId               = '';
                this.categoriaNuevaValor       = nombre;
                this.prefijoCat                = '';
                this.examenesCategoria         = [];
                this.categoriaOpen             = false;
                this.categoriaDuplicadaSimilar = null;
            },

            limpiarCategoria() {
                this.categoriaInput               = '';
                this.categoriaId                  = '';
                this.categoriaNuevaValor          = '';
                this.prefijoCat                   = '';
                this.examenesCategoria            = [];
                this.categoriaDuplicadaSimilar    = null;
                this.limpiarNombre();
            },

            normalizarCategoria() {
                if (!this.categoriaId && !this.categoriaNuevaValor && this.categoriaInput.trim()) {
                    const exacta = this.todasCategorias.find(c =>
                        this.esSimilar(c.nombre, this.categoriaInput)
                    );
                    if (exacta) this.seleccionarCategoria(exacta);
                }
            },

            categoriaExacta() {
                return this.todasCategorias.some(c =>
                    this.norm(c.nombre) === this.norm(this.categoriaInput.trim())
                );
            },

            // ─────────────────────────────────────────────────────
            // NOMBRE EXAMEN
            // ─────────────────────────────────────────────────────
            onNombreInput() {
                // Actualizar valor en tiempo real (sin esperar trim)
                this.nombreExamenValor  = this.nombreInput;
                this.forzarNuevo       = false;
                // Detectar PRIMERO, luego filtrar — así el aviso aparece sin espacio
                this.detectarDuplicadoNombre();
                this.filtrarNombres();
            },

            filtrarNombres() {
                const q = this.norm(this.nombreInput);

                // Sugerencias de la categoria actual
                const deCategoria = (this.examenesCategoria || [])
                    .filter(n => !q || this.norm(n).includes(q) || this.esMuyParecido(n, this.nombreInput))
                    .map(n => ({
                        nombre:    n,
                        esSimilar: n !== this.nombreInput && this.esSimilar(n, this.nombreInput),
                    }));

                this.nombresFiltrados = deCategoria;
            },

            detectarDuplicadoNombre() {
                // Usar norm() que ya quita espacios y acentos — no depender de trim()
                const inputNorm = this.norm(this.nombreInput);
                this.nombreDuplicadoExacto  = null;
                this.nombreDuplicadoSimilar = null;
                this.nombreEnOtraCategoria  = [];

                if (!inputNorm) return;

                // 1. Exacto en misma categoria (norm vs norm)
                const exactoEnCat = (this.examenesCategoria || []).find(n =>
                    this.norm(n) === inputNorm
                );
                if (exactoEnCat) {
                    this.nombreDuplicadoExacto = exactoEnCat;
                    return;
                }

                // 2. Similar en misma categoria
                if (!this.forzarNuevo) {
                    const similarEnCat = (this.examenesCategoria || []).find(n =>
                        this.norm(n) !== inputNorm && this.esSimilar(n, this.nombreInput)
                    );
                    if (similarEnCat) {
                        this.nombreDuplicadoSimilar = similarEnCat;
                    }
                }

                // 3. En OTRA categoria (solo informativo)
                const enOtras = this.todosExamenes.filter(e =>
                    e.categoria_id != this.categoriaId &&
                    (this.norm(e.nombre) === inputNorm || this.esSimilar(e.nombre, this.nombreInput))
                );
                this.nombreEnOtraCategoria = enOtras.map(e => ({
                    nombre:    e.nombre,
                    categoria: e.categoria,
                }));
            },

            nombreExactoEnCategoria() {
                return (this.examenesCategoria || []).some(n =>
                    this.norm(n) === this.norm(this.nombreInput.trim())
                );
            },

            seleccionarNombre(nombre) {
                this.nombreInput           = nombre;
                this.nombreExamenValor     = nombre;
                this.nombreOpen            = false;
                // Si seleccionó un nombre que ya existe en esta categoría → bloquearlo
                const existeEnCategoria = (this.examenesCategoria || []).find(n =>
                    this.norm(n) === this.norm(nombre)
                );
                if (existeEnCategoria) {
                    this.nombreDuplicadoExacto  = existeEnCategoria;
                    this.nombreDuplicadoSimilar = null;
                } else {
                    this.nombreDuplicadoExacto  = null;
                    this.nombreDuplicadoSimilar = null;
                }
                // Verificar en otras categorías (solo informativo)
                this.nombreEnOtraCategoria = this.todosExamenes
                    .filter(e => e.categoria_id != this.categoriaId && this.norm(e.nombre) === this.norm(nombre))
                    .map(e => ({ nombre: e.nombre, categoria: e.categoria }));
            },

            confirmarNuevoNombre() {
                this.nombreExamenValor = this.nombreInput.trim();
                this.nombreOpen        = false;
            },

            forzarNuevoNombre() {
                this.forzarNuevo            = true;
                this.nombreDuplicadoSimilar = null;
                this.nombreExamenValor      = this.nombreInput.trim();
            },

            limpiarNombre() {
                this.nombreInput            = '';
                this.nombreExamenValor      = '';
                this.nombreDuplicadoExacto  = null;
                this.nombreDuplicadoSimilar = null;
                this.nombreEnOtraCategoria  = [];
                this.forzarNuevo            = false;
            },

            // ─────────────────────────────────────────────────────
            // SUBMIT — validacion final antes de enviar
            // ─────────────────────────────────────────────────────
            intentarEnviar(form) {
                // Bloquear si hay duplicado exacto
                if (this.nombreDuplicadoExacto) return;

                // Requiere categoria
                if (!this.categoriaId && !this.categoriaNuevaValor) {
                    alert('Selecciona o crea una categoria primero.');
                    return;
                }

                // Requiere nombre
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