<x-app-layout>
    @php
        $pageWrap = 'space-y-6';
        $card     = 'rounded-[2rem] border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 shadow-sm';
        $badge    = 'inline-flex items-center rounded-full px-2.5 py-1 text-[11px] font-bold uppercase tracking-[0.12em]';
        $inputClass = 'block w-full px-4 py-2.5 bg-white dark:bg-gray-800/40 border border-gray-200 dark:border-gray-700 focus:border-[#44B0B3] focus:ring-1 focus:ring-[#44B0B3] rounded-2xl text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 transition-all text-sm shadow-sm';
        $primaryBtn = 'inline-flex items-center justify-center rounded-2xl bg-[#44B0B3] hover:bg-[#389a9d] text-white font-bold px-5 py-2.5 shadow-lg shadow-[#44B0B3]/25 transition text-sm';
        $dropdownBtn = 'inline-flex w-full items-center justify-between px-4 py-2.5 text-sm font-medium rounded-2xl text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800/40 border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-1 focus:ring-[#44B0B3] focus:border-[#44B0B3] transition shadow-sm';
        $dropdownPanel = 'absolute z-50 mt-2 w-full rounded-2xl overflow-hidden border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-xl';
        $dropdownItem = 'block w-full px-4 py-2.5 text-left text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition';
        $dropdownItemActive = 'block w-full px-4 py-2.5 text-left text-sm font-semibold text-[#44B0B3] bg-[#44B0B3]/10 hover:bg-[#44B0B3]/20 transition';
    @endphp

    <div class="mx-auto {{ $pageWrap }}">

        {{-- CABECERA --}}
        <section class="{{ $card }} p-5 sm:p-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <p class="text-xs font-black uppercase tracking-[0.18em] text-gray-400 dark:text-gray-500">
                        Admin / Catalogo
                    </p>
                    <h1 class="mt-1 text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white">
                        Examenes de laboratorio
                    </h1>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Catalogo de examenes disponibles para solicitudes e ingresos directos.
                    </p>
                </div>
                <a href="{{ route('admin.examenes.create') }}" class="{{ $primaryBtn }}">
                    + Nuevo examen
                </a>
            </div>

            {{-- Metricas --}}
            <div class="mt-5 grid grid-cols-3 gap-3">
                <div class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50 px-4 py-3">
                    <p class="text-[10px] uppercase tracking-[0.14em] text-gray-400 dark:text-gray-500">Total</p>
                    <p class="mt-1 text-2xl font-bold text-gray-900 dark:text-white">{{ $examenes->total() }}</p>
                </div>
                <div class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50 px-4 py-3">
                    <p class="text-[10px] uppercase tracking-[0.14em] text-gray-400 dark:text-gray-500">Activos</p>
                    <p class="mt-1 text-2xl font-bold text-emerald-500">{{ $totalActivos }}</p>
                </div>
                <div class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50 px-4 py-3">
                    <p class="text-[10px] uppercase tracking-[0.14em] text-gray-400 dark:text-gray-500">Categorias</p>
                    <p class="mt-1 text-2xl font-bold text-[#44B0B3]">{{ $categorias->count() }}</p>
                </div>
            </div>
        </section>

        {{-- Mensajes --}}
        @if(session('success'))
            <div class="rounded-2xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700 dark:border-green-800 dark:bg-green-900/20 dark:text-green-300">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700 dark:border-red-800 dark:bg-red-900/20 dark:text-red-300">
                {{ session('error') }}
            </div>
        @endif

        {{-- FILTROS --}}
        <section class="{{ $card }} p-4 sm:p-5">
            <div class="flex flex-col sm:flex-row gap-3" x-data="filtrosComponent()">

                {{-- Buscador --}}
                <div class="relative flex-1">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <input
                        type="text"
                        id="buscar-input"
                        x-model="buscar"
                        @input="onBuscarInput()"
                        value="{{ request('buscar') }}"
                        placeholder="Buscar por nombre o codigo..."
                        class="{{ $inputClass }} pl-9"
                    >
                </div>

                {{-- Filtro Categoria — dropdown custom --}}
                <div class="relative sm:w-52" x-data="{ open: false }" @click.outside="open = false">
                    {{-- Hidden para enviar el valor real --}}
                    <input type="hidden" id="categoria-input" x-model="categoriaId">

                    <button
                        type="button"
                        @click="open = !open"
                        class="{{ $dropdownBtn }}"
                    >
                        <span x-text="categoriaLabel" class="truncate"></span>
                        <svg class="fill-current h-4 w-4 text-gray-400 ml-2 shrink-0 transition-transform duration-200"
                             :class="{ 'rotate-180': open }"
                             viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                        </svg>
                    </button>

                    <div x-show="open" x-cloak x-transition class="{{ $dropdownPanel }} max-h-64 overflow-y-auto custom-scroll">
                        <button type="button"
                                @click="setCategoria('', 'Todas las categorias'); open = false"
                                :class="categoriaId === '' ? '{{ $dropdownItemActive }}' : '{{ $dropdownItem }}'">
                            Todas las categorias
                        </button>
                        @foreach($categorias as $cat)
                            <button type="button"
                                    @click="setCategoria('{{ $cat->id }}', '{{ addslashes($cat->nombre) }}'); open = false"
                                    :class="categoriaId === '{{ $cat->id }}' ? '{{ $dropdownItemActive }}' : '{{ $dropdownItem }}'">
                                {{ $cat->nombre }}
                            </button>
                        @endforeach
                    </div>
                </div>

                {{-- Filtro Estado — dropdown custom --}}
                <div class="relative sm:w-36" x-data="{ open: false }" @click.outside="open = false">
                    <input type="hidden" id="estado-input" x-model="estadoVal">

                    <button
                        type="button"
                        @click="open = !open"
                        class="{{ $dropdownBtn }}"
                    >
                        <span x-text="estadoLabel"></span>
                        <svg class="fill-current h-4 w-4 text-gray-400 ml-2 shrink-0 transition-transform duration-200"
                             :class="{ 'rotate-180': open }"
                             viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                        </svg>
                    </button>

                    <div x-show="open" x-cloak x-transition class="{{ $dropdownPanel }}">
                        <button type="button"
                                @click="setEstado('', 'Todos'); open = false"
                                :class="estadoVal === '' ? '{{ $dropdownItemActive }}' : '{{ $dropdownItem }}'">
                            Todos
                        </button>
                        <button type="button"
                                @click="setEstado('activo', 'Activo'); open = false"
                                :class="estadoVal === 'activo' ? '{{ $dropdownItemActive }}' : '{{ $dropdownItem }}'">
                            <span class="inline-flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                                Activo
                            </span>
                        </button>
                        <button type="button"
                                @click="setEstado('inactivo', 'Inactivo'); open = false"
                                :class="estadoVal === 'inactivo' ? '{{ $dropdownItemActive }}' : '{{ $dropdownItem }}'">
                            <span class="inline-flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-gray-400"></span>
                                Inactivo
                            </span>
                        </button>
                    </div>
                </div>

                {{-- Limpiar --}}
                <button type="button"
                        id="btn-limpiar"
                        @click="limpiar()"
                        class="inline-flex items-center justify-center rounded-2xl border border-gray-200 dark:border-gray-700 text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800 px-4 py-2.5 text-sm transition"
                        :class="hayFiltros ? '' : 'opacity-40 pointer-events-none'">
                    Limpiar
                </button>

            </div>
        </section>

        {{-- TABLA --}}
        <section class="{{ $card }} overflow-hidden">
            <div id="tabla-wrapper">
                @include('admin.examenes.tabla', ['examenes' => $examenes])
            </div>
        </section>

    </div>

    {{-- MODAL TOGGLE ESTADO --}}
    <div id="toggleModalBackdrop" class="fixed inset-0 bg-black/50 z-40 hidden"></div>
    <div id="toggleModal" class="fixed inset-0 z-50 hidden items-center justify-center p-4">
        <div class="w-full max-w-md rounded-[2rem] bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 shadow-2xl overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-base font-bold text-gray-800 dark:text-white">Cambiar estado del examen</h3>
                <p id="toggleModalMessage" class="mt-1 text-sm text-gray-500 dark:text-gray-400"></p>
            </div>
            <form id="toggleStatusForm" method="POST" class="px-6 py-4">
                @csrf @method('PATCH')
                <div class="flex items-center justify-end gap-3">
                    <button type="button" onclick="closeToggleModal()"
                            class="px-4 py-2.5 rounded-2xl border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-800 transition text-sm">
                        Cancelar
                    </button>
                    <button type="submit"
                            class="px-4 py-2.5 rounded-2xl bg-[#44B0B3] hover:bg-[#389a9d] text-white font-bold shadow-lg shadow-[#44B0B3]/25 transition text-sm">
                        Confirmar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // ── TOGGLE MODAL ──
        function openToggleModal(id, nombre, activo) {
            const modal    = document.getElementById('toggleModal');
            const backdrop = document.getElementById('toggleModalBackdrop');
            const form     = document.getElementById('toggleStatusForm');
            const msg      = document.getElementById('toggleModalMessage');

            form.action = `/admin/examenes/${id}/toggle`;
            msg.textContent = activo
                ? `¿Deseas desactivar "${nombre}"?`
                : `¿Deseas activar "${nombre}"?`;

            modal.classList.remove('hidden');
            modal.classList.add('flex');
            backdrop.classList.remove('hidden');
        }

        function closeToggleModal() {
            document.getElementById('toggleModal').classList.add('hidden');
            document.getElementById('toggleModal').classList.remove('flex');
            document.getElementById('toggleModalBackdrop').classList.add('hidden');
        }

        document.getElementById('toggleModalBackdrop')
            .addEventListener('click', closeToggleModal);

        // ── FILTROS ALPINE COMPONENT ──
        function filtrosComponent() {
            return {
                buscar:        '{{ request('buscar') }}',
                categoriaId:   '{{ request('categoria_id') }}',
                categoriaLabel:'{{ request('categoria_id') ? $categorias->firstWhere('id', request('categoria_id'))?->nombre ?? 'Todas las categorias' : 'Todas las categorias' }}',
                estadoVal:     '{{ request('estado') }}',
                estadoLabel:   '{{ request('estado') === 'activo' ? 'Activo' : (request('estado') === 'inactivo' ? 'Inactivo' : 'Todos') }}',
                searchTimeout: null,

                get hayFiltros() {
                    return this.buscar || this.categoriaId || this.estadoVal;
                },

                onBuscarInput() {
                    clearTimeout(this.searchTimeout);
                    this.searchTimeout = setTimeout(() => this.runFilters(), 300);
                },

                setCategoria(id, label) {
                    this.categoriaId    = id;
                    this.categoriaLabel = label;
                    this.runFilters();
                },

                setEstado(val, label) {
                    this.estadoVal    = val;
                    this.estadoLabel  = label;
                    this.runFilters();
                },

                limpiar() {
                    this.buscar        = '';
                    this.categoriaId   = '';
                    this.categoriaLabel= 'Todas las categorias';
                    this.estadoVal     = '';
                    this.estadoLabel   = 'Todos';
                    this.runFilters();
                },

                async runFilters(url = null) {
                    const base   = url ?? '{{ route('admin.examenes.index') }}';
                    const urlObj = new URL(base, window.location.origin);

                    // Siempre resetear page si cambió un filtro (no paginación)
                    if (!url) urlObj.searchParams.delete('page');

                    if (this.buscar)      urlObj.searchParams.set('buscar', this.buscar);
                    else                  urlObj.searchParams.delete('buscar');

                    if (this.categoriaId) urlObj.searchParams.set('categoria_id', this.categoriaId);
                    else                  urlObj.searchParams.delete('categoria_id');

                    if (this.estadoVal)   urlObj.searchParams.set('estado', this.estadoVal);
                    else                  urlObj.searchParams.delete('estado');

                    const finalUrl = urlObj.toString();

                    try {
                        const res  = await fetch(finalUrl, {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json',
                            }
                        });
                        const data = await res.json();
                        document.getElementById('tabla-wrapper').innerHTML = data.tabla;
                        window.history.replaceState({}, '', finalUrl);
                    } catch (e) {
                        console.error('Error al filtrar:', e);
                    }
                },
            }
        }

        // runFilters global para que la paginación del _tabla lo llame
        // necesita leer los valores del componente Alpine
        function runFilters(url) {
            // Buscar el componente Alpine del filtro y llamar su método
            const el = document.querySelector('[x-data="filtrosComponent()"]');
            if (el && el._x_dataStack) {
                el._x_dataStack[0].runFilters(url);
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