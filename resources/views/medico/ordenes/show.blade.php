<x-app-layout>
    @php
        $pageWrap = 'space-y-6';
        $card = 'rounded-[2rem] border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 shadow-sm';
        $badge = 'inline-flex items-center rounded-full px-2.5 py-1 text-[11px] font-bold uppercase tracking-[0.12em]';
    @endphp

    <div class="  mx-auto {{ $pageWrap }}">
        <section class="{{ $card }} p-5 sm:p-6">
            <div class="flex flex-col xl:flex-row xl:items-start xl:justify-between gap-5">
                <div>
                    <p class="text-xs font-black uppercase tracking-[0.18em] text-gray-400 dark:text-gray-500">
                        Órdenes médicas / Detalle
                    </p>
                    <h1 class="mt-2 text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white">
                        {{ $orden->num_orden }}
                    </h1>
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                        Paciente: {{ $orden->atencion?->ticket?->paciente?->nombre_completo ?? '-' }}
                    </p>
                </div>

                @php
                    $estadoClass = match($orden->estado) {
                        'pendiente' => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-300',
                        'en_proceso' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300',
                        'completada' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300',
                        'cancelada' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300',
                        default => 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300',
                    };
                @endphp

                <div class="flex flex-wrap gap-2">
                    <span class="{{ $badge }} {{ $estadoClass }}">
                        {{ str_replace('_', ' ', $orden->estado) }}
                    </span>
                    <span class="{{ $badge }} bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300">
                        {{ $orden->tipo }}
                    </span>
                </div>
            </div>
        </section>

        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
            <section class="xl:col-span-1 {{ $card }} p-5 sm:p-6">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Resumen</h2>

                <div class="space-y-4">
                    <div>
                        <p class="text-xs font-black uppercase tracking-[0.14em] text-gray-400 dark:text-gray-500">Fecha</p>
                        <p class="mt-1 text-sm font-semibold text-gray-900 dark:text-white">
                            {{ $orden->fecha ? $orden->fecha->format('d/m/Y H:i') : '-' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs font-black uppercase tracking-[0.14em] text-gray-400 dark:text-gray-500">Paciente</p>
                        <p class="mt-1 text-sm font-semibold text-gray-900 dark:text-white">
                            {{ $orden->atencion?->ticket?->paciente?->nombre_completo ?? '-' }}
                        </p>
                    </div>
                </div>
            </section>

            <section class="xl:col-span-2 {{ $card }} p-5 sm:p-6">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Descripción e indicaciones</h2>

                <div class="space-y-6">
                    <div>
                        <p class="text-xs font-black uppercase tracking-[0.14em] text-gray-400 dark:text-gray-500">Descripción</p>
                        <p class="mt-2 text-sm leading-6 text-gray-600 dark:text-gray-300 whitespace-pre-line">
                            {{ $orden->descripcion ?: 'Sin descripción.' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs font-black uppercase tracking-[0.14em] text-gray-400 dark:text-gray-500">Indicaciones</p>
                        <p class="mt-2 text-sm leading-6 text-gray-600 dark:text-gray-300 whitespace-pre-line">
                            {{ $orden->indicaciones ?: 'Sin indicaciones.' }}
                        </p>
                    </div>
                </div>
            </section>
        </div>

        <section class="{{ $card }} p-5 sm:p-6">
            <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Exámenes / adjuntos</h2>

            @if($orden->examenes->count())
                <div class="space-y-3 mb-6">
                    @foreach($orden->examenes as $examen)
                        <div class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/40 px-4 py-3">
                            <p class="font-semibold text-gray-900 dark:text-white">{{ $examen->nombre_examen }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $examen->tipo_examen ?: '-' }}</p>
                        </div>
                    @endforeach
                </div>
            @endif

            @if($orden->adjuntosLaboratorio->count())
                <div class="space-y-3">
                    @foreach($orden->adjuntosLaboratorio as $adjunto)
                        <div class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 px-4 py-4">
                            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                                <div>
                                    <p class="font-semibold text-gray-900 dark:text-white">
                                        {{ $adjunto->nombre_archivo ?: 'Adjunto de laboratorio' }}
                                    </p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ $adjunto->fecha_subida ? $adjunto->fecha_subida->format('d/m/Y H:i') : '-' }}
                                    </p>
                                </div>

                                @if($adjunto->url_archivo)
                                    <a href="{{ $adjunto->url_archivo }}" target="_blank"
                                       class="inline-flex items-center rounded-xl px-3 py-2 text-xs font-semibold text-[#44B0B3] hover:bg-[#44B0B3]/10 transition">
                                        Ver PDF
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @elseif(!$orden->examenes->count())
                <p class="text-sm text-gray-500 dark:text-gray-400">No hay exámenes ni adjuntos registrados.</p>
            @endif
        </section>
    </div>
</x-app-layout>