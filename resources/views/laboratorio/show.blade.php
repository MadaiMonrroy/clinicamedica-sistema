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
        <div class="flex items-center gap-3">
            <a href="{{ route('laboratorio.index') }}"
               class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/>
                </svg>
            </a>
            <div class="flex-1">
                <div class="flex items-center gap-2">
                    <span class="font-mono font-bold text-[#52ABB1] dark:text-[#6dbec4] text-lg">
                        {{ $ticket->numero_ticket }}
                    </span>
                    <span class="h-4 w-px bg-gray-200 dark:bg-gray-700"></span>
                    <span class="font-semibold text-gray-800 dark:text-gray-200">
                        {{ $ticket->paciente->nombres }} {{ $ticket->paciente->apellido_paterno }}
                        {{ $ticket->paciente->apellido_materno }}
                    </span>
                </div>
                <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">
                    CI: {{ $ticket->paciente->ci }} · Ingresó {{ $ticket->created_at->format('d/m/Y H:i') }}
                </p>
            </div>
            {{-- Badge estado --}}
            @if($ticket->estado === 'en_turno')
            <span class="{{ $badge }} bg-blue-100 dark:bg-blue-900/40 text-blue-700 dark:text-blue-300 gap-1.5">
                <span class="w-1.5 h-1.5 rounded-full bg-blue-500 animate-pulse"></span>
                En turno
            </span>
            @elseif($ticket->estado === 'en_espera')
            <span class="{{ $badge }} bg-yellow-100 dark:bg-yellow-900/40 text-yellow-700 dark:text-yellow-300 gap-1.5">
                <span class="w-1.5 h-1.5 rounded-full bg-yellow-400"></span>
                En espera
            </span>
            @else
            <span class="{{ $badge }} bg-green-100 dark:bg-green-900/40 text-green-700 dark:text-green-300 gap-1.5">
                <span class="w-1.5 h-1.5 rounded-full bg-green-400"></span>
                Atendido
            </span>
            @endif
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-5">

            {{-- ── FLASH ── --}}
            @if(session('success'))
            <div class="flex items-center gap-3 px-5 py-4 rounded-2xl bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-800 dark:text-green-300">
                <svg class="w-5 h-5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd"/>
                </svg>
                <span class="text-sm font-medium">{{ session('success') }}</span>
            </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

                {{-- ── COL IZQUIERDA: Datos paciente + exámenes ── --}}
                <div class="space-y-4">

                    {{-- Datos del paciente --}}
                    <div class="{{ $card }} p-5">
                        <p class="text-xs font-black uppercase tracking-[0.18em] text-gray-400 dark:text-gray-500 mb-3">
                            Datos del paciente
                        </p>
                        <dl class="space-y-3">
                            <div>
                                <dt class="text-[11px] text-gray-400 dark:text-gray-500 uppercase tracking-wide">Nombre completo</dt>
                                <dd class="font-semibold text-gray-900 dark:text-white mt-0.5">
                                    {{ $ticket->paciente->nombres }}
                                    {{ $ticket->paciente->apellido_paterno }}
                                    {{ $ticket->paciente->apellido_materno }}
                                </dd>
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <dt class="text-[11px] text-gray-400 dark:text-gray-500 uppercase tracking-wide">CI</dt>
                                    <dd class="font-medium text-gray-800 dark:text-gray-200 mt-0.5">{{ $ticket->paciente->ci }}</dd>
                                </div>
                                <div>
                                    <dt class="text-[11px] text-gray-400 dark:text-gray-500 uppercase tracking-wide">Sexo</dt>
                                    <dd class="font-medium text-gray-800 dark:text-gray-200 mt-0.5">{{ $ticket->paciente->sexo }}</dd>
                                </div>
                            </div>
                            <div>
                                <dt class="text-[11px] text-gray-400 dark:text-gray-500 uppercase tracking-wide">Fecha de nacimiento</dt>
                                <dd class="font-medium text-gray-800 dark:text-gray-200 mt-0.5">
                                    {{ \Carbon\Carbon::parse($ticket->paciente->fecha_nacimiento)->format('d/m/Y') }}
                                    <span class="text-xs text-gray-400 dark:text-gray-500">
                                        ({{ \Carbon\Carbon::parse($ticket->paciente->fecha_nacimiento)->age }} años)
                                    </span>
                                </dd>
                            </div>
                            @if($ticket->paciente->telefono)
                            <div>
                                <dt class="text-[11px] text-gray-400 dark:text-gray-500 uppercase tracking-wide">Teléfono</dt>
                                <dd class="font-medium text-gray-800 dark:text-gray-200 mt-0.5">{{ $ticket->paciente->telefono }}</dd>
                            </div>
                            @endif
                        </dl>
                    </div>

                    {{-- Exámenes solicitados --}}
                    <div class="{{ $card }} p-5">
                        <p class="text-xs font-black uppercase tracking-[0.18em] text-gray-400 dark:text-gray-500 mb-3">
                            Exámenes ({{ $ticket->ingreso->solicitudesLab->count() }})
                        </p>
                        <ul class="space-y-3">
                            @foreach($ticket->ingreso->solicitudesLab->groupBy('examen.categoriaExamen.nombre') as $categoria => $solicitudes)
                            <li>
                                <p class="text-[11px] font-bold uppercase tracking-wide text-gray-400 dark:text-gray-500 mb-1.5">
                                    {{ $categoria ?: 'Sin categoría' }}
                                </p>
                                <ul class="space-y-1.5 pl-2">
                                    @foreach($solicitudes as $sol)
                                    <li class="flex items-center gap-2 text-sm">
                                        <span class="w-1.5 h-1.5 rounded-full bg-[#52ABB1] shrink-0"></span>
                                        <span class="font-mono text-xs text-gray-400 dark:text-gray-500">{{ $sol->examen->cod_examen }}</span>
                                        <span class="text-gray-700 dark:text-gray-300">{{ $sol->examen->nombre_examen }}</span>
                                    </li>
                                    @endforeach
                                </ul>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                </div>

                {{-- ── COL DERECHA: Acciones ── --}}
                <div class="lg:col-span-2 space-y-4">

                    {{-- PASO 1: Llamar (solo si en_espera) --}}
                    @if($ticket->estado === 'en_espera')
                    <div class="{{ $card }} p-6 border-yellow-200 dark:border-yellow-800/40">
                        <div class="flex items-start gap-4">
                            <div class="w-11 h-11 rounded-xl bg-yellow-100 dark:bg-yellow-900/30 flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <p class="font-semibold text-gray-900 dark:text-white mb-0.5">Paso 1 — Llamar al paciente</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                                    El ticket pasará a <strong class="text-gray-700 dark:text-gray-300">en turno</strong>.
                                </p>
                                <form method="POST" action="{{ route('laboratorio.llamar', $ticket) }}">
                                    @csrf
                                    <button type="submit"
                                            class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl
                                                   bg-yellow-400 hover:bg-yellow-500 text-yellow-900
                                                   font-bold text-sm transition-colors shadow-sm shadow-yellow-400/30">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405M15 17H9m6 0v1a3 3 0 11-6 0v-1"/>
                                        </svg>
                                        Llamar a {{ $ticket->paciente->nombres }}
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    @endif

                    {{-- PASO 2: Registrar muestra (solo si en_turno) --}}
                    @if($ticket->estado === 'en_turno')
                    <div class="{{ $card }} p-6">
                        <div class="flex items-center gap-3 mb-5">
                            <div class="w-11 h-11 rounded-xl bg-[#52ABB1]/10 dark:bg-[#52ABB1]/20 flex items-center justify-center">
                                <svg class="w-5 h-5 text-[#52ABB1]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900 dark:text-white">Paso 2 — Registrar toma de muestra</p>
                                <p class="text-xs text-gray-400 dark:text-gray-500">El paciente podrá retirarse una vez confirmado</p>
                            </div>
                        </div>

                        <form method="POST" action="{{ route('laboratorio.registrar-muestra', $ticket) }}" class="space-y-4">
                            @csrf
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">
                                    Observaciones de la muestra
                                    <span class="text-gray-400 dark:text-gray-500 font-normal">(opcional)</span>
                                </label>
                                <textarea name="observacion_muestra" rows="3"
                                          placeholder="Ej: Paciente en ayunas 8 horas, muestra en buenas condiciones..."
                                          class="{{ $inputClass }} resize-none">{{ old('observacion_muestra') }}</textarea>
                                @error('observacion_muestra')
                                <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="flex items-start gap-3 p-4 rounded-2xl bg-blue-50 dark:bg-blue-900/20 border border-blue-100 dark:border-blue-800">
                                <svg class="w-4 h-4 text-blue-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <p class="text-xs text-blue-700 dark:text-blue-300">
                                    Al confirmar, <strong>todos los exámenes</strong> pasarán a "Muestra tomada". El ticket
                                    quedará pendiente de resultados y el paciente puede retirarse.
                                </p>
                            </div>

                            <div class="flex items-center gap-3 pt-1">
                                <button type="submit"
                                        class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl
                                               bg-[#52ABB1] hover:bg-[#3d8f95] text-white
                                               font-bold text-sm transition-colors shadow-sm shadow-[#52ABB1]/20">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    Confirmar toma de muestra
                                </button>
                                <a href="{{ route('laboratorio.index') }}"
                                   class="px-4 py-2.5 rounded-xl text-sm font-medium text-gray-500 dark:text-gray-400
                                          hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                                    Cancelar
                                </a>
                            </div>
                        </form>
                    </div>
                    @endif

                    {{-- Estado: ya atendido --}}
                    @if($ticket->estado === 'atendido')
                    <div class="{{ $card }} p-6 text-center border-green-200 dark:border-green-800/40">
                        <div class="w-12 h-12 rounded-2xl bg-green-100 dark:bg-green-900/30 flex items-center justify-center mx-auto mb-3">
                            <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <p class="font-semibold text-gray-900 dark:text-white mb-1">Muestra registrada</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">El paciente ya puede retirarse.</p>
                        <a href="{{ route('laboratorio.show-resultados', $ticket) }}"
                           class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl
                                  bg-[#52ABB1] hover:bg-[#3d8f95] text-white font-bold text-sm transition-colors">
                            Ir a subir resultados →
                        </a>
                    </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
</x-app-layout>