<x-app-layout>
    @php
        $pageWrap = 'space-y-6';
        $card = 'rounded-[2rem] border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 shadow-sm';
        $inputClass = 'block w-full px-4 py-3 bg-white dark:bg-gray-800/40 border border-gray-200 dark:border-gray-700 focus:border-[#44B0B3] focus:ring-1 focus:ring-[#44B0B3] rounded-2xl text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 transition-all text-sm shadow-sm';
        $primaryBtn = 'inline-flex items-center justify-center rounded-2xl bg-[#44B0B3] hover:bg-[#389a9d] text-white font-bold px-5 py-3 shadow-lg shadow-[#44B0B3]/25 transition';
        $badge = 'inline-flex items-center rounded-full px-2.5 py-1 text-[11px] font-bold uppercase tracking-[0.12em]';
    @endphp

    <div class="  mx-auto {{ $pageWrap }}">
        <section class="{{ $card }} p-5 sm:p-6">
            <div class="flex flex-col xl:flex-row xl:items-center xl:justify-between gap-5">
                <div>
                    <p class="text-xs font-black uppercase tracking-[0.18em] text-gray-400 dark:text-gray-500">
                        Enfermeria
                    </p>
                    <h1 class="mt-2 text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white">
                        Cola de pacientes
                    </h1>
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400 max-w-2xl">
                        Prioriza pacientes urgentes y gestiona la clasificación inicial para derivarlos al área correcta.
                    </p>
                </div>
            </div>

            <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50 px-4 py-4">
                    <p class="text-xs uppercase tracking-[0.14em] text-gray-400 dark:text-gray-500">Pendientes</p>
                    <p class="mt-2 text-2xl font-bold text-gray-900 dark:text-white">{{ $ingresos->total() }}</p>
                </div>

                <div class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50 px-4 py-4">
                    <p class="text-xs uppercase tracking-[0.14em] text-gray-400 dark:text-gray-500">Prioridad alta</p>
                    <p class="mt-2 text-sm font-semibold text-gray-900 dark:text-white">
                        Se atienden primero
                    </p>
                </div>

                <div class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50 px-4 py-4">
                    <p class="text-xs uppercase tracking-[0.14em] text-gray-400 dark:text-gray-500">Flujo</p>
                    <p class="mt-2 text-sm font-semibold text-gray-900 dark:text-white">
                        Evaluar → clasificar → generar ticket
                    </p>
                </div>
            </div>
        </section>

        @if(session('success'))
            <div class="rounded-2xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700 dark:border-green-800 dark:bg-green-900/20 dark:text-green-300">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700 dark:border-red-800 dark:bg-red-900/20 dark:text-red-300">
                {{ session('error') }}
            </div>
        @endif

        <section class="{{ $card }} overflow-hidden">
            <div class="px-5 sm:px-6 py-5 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white">Pacientes en espera de enfermeria</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">La lista ya viene priorizada desde el controlador.</p>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-gray-50 dark:bg-gray-800/50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-[0.14em] text-gray-400 dark:text-gray-500">Paciente</th>
                            <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-[0.14em] text-gray-400 dark:text-gray-500">Preingreso</th>
                            <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-[0.14em] text-gray-400 dark:text-gray-500">Tipo</th>
                            <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-[0.14em] text-gray-400 dark:text-gray-500">Prioridad</th>
                            <th class="px-6 py-4 text-right text-xs font-black uppercase tracking-[0.14em] text-gray-400 dark:text-gray-500">Acción</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                        @forelse($ingresos as $ingreso)
                            <tr class="hover:bg-gray-50/80 dark:hover:bg-gray-800/40 transition">
                                <td class="px-6 py-4">
                                    <div class="font-semibold text-gray-900 dark:text-white">
                                        {{ $ingreso->paciente?->nombre_completo ?? 'Paciente no disponible' }}
                                    </div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">
                                        CI: {{ $ingreso->paciente?->ci ?? '-' }}
                                    </div>
                                </td>

                                <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-200">
                                    <div>{{ $ingreso->numero_preingreso ?: 'Sin número' }}</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ optional($ingreso->fecha_ingreso)->format('d/m/Y H:i') }}
                                    </div>
                                </td>

                                <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-200">
                                    {{ $ingreso->tipo_ingreso === 'enfermeria' ? 'Enfermeria' : 'Laboratorio directo' }}
                                </td>

                                <td class="px-6 py-4">
                                    @php
                                        $prioridadClass = $ingreso->prioridad_inicial === 'urgente'
                                            ? 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300'
                                            : 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300';
                                    @endphp

                                    <span class="{{ $badge }} {{ $prioridadClass }}">
                                        {{ $ingreso->prioridad_inicial }}
                                    </span>
                                </td>

                                <td class="px-6 py-4">
                                    <div class="flex justify-end">
                                        <a href="{{ route('enfermeria.create', $ingreso) }}" class="{{ $primaryBtn }} !px-4 !py-2.5 !text-sm">
                                            Atender
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center">
                                    <div class="max-w-md mx-auto">
                                        <p class="text-base font-semibold text-gray-700 dark:text-gray-200">
                                            No hay pacientes pendientes en enfermeria
                                        </p>
                                        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                                            Cuando recepción registre ingresos para enfermeria aparecerán aquí.
                                        </p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if(method_exists($ingresos, 'links'))
                <div class="px-5 sm:px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                    {{ $ingresos->links() }}
                </div>
            @endif
        </section>
    </div>
</x-app-layout>