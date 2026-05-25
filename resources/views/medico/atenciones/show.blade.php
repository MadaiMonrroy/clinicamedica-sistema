<x-app-layout>
    @php
        $pageWrap = 'space-y-6';
        $card = 'rounded-[2rem] border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 shadow-sm';
        $primaryBtn = 'inline-flex items-center justify-center rounded-2xl bg-[#44B0B3] hover:bg-[#389a9d] text-white font-bold px-5 py-3 shadow-lg shadow-[#44B0B3]/25 transition';
        $secondaryBtn = 'inline-flex items-center justify-center rounded-2xl border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-800 px-4 py-2 transition text-sm';
        $badge = 'inline-flex items-center rounded-full px-2.5 py-1 text-[11px] font-bold uppercase tracking-[0.12em]';
    @endphp

    <div class="mx-auto {{ $pageWrap }}">

        {{-- ── Cabecera ── --}}
        <section class="{{ $card }} p-5 sm:p-6">
            <div class="flex flex-col xl:flex-row xl:items-start xl:justify-between gap-5">
                <div>
                    <p class="text-xs font-black uppercase tracking-[0.18em] text-gray-400 dark:text-gray-500">
                        Atención médica / Detalle
                    </p>
                    <h1 class="mt-2 text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white">
                        {{ $atencion->ticket?->paciente?->nombre_completo ?? 'Paciente' }}
                    </h1>
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                        Ticket: {{ $atencion->ticket?->numero_ticket ?? '-' }} · Área: {{ $atencion->ticket?->area?->nombre ?? '-' }}
                    </p>
                </div>

                <div class="flex flex-wrap gap-2">
                    @php
                        $estadoClass = match($atencion->estado) {
                            'en_curso'   => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300',
                            'finalizada' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300',
                            'derivada'   => 'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-300',
                            'observacion'=> 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-300',
                            default      => 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300',
                        };
                    @endphp

                    <span class="{{ $badge }} {{ $estadoClass }}">
                        {{ str_replace('_', ' ', $atencion->estado) }}
                    </span>

                    <a href="{{ route('atenciones.edit', $atencion) }}" class="{{ $secondaryBtn }}">
                        Editar
                    </a>

                    @if($atencion->estado === 'en_curso')
                        <a href="{{ route('recetas.create', $atencion) }}" class="{{ $secondaryBtn }}">
                            Nueva receta
                        </a>
                        <a href="{{ route('ordenes-medicas.create', $atencion) }}" class="{{ $secondaryBtn }}">
                            Nueva orden
                        </a>
                        <a href="{{ route('interconsultas.create', $atencion) }}" class="{{ $secondaryBtn }}">
                            Derivar
                        </a>
                        <form method="POST" action="{{ route('atenciones.finish', $atencion) }}">
                            @csrf @method('PATCH')
                            <button type="submit" class="{{ $primaryBtn }}">
                                Finalizar atención
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </section>

        @if(session('success'))
            <div class="rounded-2xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700 dark:border-green-800 dark:bg-green-900/20 dark:text-green-300">
                {{ session('success') }}
            </div>
        @endif

        {{-- ── Signos vitales (si tiene enfermería asociada) ── --}}
        @if($atencion->ticket?->enfermeria)
            <section class="{{ $card }} p-5 sm:p-6">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h2 class="text-lg font-bold text-gray-900 dark:text-white">Signos vitales — Enfermería</h2>
                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">
                            {{ optional($atencion->ticket->enfermeria->fecha_enfermeria)->format('d/m/Y H:i') }}
                            @if($atencion->ticket->enfermeria->enfermera)
                                · {{ $atencion->ticket->enfermeria->enfermera->name }}
                                {{ $atencion->ticket->enfermeria->enfermera->apellido_paterno }}
                            @endif
                        </p>
                    </div>
                </div>
                @include('medico.atenciones.partials.signos_vitales', [
                    'enfermeria' => $atencion->ticket->enfermeria
                ])
            </section>
        @endif

        {{-- ── Resumen + Consulta médica ── --}}
        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
            <section class="xl:col-span-1 {{ $card }} p-5 sm:p-6">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Resumen</h2>
                <div class="space-y-4">
                    <div>
                        <p class="text-xs font-black uppercase tracking-[0.14em] text-gray-400 dark:text-gray-500">Paciente</p>
                        <p class="mt-1 text-sm font-semibold text-gray-900 dark:text-white">
                            {{ $atencion->ticket?->paciente?->nombre_completo ?? '-' }}
                        </p>
                    </div>
                    <div>
                        <p class="text-xs font-black uppercase tracking-[0.14em] text-gray-400 dark:text-gray-500">CI</p>
                        <p class="mt-1 text-sm font-semibold text-gray-900 dark:text-white">
                            {{ $atencion->ticket?->paciente?->ci ?? '-' }}
                        </p>
                    </div>
                    <div>
                        <p class="text-xs font-black uppercase tracking-[0.14em] text-gray-400 dark:text-gray-500">Inicio</p>
                        <p class="mt-1 text-sm font-semibold text-gray-900 dark:text-white">
                            {{ $atencion->fecha_inicio ? $atencion->fecha_inicio->format('d/m/Y H:i') : '-' }}
                        </p>
                    </div>
                    <div>
                        <p class="text-xs font-black uppercase tracking-[0.14em] text-gray-400 dark:text-gray-500">Fin</p>
                        <p class="mt-1 text-sm font-semibold text-gray-900 dark:text-white">
                            {{ $atencion->fecha_fin ? $atencion->fecha_fin->format('d/m/Y H:i') : 'Aún en curso' }}
                        </p>
                    </div>
                </div>
            </section>

            <section class="xl:col-span-2 {{ $card }} p-5 sm:p-6">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Consulta médica</h2>
                <div class="space-y-6">
                    <div>
                        <p class="text-xs font-black uppercase tracking-[0.14em] text-gray-400 dark:text-gray-500">Motivo de consulta</p>
                        <p class="mt-2 text-sm leading-6 text-gray-600 dark:text-gray-300">
                            {{ $atencion->motivo_consulta ?: 'No registrado.' }}
                        </p>
                    </div>
                    <div>
                        <p class="text-xs font-black uppercase tracking-[0.14em] text-gray-400 dark:text-gray-500">Examen físico</p>
                        <p class="mt-2 text-sm leading-6 text-gray-600 dark:text-gray-300 whitespace-pre-line">
                            {{ $atencion->examen_fisico ?: 'No registrado.' }}
                        </p>
                    </div>
                    <div>
                        <p class="text-xs font-black uppercase tracking-[0.14em] text-gray-400 dark:text-gray-500">Diagnóstico clínico</p>
                        <p class="mt-2 text-sm leading-6 text-gray-600 dark:text-gray-300 whitespace-pre-line">
                            {{ $atencion->diagnostico_texto ?: 'No registrado.' }}
                        </p>
                    </div>
                </div>
            </section>
        </div>

        {{-- ── Diagnósticos / Recetas / Órdenes ── --}}
        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
            <section class="xl:col-span-1 {{ $card }} p-5 sm:p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white">Diagnósticos</h2>
                </div>
                @if($atencion->diagnosticos->count())
                    <div class="space-y-3">
                        @foreach($atencion->diagnosticos as $diagnostico)
                            <div class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/40 px-4 py-3">
                                <p class="text-sm font-semibold text-gray-900 dark:text-white">
                                    {{ $diagnostico->nombre_diagnostico }}
                                </p>
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                    {{ $diagnostico->gravedad ?: 'Sin gravedad definida' }}
                                </p>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-gray-500 dark:text-gray-400">No hay diagnósticos registrados.</p>
                @endif
            </section>

            <section class="xl:col-span-1 {{ $card }} p-5 sm:p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white">Recetas</h2>
                </div>
                @if($atencion->recetas->count())
                    <div class="space-y-3">
                        @foreach($atencion->recetas as $receta)
                            <div class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/40 px-4 py-3">
                                <p class="text-sm font-semibold text-gray-900 dark:text-white">
                                    {{ $receta->numero_receta }}
                                </p>
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                    {{ $receta->fecha_receta ? $receta->fecha_receta->format('d/m/Y H:i') : '-' }}
                                </p>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="space-y-3">
                        <p class="text-sm text-gray-500 dark:text-gray-400">No hay recetas emitidas.</p>
                        @if($atencion->estado === 'en_curso')
                            <a href="{{ route('recetas.create', $atencion) }}"
                               class="inline-flex items-center rounded-xl px-3 py-2 text-xs font-semibold text-[#44B0B3] hover:bg-[#44B0B3]/10 transition">
                                Crear receta
                            </a>
                        @endif
                    </div>
                @endif
            </section>

            <section class="xl:col-span-1 {{ $card }} p-5 sm:p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white">Órdenes médicas</h2>
                </div>
                @if($atencion->ordenes->count())
                    <div class="space-y-3">
                        @foreach($atencion->ordenes as $orden)
                            <div class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/40 px-4 py-3">
                                <p class="text-sm font-semibold text-gray-900 dark:text-white">
                                    {{ $orden->num_orden }}
                                </p>
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                    {{ $orden->tipo }}
                                </p>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="space-y-3">
                        <p class="text-sm text-gray-500 dark:text-gray-400">No hay órdenes médicas registradas.</p>
                        @if($atencion->estado === 'en_curso')
                            <a href="{{ route('ordenes-medicas.create', $atencion) }}"
                               class="inline-flex items-center rounded-xl px-3 py-2 text-xs font-semibold text-[#44B0B3] hover:bg-[#44B0B3]/10 transition">
                                Crear orden médica
                            </a>
                        @endif
                    </div>
                @endif
            </section>
        </div>

    </div>
</x-app-layout>