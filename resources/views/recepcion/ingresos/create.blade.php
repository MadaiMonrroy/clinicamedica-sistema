{{--
    resources/views/recepcion/ingresos/create.blade.php
    Vista de creación de ingreso con el panel de espera al lado derecho.
--}}
<x-app-layout>
@php
    $card = 'rounded-[2rem] border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 shadow-sm';
    $inputClass = 'block w-full px-4 py-3 rounded-2xl text-sm
                   bg-white dark:bg-gray-800
                   border border-gray-200 dark:border-gray-700
                   text-gray-900 dark:text-white
                   placeholder-gray-400 dark:placeholder-gray-500
                   focus:outline-none focus:ring-2 focus:ring-[#52ABB1]/40 focus:border-[#52ABB1]
                   transition';
    $labelClass = 'block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5';

    $pacienteNombreCompleto = $paciente
        ? trim(($paciente->nombres ?? '') . ' ' . ($paciente->apellido_paterno ?? '') . ' ' . ($paciente->apellido_materno ?? ''))
        : '';

    $pacienteEdad = ($paciente && $paciente->fecha_nacimiento)
        ? \Carbon\Carbon::parse($paciente->fecha_nacimiento)->age
        : null;

    $pacienteQuery = $paciente
        ? $pacienteNombreCompleto . ' — ' . $paciente->ci
        : '';

    $pacienteInfoInicial = $paciente ? [
        'id'             => $paciente->id,
        'nombre_completo'=> $pacienteNombreCompleto,
        'ci'             => $paciente->ci,
        'sexo'           => $paciente->sexo,
        'edad'           => $pacienteEdad,
    ] : null;

    $categoriasJs = $categorias->map(fn($c) => [
        'id'      => $c->id,
        'nombre'  => $c->nombre,
        'prefijo' => $c->codigo_prefijo,
        'examenes'=> $c->examenesActivos->map(fn($e) => [
            'id'     => $e->id,
            'nombre' => $e->nombre_examen,
            'codigo' => $e->cod_examen,
        ])->values()->toArray(),
    ])->values()->toArray();
@endphp

{{--
    Layout de dos columnas:
    · Izquierda  → formulario de ingreso (ocupa el espacio disponible)
    · Derecha    → panel de sala de espera (ancho fijo, sticky)
--}}
<div class="flex gap-6 items-start">

    {{-- ════════════════════════════════════════════════════════════
         COLUMNA IZQUIERDA — Formulario de ingreso
    ════════════════════════════════════════════════════════════ --}}
    <div class="flex-1 min-w-0 space-y-6">

        {{-- Header --}}
        <section class="{{ $card }} p-5 sm:p-6">
            <p class="text-xs font-black uppercase tracking-[0.18em] text-gray-400 dark:text-gray-500">
                Recepcion
            </p>
            <h1 class="mt-1.5 text-2xl font-bold text-gray-900 dark:text-white">
                Nuevo ingreso
            </h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                Registra la entrada de un paciente al sistema clinico.
            </p>
        </section>

        {{-- Formulario --}}
        <section class="{{ $card }} p-5 sm:p-6">
            <form
                method="POST"
                action="{{ route('recepcion.ingresos.store') }}"
                x-data="ingresoFormComponent()"
                x-init="init()"
                @click.outside="mostrarLista = false; categoriaOpen = false"
            >
                @csrf
                <input type="hidden" name="paciente_id" :value="pacienteId">

                <template x-for="examen in examenesSeleccionados" :key="examen.id">
                    <input type="hidden" name="examenes[]" :value="examen.id">
                </template>

                <div class="space-y-5">

                    {{-- BUSCADOR DE PACIENTE --}}
                    <div>
                        <label class="{{ $labelClass }}">
                            Paciente <span class="text-red-500 ml-0.5">*</span>
                        </label>

                        <div x-show="pacienteInfo" x-cloak
                             class="flex items-center justify-between gap-3 px-4 py-3 rounded-2xl
                                    border border-[#52ABB1]/40 bg-[#52ABB1]/5 dark:bg-[#52ABB1]/10">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="w-9 h-9 rounded-full flex items-center justify-center text-xs font-bold flex-shrink-0"
                                     style="background:rgba(82,171,177,.15);color:#52ABB1">
                                    <span x-text="pacienteInfo ? pacienteInfo.nombre_completo.split(' ').map(w=>w[0]).slice(0,2).join('') : ''"></span>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-sm font-semibold text-gray-900 dark:text-white truncate"
                                       x-text="pacienteInfo?.nombre_completo"></p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400"
                                       x-text="'CI: ' + (pacienteInfo?.ci ?? '') + (pacienteInfo?.edad ? ' · ' + pacienteInfo.edad + ' años' : '') + (pacienteInfo?.sexo ? ' · ' + pacienteInfo.sexo : '')"></p>
                                </div>
                            </div>
                            <button type="button" @click="limpiarPaciente()"
                                    class="flex-shrink-0 text-xs text-gray-400 hover:text-red-500 dark:hover:text-red-400 transition font-medium">
                                Cambiar
                            </button>
                        </div>

                        <div x-show="!pacienteInfo" class="relative">
                            <div class="relative">
                                <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"
                                     fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/>
                                </svg>
                                <input type="text"
                                       x-ref="inputBusqueda"
                                       x-model="query"
                                       @input="buscarPaciente()"
                                       @focus="query.length >= 2 && buscarPaciente()"
                                       @keydown.escape="mostrarLista = false"
                                       placeholder="Buscar por nombre o CI..."
                                       autocomplete="off"
                                       class="{{ $inputClass }} pl-10 pr-10"/>
                                <div x-show="buscando" class="absolute right-3.5 top-1/2 -translate-y-1/2">
                                    <svg class="w-4 h-4 animate-spin text-[#52ABB1]" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div x-show="mostrarLista" x-cloak
                                 class="absolute z-20 left-0 right-0 mt-1 bg-white dark:bg-gray-800
                                        border border-gray-200 dark:border-gray-700 rounded-2xl shadow-lg overflow-hidden">
                                <ul>
                                    <template x-for="p in resultados" :key="p.id">
                                        <li>
                                            <button type="button" @click="seleccionarPaciente(p)"
                                                    class="w-full flex items-center gap-3 px-4 py-3 text-left
                                                           hover:bg-gray-50 dark:hover:bg-gray-700/60 transition
                                                           border-b border-gray-100 dark:border-gray-700 last:border-b-0">
                                                <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold flex-shrink-0"
                                                     style="background:rgba(82,171,177,.15);color:#52ABB1">
                                                    <span x-text="p.nombre_completo.split(' ').map(w=>w[0]).slice(0,2).join('')"></span>
                                                </div>
                                                <div class="min-w-0">
                                                    <p class="text-sm font-semibold text-gray-900 dark:text-white truncate" x-text="p.nombre_completo"></p>
                                                    <p class="text-xs text-gray-500 dark:text-gray-400"
                                                       x-text="'CI: ' + p.ci + (p.edad ? ' · ' + p.edad + ' años' : '') + (p.sexo ? ' · ' + p.sexo : '')"></p>
                                                </div>
                                            </button>
                                        </li>
                                    </template>
                                </ul>
                            </div>
                        </div>
                        @error('paciente_id')
                            <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- TIPO DE INGRESO --}}
                    <div>
                        <label class="{{ $labelClass }}">
                            Tipo de ingreso <span class="text-red-500 ml-0.5">*</span>
                        </label>
                        <div class="grid grid-cols-2 gap-3">
                            <label class="relative cursor-pointer">
                                <input type="radio" name="tipo_ingreso" value="enfermeria"
                                       x-model="tipoIngreso" class="peer sr-only"
                                       {{ old('tipo_ingreso', 'enfermeria') === 'enfermeria' ? 'checked' : '' }}>
                                <div class="flex items-center gap-3 px-4 py-3.5 rounded-2xl border
                                            border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800
                                            peer-checked:border-[#52ABB1] peer-checked:bg-[#52ABB1]/8
                                            dark:peer-checked:bg-[#52ABB1]/15 transition">
                                    <svg class="w-5 h-5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-3-3v6m8-3A9 9 0 113 12a9 9 0 0118 0z"/>
                                    </svg>
                                    <div>
                                        <p class="text-sm font-semibold text-gray-800 dark:text-gray-100">Enfermeria</p>
                                        <p class="text-[11px] text-gray-400 dark:text-gray-500">Evaluacion inicial</p>
                                    </div>
                                </div>
                            </label>
                            <label class="relative cursor-pointer">
                                <input type="radio" name="tipo_ingreso" value="laboratorio_directo"
                                       x-model="tipoIngreso" class="peer sr-only"
                                       {{ old('tipo_ingreso') === 'laboratorio_directo' ? 'checked' : '' }}>
                                <div class="flex items-center gap-3 px-4 py-3.5 rounded-2xl border
                                            border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800
                                            peer-checked:border-[#52ABB1] peer-checked:bg-[#52ABB1]/8
                                            dark:peer-checked:bg-[#52ABB1]/15 transition">
                                    <svg class="w-5 h-5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                              d="M9 3h6m-1 0v6.586l4.95 7.425A2 2 0 0117.286 20H6.714a2 2 0 01-1.664-2.989L10 9.586V3"/>
                                    </svg>
                                    <div>
                                        <p class="text-sm font-semibold text-gray-800 dark:text-gray-100">Laboratorio</p>
                                        <p class="text-[11px] text-gray-400 dark:text-gray-500">Acceso directo</p>
                                    </div>
                                </div>
                            </label>
                        </div>
                        @error('tipo_ingreso')
                            <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- SELECTOR DE EXAMENES --}}
                    <div x-show="tipoIngreso === 'laboratorio_directo'" x-cloak
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 -translate-y-2"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         class="space-y-4 rounded-2xl border border-[#52ABB1]/30 bg-[#52ABB1]/5 dark:bg-[#52ABB1]/10 p-4">

                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-[#52ABB1]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 3h6m-1 0v6.586l4.95 7.425A2 2 0 0117.286 20H6.714a2 2 0 01-1.664-2.989L10 9.586V3"/>
                            </svg>
                            <p class="text-sm font-bold text-gray-700 dark:text-gray-200">Seleccionar examenes</p>
                        </div>

                        <div>
                            <label class="{{ $labelClass }}">Categoria <span class="text-red-500">*</span></label>
                            <div class="relative" @click.outside="categoriaOpen = false">
                                <button type="button" @click="categoriaOpen = !categoriaOpen"
                                        class="inline-flex w-full items-center justify-between px-4 py-3 text-sm font-medium rounded-2xl
                                               text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800
                                               border border-gray-200 dark:border-gray-700
                                               hover:bg-gray-50 dark:hover:bg-gray-700
                                               focus:outline-none focus:ring-2 focus:ring-[#52ABB1]/40 focus:border-[#52ABB1]
                                               transition shadow-sm">
                                    <span x-text="categoriaSeleccionada ? categoriaSeleccionada.nombre : 'Selecciona una categoria...'"></span>
                                    <svg class="fill-current h-4 w-4 text-gray-400 ml-2 shrink-0 transition-transform duration-200"
                                         :class="{ 'rotate-180': categoriaOpen }" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                    </svg>
                                </button>
                                <div x-show="categoriaOpen" x-cloak x-transition
                                     class="absolute z-50 mt-2 w-full max-h-60 overflow-y-auto rounded-2xl
                                            border border-gray-200 dark:border-gray-700
                                            bg-white dark:bg-gray-800 shadow-xl custom-scroll">
                                    <template x-for="cat in todasCategorias" :key="cat.id">
                                        <button type="button" @click="seleccionarCategoria(cat)"
                                                class="flex items-center justify-between w-full px-4 py-2.5 text-left text-sm transition"
                                                :class="categoriaSeleccionada?.id === cat.id
                                                    ? 'bg-[#52ABB1]/10 text-[#52ABB1] font-semibold'
                                                    : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700'">
                                            <span x-text="cat.nombre"></span>
                                            <span class="text-xs font-mono font-bold text-[#52ABB1] ml-2" x-text="cat.prefijo"></span>
                                        </button>
                                    </template>
                                </div>
                            </div>
                        </div>

                        <div x-show="categoriaSeleccionada" x-cloak>
                            <label class="{{ $labelClass }}">
                                Examenes disponibles
                                <span class="ml-1 text-xs font-normal text-gray-400">
                                    (<span x-text="examenesDisponibles.length"></span> en esta categoria)
                                </span>
                            </label>
                            <div class="relative mb-3">
                                <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"
                                     fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                                <input type="text" x-model="buscarExamen" placeholder="Filtrar examenes..."
                                       class="{{ $inputClass }} pl-9 py-2.5 text-sm bg-white dark:bg-gray-800">
                            </div>
                            <div class="max-h-52 overflow-y-auto rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 custom-scroll">
                                <template x-if="examenesFiltrados.length === 0">
                                    <p class="px-4 py-3 text-sm text-gray-400 text-center">No se encontraron examenes.</p>
                                </template>
                                <template x-for="examen in examenesFiltrados" :key="examen.id">
                                    <button type="button" @click="toggleExamen(examen)"
                                            class="flex items-center justify-between w-full px-4 py-2.5 text-left text-sm
                                                   border-b border-gray-100 dark:border-gray-700 last:border-b-0 transition"
                                            :class="estaSeleccionado(examen.id)
                                                ? 'bg-[#52ABB1]/10 dark:bg-[#52ABB1]/20'
                                                : 'hover:bg-gray-50 dark:hover:bg-gray-700/50'">
                                        <div class="flex items-center gap-3">
                                            <div class="w-5 h-5 rounded-md border-2 flex items-center justify-center flex-shrink-0 transition"
                                                 :class="estaSeleccionado(examen.id)
                                                     ? 'bg-[#52ABB1] border-[#52ABB1]'
                                                     : 'border-gray-300 dark:border-gray-600'">
                                                <svg x-show="estaSeleccionado(examen.id)" class="w-3 h-3 text-white" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                                </svg>
                                            </div>
                                            <span class="text-gray-800 dark:text-gray-100 font-medium" x-text="examen.nombre"></span>
                                        </div>
                                        <span class="text-xs font-mono text-gray-400" x-text="examen.codigo"></span>
                                    </button>
                                </template>
                            </div>
                        </div>

                        <div x-show="examenesSeleccionados.length > 0" x-cloak>
                            <div class="flex items-center justify-between mb-2">
                                <p class="text-xs font-bold text-gray-600 dark:text-gray-400 uppercase tracking-wide">
                                    Seleccionados (<span x-text="examenesSeleccionados.length"></span>)
                                </p>
                                <button type="button" @click="examenesSeleccionados = []"
                                        class="text-xs text-red-400 hover:text-red-600 transition">
                                    Limpiar todo
                                </button>
                            </div>
                            <div class="flex flex-wrap gap-2">
                                <template x-for="examen in examenesSeleccionados" :key="examen.id">
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-[#52ABB1]/15 dark:bg-[#52ABB1]/25
                                                 text-[#52ABB1] text-xs font-semibold px-3 py-1">
                                        <span x-text="examen.nombre"></span>
                                        <button type="button" @click="quitarExamen(examen.id)"
                                                class="hover:text-red-500 transition leading-none">✕</button>
                                    </span>
                                </template>
                            </div>
                        </div>

                        <template x-if="tipoIngreso === 'laboratorio_directo' && examenesSeleccionados.length === 0">
                            <p class="text-xs text-amber-600 dark:text-amber-400 font-medium">
                                ⚠️ Debes seleccionar al menos un examen para continuar.
                            </p>
                        </template>

                        @error('examenes')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- PRIORIDAD --}}
                    <div>
                        <label class="{{ $labelClass }}">
                            Prioridad <span class="text-red-500 ml-0.5">*</span>
                        </label>
                        <div class="grid grid-cols-2 gap-3">
                            <label class="relative cursor-pointer">
                                <input type="radio" name="prioridad_inicial" value="normal" class="peer sr-only"
                                       {{ old('prioridad_inicial', 'normal') === 'normal' ? 'checked' : '' }}>
                                <div class="flex items-center gap-3 px-4 py-3.5 rounded-2xl border
                                            border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800
                                            peer-checked:border-emerald-400 peer-checked:bg-emerald-50
                                            dark:peer-checked:bg-emerald-900/20 transition">
                                    <span class="w-3 h-3 rounded-full bg-emerald-400 flex-shrink-0"></span>
                                    <p class="text-sm font-semibold text-gray-800 dark:text-gray-100">Normal</p>
                                </div>
                            </label>
                            <label class="relative cursor-pointer">
                                <input type="radio" name="prioridad_inicial" value="urgente" class="peer sr-only"
                                       {{ old('prioridad_inicial') === 'urgente' ? 'checked' : '' }}>
                                <div class="flex items-center gap-3 px-4 py-3.5 rounded-2xl border
                                            border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800
                                            peer-checked:border-red-400 peer-checked:bg-red-50
                                            dark:peer-checked:bg-red-900/20 transition">
                                    <span class="w-3 h-3 rounded-full bg-red-400 flex-shrink-0"></span>
                                    <p class="text-sm font-semibold text-gray-800 dark:text-gray-100">Urgente</p>
                                </div>
                            </label>
                        </div>
                        @error('prioridad_inicial')
                            <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- MOTIVO --}}
                    <div>
                        <label class="{{ $labelClass }}">
                            Motivo de ingreso
                            <span class="text-gray-400 font-normal text-xs ml-1">(opcional)</span>
                        </label>
                        <textarea name="motivo_ingreso" rows="3"
                                  placeholder="Describe brevemente el motivo..."
                                  class="{{ $inputClass }} resize-none">{{ old('motivo_ingreso') }}</textarea>
                    </div>

                    {{-- N° PRE-INGRESO --}}
                    <div>
                        <label class="{{ $labelClass }}">
                            N° de pre-ingreso
                            <span class="text-gray-400 font-normal text-xs ml-1">(automatico)</span>
                        </label>
                        <input type="text" value="{{ $numeroPreingreso }}" readonly
                               class="{{ $inputClass }} bg-gray-50 dark:bg-gray-800/60 cursor-not-allowed font-semibold"/>
                        <p class="mt-1.5 text-xs text-gray-400 dark:text-gray-500">Generado automaticamente.</p>
                    </div>

                    {{-- BOTONES --}}
                    <div class="flex items-center justify-between gap-3 pt-2 border-t border-gray-100 dark:border-gray-700">
                        <a href="{{ url()->previous() }}"
                           class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold
                                  border border-gray-200 dark:border-gray-700
                                  text-gray-600 dark:text-gray-400
                                  hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                            Cancelar
                        </a>
                        <button type="submit"
                                :disabled="!puedeEnviar"
                                :class="puedeEnviar ? 'opacity-100 cursor-pointer hover:opacity-90' : 'opacity-40 cursor-not-allowed'"
                                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-bold text-white transition"
                                style="background:#52ABB1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                            </svg>
                            Registrar ingreso
                        </button>
                    </div>

                </div>
            </form>
        </section>

    </div>{{-- /columna izquierda --}}


    {{-- ════════════════════════════════════════════════════════════
         COLUMNA DERECHA — Panel de sala de espera (sticky)
    ════════════════════════════════════════════════════════════ --}}
    <div class="w-[850px] flex-shrink-0 sticky top-6">
        @include('recepcion.components.panel-espera')
    </div>

</div>{{-- /flex layout --}}


{{-- ── Alpine.js — Formulario de ingreso ───────────────────────────── --}}
<script>
function ingresoFormComponent() {
    return {
        query:        '{{ $pacienteQuery }}',
        pacienteId:   '{{ $paciente?->id ?? '' }}',
        pacienteInfo: @json($pacienteInfoInicial),
        resultados:   [],
        buscando:     false,
        mostrarLista: false,
        timer:        null,

        tipoIngreso: '{{ old('tipo_ingreso', 'enfermeria') }}',

        todasCategorias:       @json($categoriasJs),
        categoriaSeleccionada: null,
        categoriaOpen:         false,
        examenesDisponibles:   [],
        examenesSeleccionados: [],
        buscarExamen:          '',

        init() {},

        get examenesFiltrados() {
            const q = this.buscarExamen.toLowerCase().trim();
            if (!q) return this.examenesDisponibles;
            return this.examenesDisponibles.filter(e =>
                e.nombre.toLowerCase().includes(q) || (e.codigo && e.codigo.toLowerCase().includes(q))
            );
        },

        get puedeEnviar() {
            if (!this.pacienteId) return false;
            if (this.tipoIngreso === 'laboratorio_directo' && this.examenesSeleccionados.length === 0) return false;
            return true;
        },

        async buscarPaciente() {
            clearTimeout(this.timer);
            if (this.query.length < 2) { this.resultados = []; this.mostrarLista = false; return; }
            this.buscando = true;
            this.timer = setTimeout(async () => {
                try {
                    const res = await fetch(`{{ route('recepcion.ingresos.buscar-paciente') }}?q=${encodeURIComponent(this.query)}`, {
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    });
                    this.resultados   = await res.json();
                    this.mostrarLista = this.resultados.length > 0;
                } catch (e) {
                    this.resultados   = [];
                    this.mostrarLista = false;
                } finally {
                    this.buscando = false;
                }
            }, 300);
        },

        seleccionarPaciente(p) {
            this.pacienteId   = p.id;
            this.pacienteInfo = p;
            this.query        = p.nombre_completo + ' — ' + p.ci;
            this.mostrarLista = false;
            this.resultados   = [];
        },

        limpiarPaciente() {
            this.pacienteId   = '';
            this.pacienteInfo = null;
            this.query        = '';
            this.resultados   = [];
            this.mostrarLista = false;
            this.$nextTick(() => this.$refs.inputBusqueda?.focus());
        },

        seleccionarCategoria(cat) {
            this.categoriaSeleccionada = cat;
            this.examenesDisponibles   = cat.examenes;
            this.categoriaOpen         = false;
            this.buscarExamen          = '';
        },

        toggleExamen(examen) {
            this.estaSeleccionado(examen.id) ? this.quitarExamen(examen.id) : this.examenesSeleccionados.push(examen);
        },

        estaSeleccionado(id) {
            return this.examenesSeleccionados.some(e => e.id === id);
        },

        quitarExamen(id) {
            this.examenesSeleccionados = this.examenesSeleccionados.filter(e => e.id !== id);
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
</style>

</x-app-layout>