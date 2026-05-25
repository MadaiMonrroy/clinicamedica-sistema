<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    Gestión de áreas
                </h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    Administra las áreas clínicas del sistema
                </p>
            </div>
            {{-- Botón abre modal en modo CREAR --}}
            <button type="button"
                    onclick="openAreaModalCreate()"
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold text-white
                           transition-all hover:opacity-90 active:scale-[.98]"
                    style="background:#52ABB1;">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                </svg>
                Nueva área
            </button>
        </div>
    </x-slot>

    <div class="py-8">
        <div class=" mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Alertas flash --}}
            @if(session('success'))
                <div class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium
                            bg-emerald-50 text-emerald-800 border border-emerald-200
                            dark:bg-emerald-900/20 dark:text-emerald-300 dark:border-emerald-800">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium
                            bg-red-50 text-red-800 border border-red-200
                            dark:bg-red-900/20 dark:text-red-300 dark:border-red-800">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126z"/>
                    </svg>
                    {{ session('error') }}
                </div>
            @endif

            {{-- Stats --}}
            <div class="grid grid-cols-3 gap-4">
                <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-700 p-5">
                    <p class="text-xs uppercase tracking-widest text-gray-400 dark:text-gray-500">Total áreas</p>
                    <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">{{ $stats['total'] }}</p>
                </div>
                <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-700 p-5">
                    <p class="text-xs uppercase tracking-widest text-gray-400 dark:text-gray-500">Activas</p>
                    <p class="mt-2 text-3xl font-bold text-emerald-600 dark:text-emerald-400">{{ $stats['activas'] }}</p>
                </div>
                <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-700 p-5">
                    <p class="text-xs uppercase tracking-widest text-gray-400 dark:text-gray-500">Inactivas</p>
                    <p class="mt-2 text-3xl font-bold text-amber-600 dark:text-amber-400">{{ $stats['inactivas'] }}</p>
                </div>
            </div>

            {{-- Tabla --}}
            <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-700 overflow-hidden"
                 x-data="{ search: '' }">

                <div class="flex items-center gap-3 px-5 py-4 border-b border-gray-200 dark:border-gray-700">
                    <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/>
                    </svg>
                    <input type="text" x-model="search"
                           placeholder="Buscar por nombre, código o tipo…"
                           class="flex-1 bg-transparent border-none outline-none text-sm
                                  text-gray-700 dark:text-gray-300
                                  placeholder-gray-400 dark:placeholder-gray-600"/>
                    <span x-show="search.length > 0" @click="search = ''"
                          class="text-xs text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 cursor-pointer select-none">
                        Limpiar
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead class="bg-gray-50 dark:bg-gray-800/50">
                            <tr>
                                <th class="px-5 py-3 text-left text-xs font-black uppercase tracking-widest text-gray-400 dark:text-gray-500 w-12">#</th>
                                <th class="px-5 py-3 text-left text-xs font-black uppercase tracking-widest text-gray-400 dark:text-gray-500">Nombre</th>
                                <th class="px-5 py-3 text-left text-xs font-black uppercase tracking-widest text-gray-400 dark:text-gray-500">Código</th>
                                <th class="px-5 py-3 text-left text-xs font-black uppercase tracking-widest text-gray-400 dark:text-gray-500">Tipo</th>
                                <th class="px-5 py-3 text-left text-xs font-black uppercase tracking-widest text-gray-400 dark:text-gray-500">Estado</th>
                                <th class="px-5 py-3 text-left text-xs font-black uppercase tracking-widest text-gray-400 dark:text-gray-500">Tickets totales</th>
                                <th class="px-5 py-3 text-left text-xs font-black uppercase tracking-widest text-gray-400 dark:text-gray-500">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                            @forelse($areas as $area)
                                <tr
                                    x-show="
                                        search.length === 0 ||
                                        '{{ strtolower($area->nombre) }}'.includes(search.toLowerCase()) ||
                                        '{{ strtolower($area->codigo) }}'.includes(search.toLowerCase()) ||
                                        '{{ strtolower($area->tipo) }}'.includes(search.toLowerCase())
                                    "
                                    class="hover:bg-gray-50/80 dark:hover:bg-gray-800/40 transition"
                                >
                                    <td class="px-5 py-4 text-sm text-gray-400 dark:text-gray-600">{{ $loop->iteration }}</td>


                                    <td class="px-5 py-4">
                                        <span class="font-semibold text-sm text-gray-900 dark:text-white">
                                            {{ $area->nombre }}
                                        </span>
                                    </td>

                                    <td class="px-5 py-4">
                                        <code class="text-xs px-2 py-1 rounded-md font-mono
                                                     bg-gray-100 dark:bg-gray-800
                                                     text-gray-600 dark:text-gray-400">
                                            {{ $area->codigo }}
                                        </code>
                                    </td>

                                    <td class="px-5 py-4">
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full
                                                     text-[11px] font-semibold capitalize
                                                     bg-gray-100 dark:bg-gray-800
                                                     text-gray-700 dark:text-gray-300">
                                            <span class="w-1.5 h-1.5 rounded-full"
                                                  style="background:#52ABB1;opacity:.8;"></span>
                                            {{ $area->tipo }}
                                        </span>
                                    </td>

                                    <td class="px-5 py-4">
                                        <form method="POST" action="{{ route('admin.areas.toggle', $area) }}">
                                            @csrf @method('PATCH')
                                            <button type="submit"
                                                class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full
                                                       text-[11px] font-semibold uppercase tracking-wide
                                                       transition hover:opacity-75 cursor-pointer
                                                       {{ $area->estado === 'activo'
                                                           ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400'
                                                           : 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400' }}"
                                                title="Clic para cambiar estado">
                                                <span class="w-1.5 h-1.5 rounded-full bg-current opacity-70"></span>
                                                {{ $area->estado === 'activo' ? 'Activo' : 'Inactivo' }}
                                            </button>
                                        </form>
                                    </td>

                                    <td class="px-5 py-4 text-sm text-gray-700 dark:text-gray-300">
                                        @php $t = $area->totalTickets() @endphp
@if($t > 0)
    <span class="font-bold" style="color:#52ABB1">{{ $t }}</span>
@else
    <span class="text-gray-400 dark:text-gray-600">—</span>
@endif
                                    </td>

                                    <td class="px-5 py-4">
    <div class="flex items-center gap-2">

        {{-- ── EDITAR ── --}}
        <button
            type="button"
            data-tooltip-target="tooltip-editar-{{ $area->id }}"
            onclick="openAreaModalEdit({
                id: {{ $area->id }},
                nombre: @js($area->nombre),
                codigo: @js($area->codigo),
                tipo: @js($area->tipo),
                estado: @js($area->estado),
                tiene_dependencias: {{ $area->tieneDependencias() ? 'true' : 'false' }}
            })"
            class="w-8 h-8 flex items-center justify-center rounded-lg border transition
                   border-[#52ABB1]/40
                   hover:bg-[#52ABB1]/15 dark:hover:bg-[#52ABB1]/20">
            <svg class="w-3.5 h-3.5" fill="none" stroke="#52ABB1" stroke-width="1.8" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652
                         L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685
                         a4.5 4.5 0 011.13-1.897l8.932-8.931z"/>
            </svg>
        </button>
        {{-- Tooltip EDITAR --}}
<div id="tooltip-editar-{{ $area->id }}"
     role="tooltip"
     class="absolute z-10 invisible inline-block px-3 py-2 text-xs font-medium
            text-white bg-gray-800 dark:bg-gray-700
            transition-opacity duration-300 rounded-lg shadow-sm opacity-0 tooltip">
    Editar área
    <div class="tooltip-arrow" data-popper-arrow></div>
</div>

        {{-- ── DESACTIVAR ── --}}
        <div x-data="{ open: false }">

            <button
                type="button"
                data-tooltip-target="tooltip-desactivar-{{ $area->id }}"
                @click="open = true"
                class="w-8 h-8 flex items-center justify-center rounded-lg border transition
                       border-red-300 dark:border-red-800/60
                       bg-red-50 dark:bg-red-900/10
                       hover:bg-red-100 dark:hover:bg-red-900/25">
                <svg class="w-3.5 h-3.5" fill="none" stroke="#ef4444" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M14.74 9l-.346 9m-4.788 0L9.26 9
                             m9.968-3.21c.342.052.682.107 1.022.166
                             m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077
                             H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79
                             m14.456 0a48.108 48.108 0 00-3.478-.397
                             m-12 .562c.34-.059.68-.114 1.022-.165
                             m0 0a48.11 48.11 0 013.478-.397
                             m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201
                             a51.964 51.964 0 00-3.32 0
                             c-1.18.037-2.09 1.022-2.09 2.201v.916
                             m7.5 0a48.667 48.667 0 00-7.5 0"/>
                </svg>
            </button>
            {{-- Tooltip DESACTIVAR --}}
<div id="tooltip-desactivar-{{ $area->id }}"
     role="tooltip"
     class="absolute z-10 invisible inline-block px-3 py-2 text-xs font-medium
            text-white bg-gray-800 dark:bg-gray-700
            transition-opacity duration-300 rounded-lg shadow-sm opacity-0 tooltip">
    Desactivar área
    <div class="tooltip-arrow" data-popper-arrow></div>
</div>
            {{-- Modal de confirmación --}}
            <div x-show="open"
                 x-cloak
                 class="fixed inset-0 z-50 flex items-center justify-center p-4"
                 style="background:rgba(0,0,0,0.5);">
                <div @click.outside="open = false"
                     class="w-full max-w-sm rounded-2xl bg-white dark:bg-gray-900
                            border border-gray-200 dark:border-gray-700
                            shadow-2xl p-6">

                    <div class="w-12 h-12 rounded-full flex items-center justify-center mx-auto mb-4
                                bg-red-100 dark:bg-red-900/30">
                        <svg class="w-6 h-6" fill="none" stroke="#ef4444" stroke-width="1.8" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374
                                     h14.71c1.73 0 2.813-1.874 1.948-3.374
                                     L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126z"/>
                        </svg>
                    </div>

                    <h3 class="text-center text-base font-bold text-gray-900 dark:text-white mb-2">
                        ¿Desactivar esta área?
                    </h3>
                    <p class="text-center text-sm text-gray-500 dark:text-gray-400 mb-6">
                        El área <strong class="text-gray-700 dark:text-gray-200">{{ $area->nombre }}</strong>
                        quedará inactiva. Podrás reactivarla cuando quieras.
                    </p>

                    <div class="flex gap-3">
                        <button type="button"
                                @click="open = false"
                                class="flex-1 py-2.5 rounded-xl text-sm font-medium
                                       border border-gray-200 dark:border-gray-700
                                       text-gray-600 dark:text-gray-400
                                       hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                            Cancelar
                        </button>
                        <form method="POST"
                              action="{{ route('admin.areas.destroy', $area) }}"
                              class="flex-1">
                            @csrf @method('DELETE')
                            <button type="submit"
                                    class="w-full py-2.5 rounded-xl text-sm font-bold text-white
                                           bg-red-500 hover:bg-red-600 transition">
                                Sí, desactivar
                            </button>
                        </form>
                    </div>

                </div>
            </div>

        </div>{{-- /x-data desactivar --}}

    </div>
</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-5 py-12 text-center text-sm text-gray-400 dark:text-gray-600">
                                        No hay áreas registradas todavía.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

    {{-- Modal incluido al final del body --}}
    @include('admin.areas.modal')

</x-app-layout>