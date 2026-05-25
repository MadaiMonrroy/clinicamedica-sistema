<x-app-layout>
    @php
        $pageWrap = 'space-y-6';
        $card = 'rounded-[2rem] border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 shadow-sm';
        $primaryBtn = 'inline-flex items-center justify-center rounded-2xl bg-[#44B0B3] hover:bg-[#389a9d] text-white font-bold px-5 py-3 shadow-lg shadow-[#44B0B3]/25 transition';
        $secondaryBtn = 'inline-flex items-center justify-center rounded-2xl border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-800 px-4 py-2 transition text-sm';
        $badge = 'inline-flex items-center rounded-full px-2.5 py-1 text-[11px] font-bold uppercase tracking-[0.12em]';
    @endphp

    <div class=" mx-auto {{ $pageWrap }}">

        {{-- HEADER --}}
        <section class="{{ $card }} p-5 sm:p-6">
            <div class="flex flex-col xl:flex-row xl:items-start xl:justify-between gap-5">
                <div>
                    <p class="text-xs font-black uppercase tracking-[0.18em] text-gray-400 dark:text-gray-500">
                        Recepción / Ingreso
                    </p>

                    <h1 class="mt-2 text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white">
                        {{ $ingreso->numero_preingreso ?: 'Ingreso sin número' }}
                    </h1>

                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                        {{ optional($ingreso->fecha_ingreso)->format('d/m/Y H:i') }}
                    </p>
                </div>

                <div class="flex flex-wrap gap-2">
                    @if($ingreso->estado !== 'cancelado' && $ingreso->estado !== 'finalizado')
                        <form method="POST" action="{{ route('recepcion.ingresos.cancel', $ingreso) }}">
                            @csrf
                            @method('PATCH')
                            <button class="text-red-600 text-sm font-semibold hover:underline">
                                Cancelar
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            {{-- BADGES --}}
            <div class="mt-5 flex flex-wrap gap-3">
                @php
                    $estadoClass = match($ingreso->estado) {
                        'esperando_enfermeria' => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-300',
                        'en_enfermeria' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300',
                        'en_area' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300',
                        'finalizado' => 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300',
                        'cancelado' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300',
                        default => 'bg-gray-100 text-gray-700'
                    };

                    $prioridadClass = $ingreso->prioridad_inicial === 'urgente'
                        ? 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300'
                        : 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300';
                @endphp

                <span class="{{ $badge }} {{ $estadoClass }}">
                    {{ str_replace('_', ' ', $ingreso->estado) }}
                </span>

                <span class="{{ $badge }} {{ $prioridadClass }}">
                    {{ $ingreso->prioridad_inicial }}
                </span>

                <span class="{{ $badge }} bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300">
                    {{ $ingreso->tipo_ingreso }}
                </span>
            </div>
        </section>

        {{-- GRID --}}
        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

            {{-- PACIENTE --}}
            <section class="{{ $card }} p-5 sm:p-6">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4">
                    Paciente
                </h2>

                <div class="space-y-3">
                    <p class="font-semibold text-gray-900 dark:text-white">
                        {{ $ingreso->paciente->nombre_completo }}
                    </p>

                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        CI: {{ $ingreso->paciente->ci }}
                    </p>

                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        Tel: {{ $ingreso->paciente->telefono ?: 'No registrado' }}
                    </p>

                    <a href="{{ route('recepcion.pacientes.show', $ingreso->paciente) }}"
                       class="inline-block mt-3 text-sm font-semibold text-[#44B0B3] hover:underline">
                        Ver ficha completa →
                    </a>
                </div>
            </section>

            {{-- MOTIVO --}}
            <section class="xl:col-span-2 {{ $card }} p-5 sm:p-6">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-3">
                    Motivo de ingreso
                </h2>

                <p class="text-sm text-gray-600 dark:text-gray-300 leading-relaxed">
                    {{ $ingreso->motivo_ingreso ?: 'No especificado' }}
                </p>
            </section>

        </div>

        {{-- TRIAGE --}}
        <section class="{{ $card }} p-5 sm:p-6">
            <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4">
                Enfermeria
            </h2>

            @if($ingreso->enfermeria->count())
                @foreach($ingreso->enfermeria as $enfermeria)
                    <div class="border border-gray-200 dark:border-gray-700 rounded-2xl p-4 mb-4">
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            {{ optional($enfermeria->fecha_enfermeria)->format('d/m/Y H:i') }}
                        </p>

                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mt-3 text-sm">
                            <div>Temp: {{ $enfermeria->temperatura }}</div>
                            <div>Presión: {{ $enfermeria->presion_arterial }}</div>
                            <div>FC: {{ $enfermeria->frecuencia_cardiaca }}</div>
                            <div>Sat: {{ $enfermeria->saturacion_oxigeno }}</div>
                        </div>

                        <div class="mt-3 text-sm">
                            Área destino:
                            <span class="font-semibold">
                                {{ $enfermeria->areaDestino->nombre ?? '-' }}
                            </span>
                        </div>
                    </div>
                @endforeach
            @else
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Aún no se ha realizado enfermeria.
                </p>
            @endif
        </section>

        {{-- TICKETS --}}
        <section class="{{ $card }} p-5 sm:p-6">
            <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4">
                Tickets / Turnos
            </h2>

            @if($ingreso->tickets->count())
                <div class="space-y-3">
                    @foreach($ingreso->tickets as $ticket)
                        <div class="flex items-center justify-between border border-gray-200 dark:border-gray-700 rounded-2xl px-4 py-3">
                            <div>
                                <p class="font-semibold text-gray-900 dark:text-white">
                                    Ticket #{{ $ticket->numero_ticket }}
                                </p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    Área: {{ $ticket->area->nombre ?? '-' }}
                                </p>
                            </div>

                            <span class="{{ $badge }} bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300">
                                {{ $ticket->estado }}
                            </span>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Aún no se han generado tickets.
                </p>
            @endif
        </section>

    </div>
</x-app-layout>