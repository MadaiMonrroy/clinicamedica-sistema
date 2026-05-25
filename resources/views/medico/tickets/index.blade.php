<x-app-layout>
    @php
        $pageWrap = 'space-y-6';
        $card = 'rounded-[2rem] border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 shadow-sm';
        $inputClass = 'block w-full px-4 py-3 bg-white dark:bg-gray-800/40 border border-gray-200 dark:border-gray-700 focus:border-[#44B0B3] focus:ring-1 focus:ring-[#44B0B3] rounded-2xl text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 transition-all text-sm shadow-sm';
        $primaryBtn = 'inline-flex items-center justify-center rounded-2xl bg-[#44B0B3] hover:bg-[#389a9d] text-white font-bold px-5 py-3 shadow-lg shadow-[#44B0B3]/25 transition';
        $secondaryBtn = 'inline-flex items-center justify-center rounded-2xl border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-800 px-5 py-3 transition';
        $badge = 'inline-flex items-center rounded-full px-2.5 py-1 text-[11px] font-bold uppercase tracking-[0.12em]';
    @endphp

    <div class="  mx-auto {{ $pageWrap }}">
        <section class="{{ $card }} p-5 sm:p-6">
            <div class="flex flex-col xl:flex-row xl:items-center xl:justify-between gap-5">
                <div>
                    <p class="text-xs font-black uppercase tracking-[0.18em] text-gray-400 dark:text-gray-500">
                        Atención médica / Tickets
                    </p>
                    <h1 class="mt-2 text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white">
                        Gestión de turnos
                    </h1>
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400 max-w-2xl">
                        Consulta los tickets generados, revisa su estado actual y accede rápidamente al detalle de cada turno.
                    </p>
                </div>
            </div>

            <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50 px-4 py-4">
                    <p class="text-xs uppercase tracking-[0.14em] text-gray-400 dark:text-gray-500">Tickets visibles</p>
                    <p class="mt-2 text-2xl font-bold text-gray-900 dark:text-white">{{ $tickets->total() }}</p>
                </div>

                <div class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50 px-4 py-4">
                    <p class="text-xs uppercase tracking-[0.14em] text-gray-400 dark:text-gray-500">Filtro actual</p>
                    <p class="mt-2 text-sm font-semibold text-gray-900 dark:text-white">
                        {{ $estado ?: 'Todos los estados' }}
                    </p>
                </div>

                <div class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50 px-4 py-4">
                    <p class="text-xs uppercase tracking-[0.14em] text-gray-400 dark:text-gray-500">Flujo</p>
                    <p class="mt-2 text-sm font-semibold text-gray-900 dark:text-white">
                        Espera → Turno → Atención
                    </p>
                </div>
            </div>
        </section>

        @if(session('success'))
            <div class="rounded-2xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700 dark:border-green-800 dark:bg-green-900/20 dark:text-green-300">
                {{ session('success') }}
            </div>
        @endif

        <section class="{{ $card }} p-5 sm:p-6">
            <div class="flex items-center justify-between gap-4 mb-4">
                <div>
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white">Filtrar tickets</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Consulta por estado del turno.</p>
                </div>
            </div>

            <form method="GET" action="{{ route('tickets.index') }}" class="grid grid-cols-1 lg:grid-cols-[1fr_auto] gap-3">
                <select name="estado" class="{{ $inputClass }}">
                    <option value="">Todos los estados</option>
                    <option value="en_espera" @selected(request('estado') === 'en_espera')>En espera</option>
                    <option value="en_turno" @selected(request('estado') === 'en_turno')>En turno</option>
                    <option value="atendido" @selected(request('estado') === 'atendido')>Atendido</option>
                    <option value="derivado" @selected(request('estado') === 'derivado')>Derivado</option>
                    <option value="cancelado" @selected(request('estado') === 'cancelado')>Cancelado</option>
                </select>

                <button type="submit" class="{{ $secondaryBtn }}">
                    Aplicar filtro
                </button>
            </form>
        </section>

        <section class="{{ $card }} overflow-hidden">
            <div class="px-5 sm:px-6 py-5 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white">Listado de tickets</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">Turnos generados por enfermeria o flujo clínico.</p>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-gray-50 dark:bg-gray-800/50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-[0.14em] text-gray-400 dark:text-gray-500">Ticket</th>
                            <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-[0.14em] text-gray-400 dark:text-gray-500">Paciente</th>
                            <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-[0.14em] text-gray-400 dark:text-gray-500">Área</th>
                            <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-[0.14em] text-gray-400 dark:text-gray-500">Prioridad</th>
                            <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-[0.14em] text-gray-400 dark:text-gray-500">Estado</th>
                            <th class="px-6 py-4 text-right text-xs font-black uppercase tracking-[0.14em] text-gray-400 dark:text-gray-500">Acciones</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                        @forelse($tickets as $ticket)
                            <tr class="hover:bg-gray-50/80 dark:hover:bg-gray-800/40 transition">
                                <td class="px-6 py-4">
                                    <div class="font-semibold text-gray-900 dark:text-white">
                                        {{ $ticket->numero_ticket }}
                                    </div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ optional($ticket->created_at)->format('d/m/Y H:i') }}
                                    </div>
                                </td>

                                <td class="px-6 py-4">
                                    <div class="font-semibold text-gray-900 dark:text-white">
                                        {{ $ticket->paciente?->nombre_completo ?? 'Paciente no disponible' }}
                                    </div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">
                                        CI: {{ $ticket->paciente?->ci ?? '-' }}
                                    </div>
                                </td>

                                <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-200">
                                    {{ $ticket->area?->nombre ?? '-' }}
                                </td>

                                <td class="px-6 py-4">
                                    @php
                                        $prioridadClass = match($ticket->prioridad_turno) {
                                            'critico' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300',
                                            'urgente' => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-300',
                                            default => 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300',
                                        };
                                    @endphp

                                    <span class="{{ $badge }} {{ $prioridadClass }}">
                                        {{ $ticket->prioridad_turno }}
                                    </span>
                                </td>

                                <td class="px-6 py-4">
                                    @php
                                        $estadoClass = match($ticket->estado) {
                                            'en_espera' => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-300',
                                            'en_turno' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300',
                                            'atendido' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300',
                                            'derivado' => 'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-300',
                                            'cancelado' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300',
                                            default => 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300',
                                        };
                                    @endphp

                                    <span class="{{ $badge }} {{ $estadoClass }}">
                                        {{ str_replace('_', ' ', $ticket->estado) }}
                                    </span>
                                </td>

                                <td class="px-6 py-4">
                                    <div class="flex justify-end gap-2 flex-wrap">
                                        <a href="{{ route('tickets.show', $ticket) }}"
                                           class="inline-flex items-center rounded-xl px-3 py-2 text-xs font-semibold text-[#44B0B3] hover:bg-[#44B0B3]/10 transition">
                                            Ver
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center">
                                    <div class="max-w-md mx-auto">
                                        <p class="text-base font-semibold text-gray-700 dark:text-gray-200">
                                            No hay tickets registrados
                                        </p>
                                        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                                            Cuando enfermeria genere turnos, aparecerán aquí.
                                        </p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if(method_exists($tickets, 'links'))
                <div class="px-5 sm:px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                    {{ $tickets->links() }}
                </div>
            @endif
        </section>
    </div>
</x-app-layout>