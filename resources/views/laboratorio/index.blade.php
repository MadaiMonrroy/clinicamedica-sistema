<x-app-layout>
@php
    $card       = 'rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 shadow-sm';
    $badge      = 'inline-flex items-center rounded-full px-2.5 py-1 text-[11px] font-bold uppercase tracking-[0.12em]';
    $inputClass = 'block w-full px-4 py-3 rounded-2xl text-sm
                   bg-white dark:bg-gray-800
                   border border-gray-200 dark:border-gray-700
                   text-gray-900 dark:text-white
                   placeholder-gray-400 dark:placeholder-gray-500
                   focus:outline-none focus:ring-2 focus:ring-[#52ABB1]/40 focus:border-[#52ABB1]
                   transition';
@endphp

    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-black uppercase tracking-[0.18em] text-gray-400 dark:text-gray-500">
                    Módulo de laboratorio
                </p>
                <h2 class="mt-1 font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    Cola de atención
                </h2>
            </div>
            {{-- Contadores rápidos --}}
            <div class="flex items-center gap-5">
                <div class="text-center">
                    <p class="text-2xl font-bold text-yellow-500 dark:text-yellow-400">
                        {{ $ticketsPendientes->count() }}
                    </p>
                    <p class="text-[11px] text-gray-400 dark:text-gray-500 uppercase tracking-wide">En espera</p>
                </div>
                <div class="w-px h-8 bg-gray-200 dark:bg-gray-700"></div>
                <div class="text-center">
                    <p class="text-2xl font-bold text-blue-500 dark:text-blue-400">
                        {{ $ticketsEnProceso->count() }}
                    </p>
                    <p class="text-[11px] text-gray-400 dark:text-gray-500 uppercase tracking-wide">Pend. resultado</p>
                </div>
                <div class="w-px h-8 bg-gray-200 dark:bg-gray-700"></div>
                <div class="text-center">
                    <p class="text-2xl font-bold text-green-500 dark:text-green-400">
                        {{ $ticketsCompletados->count() }}
                    </p>
                    <p class="text-[11px] text-gray-400 dark:text-gray-500 uppercase tracking-wide">Completados</p>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            {{-- ── FLASH ── --}}
            @if(session('success'))
            <div class="flex items-center gap-3 px-5 py-4 rounded-2xl bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-800 dark:text-green-300">
                <svg class="w-5 h-5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd"/>
                </svg>
                <span class="text-sm font-medium">{{ session('success') }}</span>
            </div>
            @endif

            @if(session('error'))
            <div class="flex items-center gap-3 px-5 py-4 rounded-2xl bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-800 dark:text-red-300">
                <svg class="w-5 h-5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd"/>
                </svg>
                <span class="text-sm font-medium">{{ session('error') }}</span>
            </div>
            @endif

            {{-- ═══════════════════════════════════════════
                 PANEL 1 — COLA EN ESPERA / EN TURNO
            ══════════════════════════════════════════════ --}}
            <section>
                <div class="flex items-center gap-2 mb-4">
                    <span class="w-2 h-2 rounded-full bg-yellow-400 animate-pulse"></span>
                    <h3 class="text-xs font-black uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">
                        Cola de espera
                    </h3>
                    <span class="{{ $badge }} bg-yellow-100 dark:bg-yellow-900/40 text-yellow-700 dark:text-yellow-300 ml-auto">
                        {{ $ticketsPendientes->count() }} pacientes
                    </span>
                </div>

                @if($ticketsPendientes->isEmpty())
                <div class="{{ $card }} p-10 text-center">
                    <div class="w-14 h-14 rounded-2xl bg-gray-100 dark:bg-gray-800 flex items-center justify-center mx-auto mb-3">
                        <svg class="w-7 h-7 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                    </div>
                    <p class="text-sm font-medium text-gray-400 dark:text-gray-500">No hay pacientes en espera</p>
                </div>
                @else
                <div class="{{ $card }} overflow-hidden">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-gray-100 dark:border-gray-800">
                                <th class="px-5 py-3.5 text-left text-[11px] font-bold uppercase tracking-[0.12em] text-gray-400 dark:text-gray-500">Ticket</th>
                                <th class="px-5 py-3.5 text-left text-[11px] font-bold uppercase tracking-[0.12em] text-gray-400 dark:text-gray-500">Paciente</th>
                                <th class="px-5 py-3.5 text-left text-[11px] font-bold uppercase tracking-[0.12em] text-gray-400 dark:text-gray-500">Exámenes</th>
                                <th class="px-5 py-3.5 text-left text-[11px] font-bold uppercase tracking-[0.12em] text-gray-400 dark:text-gray-500">Ingresó</th>
                                <th class="px-5 py-3.5 text-left text-[11px] font-bold uppercase tracking-[0.12em] text-gray-400 dark:text-gray-500">Estado</th>
                                <th class="px-5 py-3.5"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                            @foreach($ticketsPendientes as $ticket)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors
                                {{ $ticket->estado === 'en_turno' ? 'bg-blue-50/60 dark:bg-blue-900/10' : '' }}">

                                {{-- Ticket --}}
                                <td class="px-5 py-4">
                                    <span class="font-mono font-bold text-sm
                                        {{ $ticket->estado === 'en_turno'
                                            ? 'text-blue-600 dark:text-blue-400'
                                            : 'text-gray-700 dark:text-gray-300' }}">
                                        {{ $ticket->numero_ticket }}
                                    </span>
                                    @if($ticket->prioridad_turno === 'urgente')
                                    <span class="{{ $badge }} bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400 ml-1">
                                        Urgente
                                    </span>
                                    @endif
                                </td>

                                {{-- Paciente --}}
                                <td class="px-5 py-4">
                                    <p class="font-semibold text-gray-900 dark:text-white">
                                        {{ $ticket->paciente->nombres }} {{ $ticket->paciente->apellido_paterno }}
                                    </p>
                                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">CI: {{ $ticket->paciente->ci }}</p>
                                </td>

                                {{-- Exámenes --}}
                                <td class="px-5 py-4">
                                    <div class="flex flex-wrap gap-1 max-w-xs">
                                        @foreach($ticket->ingreso->solicitudesLab->take(3) as $sol)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-[11px] font-medium
                                                     bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-300">
                                            {{ Str::limit($sol->examen->nombre_examen, 18) }}
                                        </span>
                                        @endforeach
                                        @if($ticket->ingreso->solicitudesLab->count() > 3)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-[11px] font-medium
                                                     bg-gray-100 dark:bg-gray-800 text-gray-500 dark:text-gray-400">
                                            +{{ $ticket->ingreso->solicitudesLab->count() - 3 }} más
                                        </span>
                                        @endif
                                    </div>
                                </td>

                                {{-- Hora --}}
                                <td class="px-5 py-4">
                                    <p class="text-sm text-gray-700 dark:text-gray-300">{{ $ticket->created_at->format('H:i') }}</p>
                                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">{{ $ticket->created_at->diffForHumans() }}</p>
                                </td>

                                {{-- Estado --}}
                                <td class="px-5 py-4">
                                    @if($ticket->estado === 'en_turno')
                                    <span class="{{ $badge }} bg-blue-100 dark:bg-blue-900/40 text-blue-700 dark:text-blue-300 gap-1">
                                        <span class="w-1.5 h-1.5 rounded-full bg-blue-500 animate-pulse"></span>
                                        En turno
                                    </span>
                                    @else
                                    <span class="{{ $badge }} bg-yellow-100 dark:bg-yellow-900/40 text-yellow-700 dark:text-yellow-300 gap-1">
                                        <span class="w-1.5 h-1.5 rounded-full bg-yellow-400"></span>
                                        En espera
                                    </span>
                                    @endif
                                </td>

                                {{-- Acción --}}
                                <td class="px-5 py-4 text-right">
                                    <a href="{{ route('laboratorio.show', $ticket) }}"
                                       class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-bold
                                              bg-[#52ABB1] hover:bg-[#3d8f95] text-white transition-colors shadow-sm
                                              shadow-[#52ABB1]/20">
                                        @if($ticket->estado === 'en_turno')
                                            Atender
                                        @else
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                            </svg>
                                            Llamar
                                        @endif
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif
            </section>

            {{-- ═══════════════════════════════════════════
                 PANEL 2 — PENDIENTES DE RESULTADO
            ══════════════════════════════════════════════ --}}
            <section>
                <div class="flex items-center gap-2 mb-4">
                    <span class="w-2 h-2 rounded-full bg-blue-400"></span>
                    <h3 class="text-xs font-black uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">
                        Pendientes de resultado
                    </h3>
                    <span class="{{ $badge }} bg-blue-100 dark:bg-blue-900/40 text-blue-700 dark:text-blue-300 ml-auto">
                        {{ $ticketsEnProceso->count() }}
                    </span>
                </div>

                @if($ticketsEnProceso->isEmpty())
                <div class="{{ $card }} p-8 text-center border-dashed">
                    <p class="text-sm text-gray-400 dark:text-gray-500">Sin resultados pendientes de cargar</p>
                </div>
                @else
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                    @foreach($ticketsEnProceso as $ticket)
                    <div class="{{ $card }} p-5 hover:border-[#52ABB1]/50 dark:hover:border-[#52ABB1]/40 transition-colors">

                        <div class="flex items-start justify-between mb-3">
                            <span class="font-mono font-bold text-[#52ABB1] dark:text-[#6dbec4]">
                                {{ $ticket->numero_ticket }}
                            </span>
                            <span class="{{ $badge }} bg-orange-100 dark:bg-orange-900/30 text-orange-600 dark:text-orange-400 gap-1">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Muestra tomada
                            </span>
                        </div>

                        <p class="font-semibold text-gray-900 dark:text-white text-sm">
                            {{ $ticket->paciente->nombres }} {{ $ticket->paciente->apellido_paterno }}
                        </p>
                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">CI: {{ $ticket->paciente->ci }}</p>

                        <div class="mt-3 flex flex-wrap gap-1">
                            @foreach($ticket->ingreso->solicitudesLab->take(4) as $sol)
                            <span class="px-2 py-0.5 rounded-lg text-[11px] font-medium
                                         bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400">
                                {{ Str::limit($sol->examen->nombre_examen, 20) }}
                            </span>
                            @endforeach
                            @if($ticket->ingreso->solicitudesLab->count() > 4)
                            <span class="px-2 py-0.5 rounded-lg text-[11px] font-medium
                                         bg-gray-100 dark:bg-gray-800 text-gray-500 dark:text-gray-500">
                                +{{ $ticket->ingreso->solicitudesLab->count() - 4 }}
                            </span>
                            @endif
                        </div>

                        @if($ticket->adjuntosLab->count() > 0)
                        <div class="mt-3 flex items-center gap-1.5 text-xs text-green-600 dark:text-green-400 font-medium">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                            </svg>
                            {{ $ticket->adjuntosLab->count() }} archivo(s) subido(s)
                        </div>
                        @endif

                        <div class="mt-4">
                            <a href="{{ route('laboratorio.show-resultados', $ticket) }}"
                               class="block w-full text-center py-2 rounded-xl text-xs font-bold
                                      bg-[#52ABB1] hover:bg-[#3d8f95] text-white transition-colors">
                                Subir resultados
                            </a>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
            </section>

            {{-- ═══════════════════════════════════════════
                 PANEL 3 — COMPLETADOS RECIENTES
            ══════════════════════════════════════════════ --}}
            <section>
                <div class="flex items-center gap-2 mb-4">
                    <span class="w-2 h-2 rounded-full bg-green-400"></span>
                    <h3 class="text-xs font-black uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">
                        Completados recientes
                    </h3>
                </div>

                @if($ticketsCompletados->isEmpty())
                <div class="{{ $card }} p-8 text-center border-dashed">
                    <p class="text-sm text-gray-400 dark:text-gray-500">Sin registros completados</p>
                </div>
                @else
                <div class="{{ $card }} overflow-hidden">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-gray-100 dark:border-gray-800">
                                <th class="px-5 py-3.5 text-left text-[11px] font-bold uppercase tracking-[0.12em] text-gray-400 dark:text-gray-500">Ticket</th>
                                <th class="px-5 py-3.5 text-left text-[11px] font-bold uppercase tracking-[0.12em] text-gray-400 dark:text-gray-500">Paciente</th>
                                <th class="px-5 py-3.5 text-left text-[11px] font-bold uppercase tracking-[0.12em] text-gray-400 dark:text-gray-500">Archivos</th>
                                <th class="px-5 py-3.5 text-left text-[11px] font-bold uppercase tracking-[0.12em] text-gray-400 dark:text-gray-500">Cerrado</th>
                                <th class="px-5 py-3.5"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                            @foreach($ticketsCompletados as $ticket)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                                <td class="px-5 py-3.5">
                                    <span class="font-mono font-bold text-sm text-green-600 dark:text-green-400">
                                        {{ $ticket->numero_ticket }}
                                    </span>
                                </td>
                                <td class="px-5 py-3.5 font-medium text-gray-800 dark:text-gray-200">
                                    {{ $ticket->paciente->nombres }} {{ $ticket->paciente->apellido_paterno }}
                                </td>
                                <td class="px-5 py-3.5 text-gray-500 dark:text-gray-400">
                                    {{ $ticket->adjuntosLab->count() }} archivo(s)
                                </td>
                                <td class="px-5 py-3.5 text-xs text-gray-400 dark:text-gray-500">
                                    {{ optional($ticket->finalizado_en)->format('d/m H:i') ?? '—' }}
                                </td>
                                <td class="px-5 py-3.5 text-right">
                                    <a href="{{ route('laboratorio.show-resultados', $ticket) }}"
                                       class="text-xs font-semibold text-[#52ABB1] hover:text-[#3d8f95] dark:text-[#6dbec4] hover:underline transition-colors">
                                        Ver →
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif
            </section>

        </div>
    </div>
</x-app-layout>