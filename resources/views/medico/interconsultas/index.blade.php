<x-app-layout>
    @php
        $pageWrap = 'space-y-6';
        $card = 'rounded-[2rem] border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 shadow-sm';
        $badge = 'inline-flex items-center rounded-full px-2.5 py-1 text-[11px] font-bold uppercase tracking-[0.12em]';
    @endphp

    <div class="  mx-auto {{ $pageWrap }}">
        <section class="{{ $card }} p-5 sm:p-6">
            <p class="text-xs font-black uppercase tracking-[0.18em] text-gray-400 dark:text-gray-500">
                Interconsultas
            </p>
            <h1 class="mt-2 text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white">
                Historial de interconsultas
            </h1>
            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                Consulta interconsultas generadas entre áreas y su estado actual.
            </p>
        </section>

        <section class="{{ $card }} overflow-hidden">
            <div class="px-5 sm:px-6 py-5 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white">Listado de interconsultas</h2>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-gray-50 dark:bg-gray-800/50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-[0.14em] text-gray-400 dark:text-gray-500">Paciente</th>
                            <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-[0.14em] text-gray-400 dark:text-gray-500">Origen</th>
                            <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-[0.14em] text-gray-400 dark:text-gray-500">Destino</th>
                            <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-[0.14em] text-gray-400 dark:text-gray-500">Fecha</th>
                            <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-[0.14em] text-gray-400 dark:text-gray-500">Estado</th>
                            <th class="px-6 py-4 text-right text-xs font-black uppercase tracking-[0.14em] text-gray-400 dark:text-gray-500">Acciones</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                        @forelse($interconsultas as $interconsulta)
                            <tr class="hover:bg-gray-50/80 dark:hover:bg-gray-800/40 transition">
                                <td class="px-6 py-4">
                                    <div class="font-semibold text-gray-900 dark:text-white">
                                        {{ $interconsulta->atencion?->ticket?->paciente?->nombre_completo ?? '-' }}
                                    </div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">
                                        Ticket: {{ $interconsulta->atencion?->ticket?->numero_ticket ?? '-' }}
                                    </div>
                                </td>

                                <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-200">
                                    {{ $interconsulta->areaOrigen?->nombre ?? '-' }}
                                </td>

                                <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-200">
                                    {{ $interconsulta->areaDestino?->nombre ?? '-' }}
                                </td>

                                <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-200">
                                    {{ $interconsulta->fecha ? $interconsulta->fecha->format('d/m/Y H:i') : '-' }}
                                </td>

                                <td class="px-6 py-4">
                                    @php
                                        $estadoClass = match($interconsulta->estado) {
                                            'pendiente' => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-300',
                                            'aceptada' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300',
                                            'completada' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300',
                                            'cancelada' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300',
                                            default => 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300',
                                        };
                                    @endphp

                                    <span class="{{ $badge }} {{ $estadoClass }}">
                                        {{ $interconsulta->estado }}
                                    </span>
                                </td>

                                <td class="px-6 py-4 text-right">
                                    <a href="{{ route('interconsultas.show', $interconsulta) }}"
                                       class="inline-flex items-center rounded-xl px-3 py-2 text-xs font-semibold text-[#44B0B3] hover:bg-[#44B0B3]/10 transition">
                                        Ver
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-sm text-gray-500 dark:text-gray-400">
                                    No hay interconsultas registradas.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if(method_exists($interconsultas, 'links'))
                <div class="px-5 sm:px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                    {{ $interconsultas->links() }}
                </div>
            @endif
        </section>
    </div>
</x-app-layout>