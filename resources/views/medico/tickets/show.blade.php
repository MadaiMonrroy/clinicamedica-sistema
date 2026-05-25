<x-app-layout>
    @php
        $pageWrap     = 'space-y-6';
        $card         = 'rounded-[2rem] border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 shadow-sm';
        $primaryBtn   = 'inline-flex items-center justify-center rounded-2xl bg-[#44B0B3] hover:bg-[#389a9d] text-white font-bold px-5 py-3 shadow-lg shadow-[#44B0B3]/25 transition';
        $secondaryBtn = 'inline-flex items-center justify-center rounded-2xl border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-800 px-4 py-2 transition text-sm';
        $badge        = 'inline-flex items-center rounded-full px-2.5 py-1 text-[11px] font-bold uppercase tracking-[0.12em]';

        // Verificar si ya existe una atención registrada para este ticket
        $atencionExistente = $ticket->atenciones()->latest()->first();
    @endphp

    <div class="mx-auto {{ $pageWrap }}">

        {{-- ── Cabecera ── --}}
        <section class="{{ $card }} p-5 sm:p-6">
            <div class="flex flex-col xl:flex-row xl:items-start xl:justify-between gap-5">
                <div>
                    <p class="text-xs font-black uppercase tracking-[0.18em] text-gray-400 dark:text-gray-500">
                        Atención médica / Ticket
                    </p>
                    <h1 class="mt-2 text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white">
                        {{ $ticket->numero_ticket }}
                    </h1>
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                        Creado el {{ optional($ticket->created_at)->format('d/m/Y H:i') }}
                    </p>
                </div>

                <div class="flex flex-wrap gap-2">

                    {{-- Estado: en_espera → solo se puede marcar en turno --}}
                    @if($ticket->estado === 'en_espera')
                        <form method="POST" action="{{ route('tickets.call', $ticket) }}">
                            @csrf @method('PATCH')
                            <button type="submit" class="{{ $secondaryBtn }}">Llamar paciente</button>
                        </form>
                        <form method="POST" action="{{ route('tickets.set-in-turn', $ticket) }}">
                            @csrf @method('PATCH')
                            <button type="submit" class="{{ $primaryBtn }}">Marcar en turno</button>
                        </form>
                    @endif

                    {{-- Estado: en_turno → iniciar o continuar atención según si ya existe --}}
                    @if($ticket->estado === 'en_turno')
                        @if($atencionExistente)
                            {{-- Ya existe una atención → ir a editar --}}
                            <a href="{{ route('atenciones.edit', $atencionExistente) }}" class="{{ $primaryBtn }}">
                                Continuar atención
                            </a>
                        @else
                            {{-- No existe atención aún → crear nueva --}}
                            <a href="{{ route('atenciones.create', $ticket) }}" class="{{ $primaryBtn }}">
                                Iniciar atención
                            </a>
                        @endif

                        <form method="POST" action="{{ route('tickets.finish', $ticket) }}">
                            @csrf @method('PATCH')
                            <button type="submit" class="{{ $secondaryBtn }}">Finalizar ticket</button>
                        </form>
                    @endif

                    {{-- Estado: atendido → solo ver la atención registrada --}}
                    @if($ticket->estado === 'atendido' && $atencionExistente)
                        <a href="{{ route('atenciones.show', $atencionExistente) }}"
                           class="{{ $secondaryBtn }}">
                            Ver atención registrada
                        </a>
                    @endif

                </div>
            </div>

            <div class="mt-5 flex flex-wrap gap-3">
                @php
                    $estadoClass = match($ticket->estado) {
                        'en_espera' => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-300',
                        'en_turno'  => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300',
                        'atendido'  => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300',
                        'derivado'  => 'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-300',
                        'cancelado' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300',
                        default     => 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300',
                    };
                    $prioridadClass = match($ticket->prioridad_turno) {
                        'critico' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300',
                        'urgente' => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-300',
                        default   => 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300',
                    };
                @endphp
                <span class="{{ $badge }} {{ $estadoClass }}">{{ str_replace('_', ' ', $ticket->estado) }}</span>
                <span class="{{ $badge }} {{ $prioridadClass }}">{{ $ticket->prioridad_turno }}</span>
                <span class="{{ $badge }} bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300">
                    {{ $ticket->area?->nombre ?? 'Sin área' }}
                </span>
            </div>
        </section>

        @if(session('success'))
            <div class="rounded-2xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700 dark:border-green-800 dark:bg-green-900/20 dark:text-green-300">
                {{ session('success') }}
            </div>
        @endif

        {{-- ── Paciente + Info ── --}}
        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
            <section class="xl:col-span-1 {{ $card }} p-5 sm:p-6">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Paciente</h2>
                <div class="space-y-3">
                    <p class="font-semibold text-gray-900 dark:text-white">
                        {{ $ticket->paciente?->nombre_completo ?? 'Paciente no disponible' }}
                    </p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">CI: {{ $ticket->paciente?->ci ?? '-' }}</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Tel: {{ $ticket->paciente?->telefono ?: 'No registrado' }}</p>
                    @if($ticket->paciente)
                        <a href="{{ route('recepcion.pacientes.show', $ticket->paciente) }}"
                           class="inline-block mt-3 text-sm font-semibold text-[#44B0B3] hover:underline">
                            Ver ficha completa →
                        </a>
                    @endif
                </div>
            </section>

            <section class="xl:col-span-2 {{ $card }} p-5 sm:p-6">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Información del ticket</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <p class="text-xs font-black uppercase tracking-[0.14em] text-gray-400 dark:text-gray-500">Área</p>
                        <p class="mt-1 text-sm font-semibold text-gray-900 dark:text-white">{{ $ticket->area?->nombre ?? 'No asignada' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-black uppercase tracking-[0.14em] text-gray-400 dark:text-gray-500">Ingreso relacionado</p>
                        <p class="mt-1 text-sm font-semibold text-gray-900 dark:text-white">{{ $ticket->ingreso?->numero_preingreso ?: 'Sin preingreso' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-black uppercase tracking-[0.14em] text-gray-400 dark:text-gray-500">Llamado en</p>
                        <p class="mt-1 text-sm font-semibold text-gray-900 dark:text-white">
                            {{ $ticket->llamado_en ? $ticket->llamado_en->format('d/m/Y H:i') : 'Aún no llamado' }}
                        </p>
                    </div>
                    <div>
                        <p class="text-xs font-black uppercase tracking-[0.14em] text-gray-400 dark:text-gray-500">Finalizado en</p>
                        <p class="mt-1 text-sm font-semibold text-gray-900 dark:text-white">
                            {{ $ticket->finalizado_en ? $ticket->finalizado_en->format('d/m/Y H:i') : 'No finalizado' }}
                        </p>
                    </div>
                    <div class="md:col-span-2">
                        <p class="text-xs font-black uppercase tracking-[0.14em] text-gray-400 dark:text-gray-500">Observación</p>
                        <p class="mt-1 text-sm leading-6 text-gray-600 dark:text-gray-300">
                            {{ $ticket->observacion ?: 'Sin observaciones registradas.' }}
                        </p>
                    </div>
                </div>
            </section>
        </div>

        {{-- ── Signos vitales ── --}}
        <section class="{{ $card }} p-5 sm:p-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white">Signos vitales — Enfermería</h2>
                    @if($ticket->enfermeria)
                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">
                            {{ optional($ticket->enfermeria->fecha_enfermeria)->format('d/m/Y H:i') }}
                            @if($ticket->enfermeria->enfermera)
                                · {{ $ticket->enfermeria->enfermera->name }} {{ $ticket->enfermeria->enfermera->apellido_paterno }}
                            @endif
                        </p>
                    @endif
                </div>
            </div>

            @if($ticket->enfermeria)
                @include('medico.atenciones.partials.signos_vitales', ['enfermeria' => $ticket->enfermeria])
            @else
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Este ticket aún no tiene un registro de enfermería asociado.
                </p>
            @endif
        </section>

    </div>
</x-app-layout>