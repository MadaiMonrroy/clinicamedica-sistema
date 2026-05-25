<x-app-layout>
    @php
        $pageWrap = 'space-y-6';
        $card = 'rounded-[2rem] border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 shadow-sm';
        $badge = 'inline-flex items-center rounded-full px-2.5 py-1 text-[11px] font-bold uppercase tracking-[0.12em]';
    @endphp

    <div class=" mx-auto {{ $pageWrap }}">
        <section class="{{ $card }} p-5 sm:p-6">
            <p class="text-xs font-black uppercase tracking-[0.18em] text-gray-400 dark:text-gray-500">
                Atención médica
            </p>
            <h1 class="mt-2 text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white">
                Historial de atenciones
            </h1>
            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400 max-w-2xl">
                Consulta las atenciones registradas y accede al detalle clínico de cada una.
            </p>
        </section>

        <section class="{{ $card }} overflow-hidden">
            <div class="px-5 sm:px-6 py-5 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white">Listado de atenciones</h2>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-gray-50 dark:bg-gray-800/50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-[0.14em] text-gray-400 dark:text-gray-500">Paciente</th>
                            <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-[0.14em] text-gray-400 dark:text-gray-500">Ticket</th>
                            <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-[0.14em] text-gray-400 dark:text-gray-500">Área</th>
                            <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-[0.14em] text-gray-400 dark:text-gray-500">Estado</th>
                            <th class="px-6 py-4 text-right text-xs font-black uppercase tracking-[0.14em] text-gray-400 dark:text-gray-500">Acciones</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                        @forelse($atenciones as $atencion)
                            <tr class="hover:bg-gray-50/80 dark:hover:bg-gray-800/40 transition">
                                <td class="px-6 py-4">
                                    <div class="font-semibold text-gray-900 dark:text-white">
                                        {{ $atencion->ticket?->paciente?->nombre_completo ?? 'Paciente no disponible' }}
                                    </div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">
                                        CI: {{ $atencion->ticket?->paciente?->ci ?? '-' }}
                                    </div>
                                </td>

                                <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-200">
                                    {{ $atencion->ticket?->numero_ticket ?? '-' }}
                                </td>

                                <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-200">
                                    {{ $atencion->ticket?->area?->nombre ?? '-' }}
                                </td>

                                <td class="px-6 py-4">
                                    @php
                                        $estadoClass = match($atencion->estado) {
                                            'en_curso' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300',
                                            'finalizada' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300',
                                            'derivada' => 'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-300',
                                            'observacion' => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-300',
                                            default => 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300',
                                        };
                                    @endphp

                                    <span class="{{ $badge }} {{ $estadoClass }}">
                                        {{ str_replace('_', ' ', $atencion->estado) }}
                                    </span>
                                </td>

                                <td class="px-6 py-4">
                                    <div class="flex justify-end">
                                        <a href="{{ route('atenciones.show', $atencion) }}"
                                           class="inline-flex items-center rounded-xl px-3 py-2 text-xs font-semibold text-[#44B0B3] hover:bg-[#44B0B3]/10 transition">
                                            Ver
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center">
                                    <div class="max-w-md mx-auto">
                                        <p class="text-base font-semibold text-gray-700 dark:text-gray-200">
                                            No hay atenciones registradas
                                        </p>
                                        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                                            Cuando un médico inicie una atención, aparecerá aquí.
                                        </p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if(method_exists($atenciones, 'links'))
                <div class="px-5 sm:px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                    {{ $atenciones->links() }}
                </div>
            @endif
        </section>
    </div>
</x-app-layout>