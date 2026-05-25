<x-app-layout>
    @php
        $card = 'rounded-[2rem] border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 shadow-sm';
    @endphp

    <div class=" space-y-6">

        {{-- ── HEADER ──────────────────────────────────────────── --}}
        <section class="{{ $card }} p-5 sm:p-6">
            <div class="flex flex-col gap-4 xl:flex-row xl:items-start xl:justify-between">
                <div>
                    <p class="text-xs font-black uppercase tracking-[0.18em] text-gray-400 dark:text-gray-500">
                        Recepción
                    </p>
                    <h1 class="mt-1.5 text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white">
                        Gestión de pacientes
                    </h1>
                    <p class="mt-1.5 text-sm text-gray-500 dark:text-gray-400 max-w-2xl">
                        Consulta el padrón, revisa información personal y registra nuevos ingresos desde admisión.
                    </p>
                </div>
                <div class="flex flex-col sm:flex-row gap-2 shrink-0">
                    <a href="{{ route('recepcion.ingresos.create') }}"
                       class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold
                              border border-gray-200 dark:border-gray-700
                              text-gray-700 dark:text-gray-200
                              hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                        Nuevo ingreso
                    </a>
                    <a href="{{ route('recepcion.pacientes.create') }}"
                       class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold
                              text-white transition hover:opacity-90 active:scale-[.98]"
                       style="background:#52ABB1;">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                        </svg>
                        Nuevo paciente
                    </a>
                </div>
            </div>

            {{-- Stats --}}
            <div class="mt-5 grid grid-cols-2 sm:grid-cols-4 gap-3">
                <div class="rounded-2xl bg-gray-50 dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 px-4 py-4">
                    <p class="text-xs uppercase tracking-widest text-gray-400 dark:text-gray-500">Total pacientes</p>
                    <p class="mt-1.5 text-2xl font-bold" style="color:#52ABB1">{{ $totalPacientes }}</p>
                </div>
                <div class="rounded-2xl bg-gray-50 dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 px-4 py-4">
                    <p class="text-xs uppercase tracking-widest text-gray-400 dark:text-gray-500">Activos</p>
                    <p class="mt-1.5 text-2xl font-bold text-emerald-600 dark:text-emerald-400">{{ $totalActivos }}</p>
                </div>
                <div class="rounded-2xl bg-gray-50 dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 px-4 py-4">
                    <p class="text-xs uppercase tracking-widest text-gray-400 dark:text-gray-500">Inactivos</p>
                    <p class="mt-1.5 text-2xl font-bold text-amber-600 dark:text-amber-400">{{ $totalInactivos }}</p>
                </div>
                <div class="rounded-2xl bg-gray-50 dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 px-4 py-4">
                    <p class="text-xs uppercase tracking-widest text-gray-400 dark:text-gray-500">Ingresos totales</p>
                    <p class="mt-1.5 text-2xl font-bold text-gray-900 dark:text-white">{{ $totalIngresos }}</p>
                </div>
            </div>
        </section>

        {{-- Flash --}}
        @if(session('success'))
            <div class="flex items-center gap-3 px-4 py-3 rounded-2xl text-sm font-medium
                        bg-emerald-50 text-emerald-800 border border-emerald-200
                        dark:bg-emerald-900/20 dark:text-emerald-300 dark:border-emerald-800">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                {{ session('success') }}
            </div>
        @endif

        {{-- ── TABLA ───────────────────────────────────────────── --}}
        <section class="{{ $card }} overflow-hidden"
                 x-data="{
                     search: '',
                     rows: [],
                     visibleCount: 0,
                     perPage: 10,
                     page: 1,
                     get totalPages() { return Math.max(1, Math.ceil(this.visibleCount / this.perPage)) },
                     get pageStart() { return (this.page - 1) * this.perPage },
                     get pageEnd()   { return this.page * this.perPage },
                     get pageButtons() {
    const total = this.totalPages;
    const cur   = this.page;
    if (total <= 7) {
        // Si hay 7 páginas o menos, muestra todas
        return Array.from({ length: total }, (_, i) => i + 1);
    }
    // Ventana deslizante: siempre muestra primera, última y 3 alrededor de la actual
    const pages = new Set([1, total, cur, cur - 1, cur + 1, cur - 2, cur + 2]);
    return [...pages]
        .filter(p => p >= 1 && p <= total)
        .sort((a, b) => a - b);
},
                     init() {
                         this.rows = Array.from(document.querySelectorAll('#pac-tbody tr[data-pac]'));
                         this.filter();
                     },
                     filter() {
                         const q = this.search.toLowerCase().trim();
                         let vis = [];
                         this.rows.forEach(r => {
                             const match = !q || r.dataset.pac.includes(q);
                             r.classList.add('pac-hidden');
                             if (match) vis.push(r);
                         });
                         this.visibleCount = vis.length;
                         this.page = 1;
                         this.paginate(vis);
                     },
                     paginate(vis) {
                         if (!vis) {
                             vis = this.rows.filter(r => !r.dataset.pac ||
                                 !this.search || r.dataset.pac.includes(this.search.toLowerCase().trim()));
                         }
                         this.visibleCount = vis.length;
                         vis.forEach((r, i) => {
                             r.classList.toggle('pac-hidden', i < this.pageStart || i >= this.pageEnd);
                         });
                     },
                     goPage(p) {
                         if (p < 1 || p > this.totalPages) return;
                         this.page = p;
                         const q = this.search.toLowerCase().trim();
                         const vis = this.rows.filter(r => !q || r.dataset.pac.includes(q));
                         this.paginate(vis);
                     }
                 }">

            {{-- Barra de búsqueda --}}
            <div class="flex items-center gap-3 px-5 py-4 border-b border-gray-200 dark:border-gray-700">
                <svg class="w-4 h-4 text-gray-400 dark:text-gray-500 flex-shrink-0"
                     fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/>
                </svg>
                <input
                    type="text"
                    x-model="search"
                    @input="filter()"
                    placeholder="Buscar por CI, nombre o apellido…"
                    class="flex-1 bg-transparent border-none outline-none text-sm
                           text-gray-700 dark:text-gray-300
                           placeholder-gray-400 dark:placeholder-gray-600"
                />
                <span class="text-xs text-gray-400 dark:text-gray-600 shrink-0"
                      x-text="visibleCount + (visibleCount === 1 ? ' paciente' : ' pacientes')">
                </span>
                <span x-show="search.length > 0"
                      @click="search = ''; filter()"
                      class="text-xs text-gray-400 hover:text-gray-600 dark:hover:text-gray-300
                             cursor-pointer select-none shrink-0">
                    Limpiar
                </span>
            </div>

            {{-- Tabla --}}
            <div class="overflow-x-auto">
                <style>.pac-hidden { display: none !important; }</style>
                <table class="min-w-full">
                    <thead class="bg-gray-50 dark:bg-gray-800/50">
                        <tr>
                            <th class="px-5 py-3 text-left text-xs font-black uppercase tracking-widest text-gray-400 dark:text-gray-500">
                                Paciente
                            </th>
                            <th class="px-5 py-3 text-left text-xs font-black uppercase tracking-widest text-gray-400 dark:text-gray-500">
                                CI
                            </th>
                            <th class="px-5 py-3 text-left text-xs font-black uppercase tracking-widest text-gray-400 dark:text-gray-500">
                                Sexo / Edad
                            </th>
                            <th class="px-5 py-3 text-left text-xs font-black uppercase tracking-widest text-gray-400 dark:text-gray-500">
                                Teléfono
                            </th>
                            <th class="px-5 py-3 text-left text-xs font-black uppercase tracking-widest text-gray-400 dark:text-gray-500">
                                Dirección
                            </th>
                            <th class="px-5 py-3 text-left text-xs font-black uppercase tracking-widest text-gray-400 dark:text-gray-500">
                                Ingresos
                            </th>
                            <th class="px-5 py-3 text-left text-xs font-black uppercase tracking-widest text-gray-400 dark:text-gray-500">
                                Estado
                            </th>
                            <th class="px-5 py-3 text-right text-xs font-black uppercase tracking-widest text-gray-400 dark:text-gray-500">
                                Acciones
                            </th>
                        </tr>
                    </thead>
                    <tbody id="pac-tbody" class="divide-y divide-gray-100 dark:divide-gray-800">
                        @forelse($pacientes as $paciente)
                            @php
                                // Dato para búsqueda cliente: ci + nombre completo (sin tildes importa menos, el includes lo resuelve)
                                $searchData = strtolower(implode(' ', array_filter([
                                    $paciente->ci,
                                    $paciente->nombres,
                                    $paciente->apellido_paterno,
                                    $paciente->apellido_materno,
                                ])));

                                // Iniciales para avatar
                                $iniciales = strtoupper(
                                    substr($paciente->nombres, 0, 1) .
                                    substr($paciente->apellido_paterno, 0, 1)
                                );

                                // Color de avatar según sexo
                                $avatarColor = match($paciente->sexo) {
                                    'M'    => 'background:rgba(59,130,246,.12);color:#1d4ed8',
                                    'F'    => 'background:rgba(236,72,153,.12);color:#9d174d',
                                    default=> 'background:rgba(82,171,177,.12);color:#0e7490',
                                };

                                // Badge sexo
                                $sexoBadge = match($paciente->sexo) {
                                    'M'    => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400',
                                    'F'    => 'bg-pink-100 text-pink-700 dark:bg-pink-900/30 dark:text-pink-400',
                                    default=> 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-400',
                                };

                                $totalIngPaciente = $paciente->ingresos_count ?? $paciente->ingresos()->count();
                            @endphp
                            <tr data-pac="{{ $searchData }}"
                                class="hover:bg-gray-50/80 dark:hover:bg-gray-800/40 transition">

                                {{-- Paciente --}}
                                <td class="px-5 py-3.5">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-full flex items-center justify-center
                                                    text-xs font-bold flex-shrink-0"
                                             style="{{ $avatarColor }}">
                                            {{ $iniciales }}
                                        </div>
                                        <div>
                                            <p class="font-semibold text-sm text-gray-900 dark:text-white leading-tight">
                                                {{ $paciente->nombre_completo }}
                                            </p>
                                            <p class="text-xs text-gray-400 dark:text-gray-600 mt-0.5">
                                                {{ $paciente->email ?: 'Sin correo' }}
                                            </p>
                                        </div>
                                    </div>
                                </td>

                                {{-- CI --}}
                                <td class="px-5 py-3.5">
                                    <code class="text-xs px-2 py-1 rounded-md font-mono
                                                 bg-gray-100 dark:bg-gray-800
                                                 text-gray-600 dark:text-gray-400">
                                        {{ $paciente->ci }}
                                    </code>
                                </td>

                                {{-- Sexo / Edad --}}
                                <td class="px-5 py-3.5">
                                    <div class="flex items-center gap-2">
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full
                                                     text-[11px] font-semibold {{ $sexoBadge }}">
                                            <span class="w-1.5 h-1.5 rounded-full bg-current opacity-70"></span>
                                            {{ $paciente->sexo }}
                                        </span>
                                        @if($paciente->edad !== null)
                                            <span class="text-xs text-gray-500 dark:text-gray-400">
                                                {{ $paciente->edad }} años
                                            </span>
                                        @else
                                            <span class="text-xs text-gray-400 dark:text-gray-600">—</span>
                                        @endif
                                    </div>
                                </td>

                                {{-- Teléfono --}}
                                <td class="px-5 py-3.5 text-sm text-gray-600 dark:text-gray-400">
                                    {{ $paciente->telefono ?: '—' }}
                                </td>

                                {{-- Dirección --}}
                                <td class="px-5 py-3.5 text-sm text-gray-500 dark:text-gray-500
                                           max-w-[160px] truncate"
                                    title="{{ $paciente->direccion }}">
                                    {{ $paciente->direccion ?: '—' }}
                                </td>

                                {{-- Ingresos --}}
                                <td class="px-5 py-3.5">
                                    @if($totalIngPaciente > 0)
                                        <span class="text-sm font-bold" style="color:#52ABB1">
                                            {{ $totalIngPaciente }}
                                        </span>
                                    @else
                                        <span class="text-gray-400 dark:text-gray-600">—</span>
                                    @endif
                                </td>

                                {{-- Estado --}}
                                <td class="px-5 py-3.5">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full
                                                 text-[11px] font-semibold uppercase tracking-wide
                                                 {{ $paciente->estado === 'activo'
                                                     ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400'
                                                     : 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400' }}">
                                        <span class="w-1.5 h-1.5 rounded-full bg-current opacity-70"></span>
                                        {{ ucfirst($paciente->estado) }}
                                    </span>
                                </td>

                                {{-- Acciones con tooltips Flowbite --}}
                                <td class="px-5 py-3.5">
                                    <div class="flex items-center justify-end gap-2">

                                        {{-- Ver historial --}}
                                        <a href="{{ route('recepcion.pacientes.show', $paciente) }}"
                                           data-tooltip-target="tt-ver-{{ $paciente->id }}"
                                           class="w-8 h-8 flex items-center justify-center rounded-lg border transition
                                                  border-[#52ABB1]/40 hover:bg-[#52ABB1]/10 dark:hover:bg-[#52ABB1]/20">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="#52ABB1" stroke-width="1.8" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                      d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5
                                                         c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639
                                                         C20.577 16.49 16.64 19.5 12 19.5
                                                         c-4.638 0-8.573-3.007-9.964-7.178z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                      d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            </svg>
                                        </a>
                                        <div id="tt-ver-{{ $paciente->id }}" role="tooltip"
                                             class="absolute z-10 invisible inline-block px-3 py-2 text-xs font-medium
                                                    text-white bg-gray-800 dark:bg-gray-700
                                                    rounded-lg shadow-sm opacity-0 transition-opacity duration-300 tooltip">
                                            Ver historial
                                            <div class="tooltip-arrow" data-popper-arrow></div>
                                        </div>

                                        {{-- Editar --}}
                                        <a href="{{ route('recepcion.pacientes.edit', $paciente) }}"
                                           data-tooltip-target="tt-edit-{{ $paciente->id }}"
                                           class="w-8 h-8 flex items-center justify-center rounded-lg border transition
                                                  border-[#52ABB1]/40 hover:bg-[#52ABB1]/10 dark:hover:bg-[#52ABB1]/20">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="#52ABB1" stroke-width="1.8" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                      d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652
                                                         L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685
                                                         a4.5 4.5 0 011.13-1.897l8.932-8.931z"/>
                                            </svg>
                                        </a>
                                        <div id="tt-edit-{{ $paciente->id }}" role="tooltip"
                                             class="absolute z-10 invisible inline-block px-3 py-2 text-xs font-medium
                                                    text-white bg-gray-800 dark:bg-gray-700
                                                    rounded-lg shadow-sm opacity-0 transition-opacity duration-300 tooltip">
                                            Editar paciente
                                            <div class="tooltip-arrow" data-popper-arrow></div>
                                        </div>

                                        {{-- Nuevo ingreso --}}
                                        <a href="{{ route('recepcion.ingresos.create', ['paciente_id' => $paciente->id]) }}"
                                           data-tooltip-target="tt-ing-{{ $paciente->id }}"
                                           class="w-8 h-8 flex items-center justify-center rounded-lg border transition
                                                  border-[#52ABB1]/40 hover:bg-[#52ABB1]/10 dark:hover:bg-[#52ABB1]/20">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="#52ABB1" stroke-width="1.8" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                                            </svg>
                                        </a>
                                        <div id="tt-ing-{{ $paciente->id }}" role="tooltip"
                                             class="absolute z-10 invisible inline-block px-3 py-2 text-xs font-medium
                                                    text-white bg-gray-800 dark:bg-gray-700
                                                    rounded-lg shadow-sm opacity-0 transition-opacity duration-300 tooltip">
                                            Nuevo ingreso
                                            <div class="tooltip-arrow" data-popper-arrow></div>
                                        </div>

                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-5 py-14 text-center">
                                    <p class="text-base font-semibold text-gray-600 dark:text-gray-300">
                                        No se encontraron pacientes
                                    </p>
                                    <p class="mt-1.5 text-sm text-gray-400 dark:text-gray-600">
                                        Intenta con otro criterio o registra un nuevo paciente.
                                    </p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- ── PAGINADOR Alpine ─────────────────────────────── --}}
            <div class="flex items-center justify-between px-5 py-3.5
                        border-t border-gray-200 dark:border-gray-700">

                {{-- Info --}}
                <span class="text-xs text-gray-400 dark:text-gray-600"
                      x-text="visibleCount === 0
                          ? 'Sin resultados'
                          : 'Mostrando ' + (pageStart + 1) + '–' + Math.min(pageEnd, visibleCount) + ' de ' + visibleCount + ' pacientes'">
                </span>

                {{-- Botones de página --}}
                <div class="flex items-center gap-1">
                    {{-- Anterior --}}
                    <button @click="goPage(page - 1)"
                            :disabled="page === 1"
                            :class="page === 1 ? 'opacity-40 cursor-not-allowed' : 'hover:bg-gray-100 dark:hover:bg-gray-800'"
                            class="w-8 h-8 flex items-center justify-center rounded-lg border
                                   border-gray-200 dark:border-gray-700 transition">
                        <svg class="w-3.5 h-3.5 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/>
                        </svg>
                    </button>

                    {{-- Números con puntos suspensivos --}}
<template x-for="(p, i) in pageButtons" :key="p">
    <span class="flex items-center gap-1">
        {{-- Ellipsis antes --}}
        <span
            x-show="i > 0 && p - pageButtons[i-1] > 1"
            class="w-8 h-8 flex items-center justify-center
                   text-xs text-gray-400 dark:text-gray-600 select-none">
            …
        </span>
        {{-- Botón de página --}}
        <button
            @click="goPage(p)"
            :class="p === page
                ? 'text-white border-[#52ABB1]'
                : 'border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800'"
            :style="p === page ? 'background:#52ABB1' : ''"
            class="w-8 h-8 flex items-center justify-center rounded-lg border
                   text-xs font-semibold transition"
            x-text="p">
        </button>
    </span>
</template>

                    {{-- Siguiente --}}
                    <button @click="goPage(page + 1)"
                            :disabled="page === totalPages"
                            :class="page === totalPages ? 'opacity-40 cursor-not-allowed' : 'hover:bg-gray-100 dark:hover:bg-gray-800'"
                            class="w-8 h-8 flex items-center justify-center rounded-lg border
                                   border-gray-200 dark:border-gray-700 transition">
                        <svg class="w-3.5 h-3.5 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/>
                        </svg>
                    </button>
                </div>
            </div>

        </section>

    </div>
</x-app-layout>