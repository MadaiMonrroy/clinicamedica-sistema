<x-app-layout>
    @php
        $pageWrap = 'space-y-6';
        $card = 'rounded-[2rem] border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 shadow-sm';
        $primaryBtn = 'inline-flex items-center justify-center rounded-2xl bg-[#44B0B3] hover:bg-[#389a9d] text-white font-bold px-5 py-3 shadow-lg shadow-[#44B0B3]/25 transition';
        $secondaryBtn = 'inline-flex items-center justify-center rounded-2xl border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-800 px-4 py-2 transition text-sm';
        $badge = 'inline-flex items-center rounded-full px-2.5 py-1 text-[11px] font-bold uppercase tracking-[0.12em]';
    @endphp

    <div class="  mx-auto {{ $pageWrap }}">
        <section class="{{ $card }} p-5 sm:p-6">
            <div class="flex flex-col xl:flex-row xl:items-start xl:justify-between gap-5">
                <div>
                    <p class="text-xs font-black uppercase tracking-[0.18em] text-gray-400 dark:text-gray-500">
                        Recetas médicas / Detalle
                    </p>
                    <h1 class="mt-2 text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white">
                        {{ $receta->numero_receta }}
                    </h1>
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                        Paciente: {{ $receta->atencion?->ticket?->paciente?->nombre_completo ?? '-' }}
                    </p>
                </div>

                @php
                    $estadoClass = match($receta->estado) {
                        'emitida' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300',
                        'anulada' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300',
                        'completada' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300',
                        default => 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300',
                    };
                @endphp

                <div class="flex flex-wrap gap-2">
                    <span class="{{ $badge }} {{ $estadoClass }}">
                        {{ $receta->estado }}
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
                            {{ $receta->fecha_receta ? $receta->fecha_receta->format('d/m/Y H:i') : '-' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs font-black uppercase tracking-[0.14em] text-gray-400 dark:text-gray-500">Paciente</p>
                        <p class="mt-1 text-sm font-semibold text-gray-900 dark:text-white">
                            {{ $receta->atencion?->ticket?->paciente?->nombre_completo ?? '-' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs font-black uppercase tracking-[0.14em] text-gray-400 dark:text-gray-500">Ticket</p>
                        <p class="mt-1 text-sm font-semibold text-gray-900 dark:text-white">
                            {{ $receta->atencion?->ticket?->numero_ticket ?? '-' }}
                        </p>
                    </div>
                </div>
            </section>

            <section class="xl:col-span-2 {{ $card }} p-5 sm:p-6">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Indicaciones generales</h2>
                <p class="text-sm leading-6 text-gray-600 dark:text-gray-300 whitespace-pre-line">
                    {{ $receta->indicacion_general ?: 'Sin indicaciones generales.' }}
                </p>
            </section>
        </div>

        <section class="{{ $card }} p-5 sm:p-6">
            <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Medicamentos</h2>

            @if($receta->detalles->count())
                <div class="space-y-3">
                    @foreach($receta->detalles as $detalle)
                        <div class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/40 px-4 py-4">
                            <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-3">
                                <div>
                                    <p class="font-semibold text-gray-900 dark:text-white">
                                        {{ $detalle->medicamento?->nombre_completo ?? 'Medicamento no disponible' }}
                                    </p>
                                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-300">
                                        Frecuencia: {{ $detalle->frecuencia ?: '-' }} · Duración: {{ $detalle->duracion ?: '-' }} · Cantidad: {{ $detalle->cantidad ?: '-' }}
                                    </p>
                                </div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ $detalle->observacion ?: '' }}
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-sm text-gray-500 dark:text-gray-400">No hay medicamentos registrados en esta receta.</p>
            @endif
        </section>
    </div>
</x-app-layout>