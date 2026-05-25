@php
    $badge = 'inline-flex items-center rounded-full px-2.5 py-1 text-[11px] font-bold uppercase tracking-[0.12em]';
@endphp

<div class="overflow-x-auto">
    <table class="min-w-full">
        <thead class="bg-gray-50 dark:bg-gray-800/50">
            <tr>
                <th class="px-5 py-3 text-left text-[11px] font-black uppercase tracking-[0.14em] text-gray-400 dark:text-gray-500">Examen</th>
                <th class="px-5 py-3 text-left text-[11px] font-black uppercase tracking-[0.14em] text-gray-400 dark:text-gray-500">Codigo</th>
                <th class="px-5 py-3 text-left text-[11px] font-black uppercase tracking-[0.14em] text-gray-400 dark:text-gray-500">Categoria</th>
                <th class="px-5 py-3 text-center text-[11px] font-black uppercase tracking-[0.14em] text-gray-400 dark:text-gray-500">Estado</th>
                <th class="px-5 py-3 text-right text-[11px] font-black uppercase tracking-[0.14em] text-gray-400 dark:text-gray-500">Acciones</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
            @forelse($examenes as $examen)
                <tr class="hover:bg-gray-50/80 dark:hover:bg-gray-800/40 transition">

                    {{-- Nombre --}}
                    <td class="px-5 py-3">
                        <div class="font-semibold text-sm text-gray-900 dark:text-white leading-tight">
                            {{ $examen->nombre_examen }}
                        </div>
                        @if($examen->descripcion)
                            <div class="text-xs text-gray-400 dark:text-gray-500 mt-0.5 max-w-xs truncate">
                                {{ $examen->descripcion }}
                            </div>
                        @endif
                    </td>

                    {{-- Codigo --}}
                    <td class="px-5 py-3">
                        <span class="text-xs font-mono text-gray-500 dark:text-gray-400">
                            {{ $examen->cod_examen ?? '—' }}
                        </span>
                    </td>

                    {{-- Categoria — usando categoriaExamen (sin conflicto) --}}
                    <td class="px-5 py-3">
                        @if($examen->categoriaExamen)
                            <span class="{{ $badge }} bg-[#44B0B3]/10 text-[#44B0B3]">
                                {{ $examen->categoriaExamen->nombre }}
                            </span>
                        @else
                            <span class="text-xs text-gray-400">—</span>
                        @endif
                    </td>

                    {{-- Estado toggle --}}
                    <td class="px-5 py-3 text-center">
                        <button
                            type="button"
                            onclick="openToggleModal(
                                {{ $examen->id }},
                                '{{ addslashes($examen->nombre_examen) }}',
                                {{ $examen->estado === 'activo' ? 'true' : 'false' }}
                            )"
                            class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors focus:outline-none
                                   {{ $examen->estado === 'activo' ? 'bg-[#44B0B3]' : 'bg-gray-300 dark:bg-gray-600' }}"
                            title="{{ $examen->estado === 'activo' ? 'Desactivar' : 'Activar' }}"
                        >
                            <span class="inline-block h-4 w-4 transform rounded-full bg-white shadow transition-transform
                                         {{ $examen->estado === 'activo' ? 'translate-x-6' : 'translate-x-1' }}">
                            </span>
                        </button>
                    </td>

                    {{-- Acciones --}}
                    <td class="px-5 py-3">
                        <div class="flex justify-end">
                            <a href="{{ route('admin.examenes.edit', $examen) }}"
                               class="inline-flex items-center gap-1.5 rounded-xl px-3 py-1.5 text-xs font-semibold text-[#44B0B3] hover:bg-[#44B0B3]/10 transition">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M14 3l7 7M8 16l-1 4 4-1 9-9-3-3-9 9z"/>
                                </svg>
                                Editar
                            </a>
                        </div>
                    </td>

                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-5 py-10 text-center">
                        <p class="text-sm font-semibold text-gray-600 dark:text-gray-300">No se encontraron examenes</p>
                        <p class="mt-1 text-xs text-gray-400 dark:text-gray-500">Intenta con otros filtros o crea uno nuevo.</p>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- PAGINACION --}}
<div class="px-5 py-4 border-t border-gray-200 dark:border-gray-700 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
    <p class="text-sm text-gray-500 dark:text-gray-400">
        Mostrando {{ $examenes->firstItem() ?? 0 }}–{{ $examenes->lastItem() ?? 0 }}
        de {{ $examenes->total() }} examenes
    </p>

    @if($examenes->lastPage() > 1)
        @php
            $current = $examenes->currentPage();
            $last    = $examenes->lastPage();
            $pages   = [];
            for ($p = 1; $p <= $last; $p++) {
                if ($p === 1 || $p === $last || abs($p - $current) <= 2) {
                    $pages[] = $p;
                }
            }
            $pages = array_unique($pages);
            sort($pages);
        @endphp

        <nav class="inline-flex items-center rounded-2xl overflow-hidden border border-gray-200 dark:border-gray-700 shadow-sm">

            {{-- Anterior --}}
            @if($examenes->onFirstPage())
                <span class="px-3 py-2 text-sm text-gray-300 dark:text-gray-600 bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700 cursor-not-allowed">‹</span>
            @else
                <a href="#"
                   onclick="event.preventDefault(); runFilters('{{ $examenes->previousPageUrl() }}')"
                   class="px-3 py-2 text-sm text-gray-600 dark:text-gray-300 bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 transition">‹</a>
            @endif

            {{-- Numeros --}}
            @php $prev = null; @endphp
            @foreach($pages as $page)
                @if($prev !== null && $page - $prev > 1)
                    <span class="px-2 py-2 text-sm text-gray-400 bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700">…</span>
                @endif

                @if($page === $current)
                    <span class="px-3 py-2 text-sm font-semibold text-white bg-[#44B0B3] border-r border-gray-200 dark:border-gray-700">{{ $page }}</span>
                @else
                    <a href="#"
                       onclick="event.preventDefault(); runFilters('{{ $examenes->url($page) }}')"
                       class="px-3 py-2 text-sm text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 transition">{{ $page }}</a>
                @endif

                @php $prev = $page; @endphp
            @endforeach

            {{-- Siguiente --}}
            @if($examenes->hasMorePages())
                <a href="#"
                   onclick="event.preventDefault(); runFilters('{{ $examenes->nextPageUrl() }}')"
                   class="px-3 py-2 text-sm text-gray-600 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 transition">›</a>
            @else
                <span class="px-3 py-2 text-sm text-gray-300 dark:text-gray-600 bg-white dark:bg-gray-800 cursor-not-allowed">›</span>
            @endif

        </nav>
    @endif
</div>