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
                        Interconsultas / Detalle
                    </p>
                    <h1 class="mt-2 text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white">
                        {{ $interconsulta->atencion?->ticket?->paciente?->nombre_completo ?? 'Derivación' }}
                    </h1>
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                        {{ $interconsulta->fecha ? $interconsulta->fecha->format('d/m/Y H:i') : '-' }}
                    </p>
                </div>

                @php
                    $estadoClass = match($interconsulta->estado) {
                        'pendiente' => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-300',
                        'aceptada' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300',
                        'completada' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300',
                        'cancelada' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300',
                        default => 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300',
                    };
                @endphp

                <div class="flex flex-wrap gap-2">
                    <span class="{{ $badge }} {{ $estadoClass }}">
                        {{ $interconsulta->estado }}
                    </span>

                    @if($interconsulta->estado === 'pendiente')
                        <form method="POST" action="{{ route('interconsultas.accept', $interconsulta) }}">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="{{ $secondaryBtn }}">
                                Aceptar
                            </button>
                        </form>
                    @endif

                    @if(in_array($interconsulta->estado, ['pendiente', 'aceptada']))
                        <form method="POST" action="{{ route('interconsultas.complete', $interconsulta) }}">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="{{ $primaryBtn }}">
                                Completar
                            </button>
                        </form>
                    @endif

                    @if($interconsulta->estado !== 'cancelada' && $interconsulta->estado !== 'completada')
                        <form method="POST" action="{{ route('interconsultas.cancel', $interconsulta) }}">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="text-red-600 text-sm font-semibold hover:underline">
                                Cancelar
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </section>

        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
            <section class="xl:col-span-1 {{ $card }} p-5 sm:p-6">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Resumen</h2>

                <div class="space-y-4">
                    <div>
                        <p class="text-xs font-black uppercase tracking-[0.14em] text-gray-400 dark:text-gray-500">Paciente</p>
                        <p class="mt-1 text-sm font-semibold text-gray-900 dark:text-white">
                            {{ $interconsulta->atencion?->ticket?->paciente?->nombre_completo ?? '-' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs font-black uppercase tracking-[0.14em] text-gray-400 dark:text-gray-500">Área origen</p>
                        <p class="mt-1 text-sm font-semibold text-gray-900 dark:text-white">
                            {{ $interconsulta->areaOrigen?->nombre ?? '-' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs font-black uppercase tracking-[0.14em] text-gray-400 dark:text-gray-500">Área destino</p>
                        <p class="mt-1 text-sm font-semibold text-gray-900 dark:text-white">
                            {{ $interconsulta->areaDestino?->nombre ?? '-' }}
                        </p>
                    </div>
                </div>
            </section>

            <section class="xl:col-span-2 {{ $card }} p-5 sm:p-6">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Motivo y observación</h2>

                <div class="space-y-6">
                    <div>
                        <p class="text-xs font-black uppercase tracking-[0.14em] text-gray-400 dark:text-gray-500">Motivo de derivación</p>
                        <p class="mt-2 text-sm leading-6 text-gray-600 dark:text-gray-300 whitespace-pre-line">
                            {{ $interconsulta->motivo_interconsulta }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs font-black uppercase tracking-[0.14em] text-gray-400 dark:text-gray-500">Observación</p>
                        <p class="mt-2 text-sm leading-6 text-gray-600 dark:text-gray-300 whitespace-pre-line">
                            {{ $interconsulta->observacion ?: 'Sin observaciones.' }}
                        </p>
                    </div>
                </div>
            </section>
        </div>
    </div>
</x-app-layout>