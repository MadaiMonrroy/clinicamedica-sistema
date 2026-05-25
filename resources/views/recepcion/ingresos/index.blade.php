<x-app-layout>
    @php
        $pageWrap = 'space-y-6';
        $pageHeaderCard = 'rounded-[2rem] border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 shadow-sm';
        $sectionCard = 'rounded-[2rem] border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 shadow-sm';
        $inputClass = 'block w-full px-4 py-3 bg-white dark:bg-gray-800/40 border border-gray-200 dark:border-gray-700 focus:border-[#44B0B3] focus:ring-1 focus:ring-[#44B0B3] rounded-2xl text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 transition-all text-sm shadow-sm';
        $primaryBtn = 'inline-flex items-center justify-center rounded-2xl bg-[#44B0B3] hover:bg-[#389a9d] text-white font-bold px-5 py-3 shadow-lg shadow-[#44B0B3]/25 transition';
        $secondaryBtn = 'inline-flex items-center justify-center rounded-2xl border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-800 px-5 py-3 transition';

        $badgeBase = 'inline-flex items-center rounded-full px-2.5 py-1 text-[11px] font-bold uppercase tracking-[0.12em]';
    @endphp

    <div class=" mx-auto {{ $pageWrap }}">
        <section class="{{ $pageHeaderCard }} p-5 sm:p-6">
            <div class="flex flex-col gap-5 xl:flex-row xl:items-center xl:justify-between">
                <div>
                    <p class="text-xs font-black uppercase tracking-[0.18em] text-gray-400 dark:text-gray-500">
                        Recepción
                    </p>
                    <h1 class="mt-2 text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white">
                        Historial de ingresos
                    </h1>
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400 max-w-2xl">
                        Visualiza las visitas registradas, su prioridad inicial y el estado actual dentro del flujo clínico.
                    </p>
                </div>

                <div class="flex flex-col sm:flex-row gap-3">
                    <a href="{{ route('recepcion.ingresos.create') }}" class="{{ $primaryBtn }}">
                        Nuevo ingreso
                    </a>
                </div>
            </div>

            <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50 px-4 py-4">
                    <p class="text-xs uppercase tracking-[0.14em] text-gray-400 dark:text-gray-500">Ingresos visibles</p>
                    <p class="mt-2 text-2xl font-bold text-gray-900 dark:text-white">{{ $ingresos->total() }}</p>
                </div>

                <div class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50 px-4 py-4">
                    <p class="text-xs uppercase tracking-[0.14em] text-gray-400 dark:text-gray-500">Filtro actual</p>
                    <p class="mt-2 text-sm font-semibold text-gray-900 dark:text-white">
                        {{ $estado ?: 'Todos los estados' }}
                    </p>
                </div>

                <div class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50 px-4 py-4">
                    <p class="text-xs uppercase tracking-[0.14em] text-gray-400 dark:text-gray-500">Acceso rápido</p>
                    <a href="{{ route('recepcion.pacientes.index') }}" class="mt-2 inline-flex text-sm font-semibold text-[#44B0B3] hover:underline">
                        Ir a pacientes
                    </a>
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

        <section class="{{ $sectionCard }} p-5 sm:p-6">
            <div class="flex items-center justify-between gap-4 mb-4">
                <div>
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white">Filtrar ingresos</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Consulta por estado del flujo.</p>
                </div>
            </div>

            <form method="GET" action="{{ route('recepcion.ingresos.index') }}" class="grid grid-cols-1 lg:grid-cols-[1fr_auto] gap-3">
                <select name="estado" class="{{ $inputClass }}">
                    <option value="">Todos los estados</option>
                    <option value="esperando_enfermeria" @selected(request('estado') === 'esperando_enfermeria')>Esperando enfermeria</option>
                    <option value="en_enfermeria" @selected(request('estado') === 'en_enfermeria')>En enfermeria</option>
                    <option value="en_area" @selected(request('estado') === 'en_area')>En área</option>
                    <option value="finalizado" @selected(request('estado') === 'finalizado')>Finalizado</option>
                    <option value="cancelado" @selected(request('estado') === 'cancelado')>Cancelado</option>
                </select>

                <button type="submit" class="{{ $secondaryBtn }}">
                    Aplicar filtro
                </button>
            </form>
        </section>

        <section class="{{ $sectionCard }} overflow-hidden">
            <div class="px-5 sm:px-6 py-5 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white">Listado de ingresos</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">Admisiones registradas en el sistema.</p>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-gray-50 dark:bg-gray-800/50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-[0.14em] text-gray-400 dark:text-gray-500">Paciente</th>
                            <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-[0.14em] text-gray-400 dark:text-gray-500">Ingreso</th>
                            <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-[0.14em] text-gray-400 dark:text-gray-500">Tipo</th>
                            <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-[0.14em] text-gray-400 dark:text-gray-500">Prioridad</th>
                            <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-[0.14em] text-gray-400 dark:text-gray-500">Estado</th>
                            <th class="px-6 py-4 text-right text-xs font-black uppercase tracking-[0.14em] text-gray-400 dark:text-gray-500">Acciones</th>
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

                                    <span class="{{ $badgeBase }} {{ $prioridadClass }}">
                                        {{ $ingreso->prioridad_inicial }}
                                    </span>
                                </td>

                                <td class="px-6 py-4">
                                    @php
                                        $estadoClass = match($ingreso->estado) {
                                            'esperando_enfermeria' => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-300',
                                            'en_enfermeria' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300',
                                            'en_area' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300',
                                            'finalizado' => 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300',
                                            'cancelado' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300',
                                            default => 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300',
                                        };
                                    @endphp

                                    <span class="{{ $badgeBase }} {{ $estadoClass }}">
                                        {{ str_replace('_', ' ', $ingreso->estado) }}
                                    </span>
                                </td>

                                <td class="px-6 py-4">
                                    <div class="flex justify-end gap-2 flex-wrap">
                                        <a href="{{ route('recepcion.ingresos.show', $ingreso) }}"
                                           class="inline-flex items-center rounded-xl px-3 py-2 text-xs font-semibold text-[#44B0B3] hover:bg-[#44B0B3]/10 transition">
                                            Ver
                                        </a>

                                        @if($ingreso->estado !== 'cancelado' && $ingreso->estado !== 'finalizado')
                                            <form method="POST" action="{{ route('recepcion.ingresos.cancel', $ingreso) }}">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit"
                                                    class="inline-flex items-center rounded-xl px-3 py-2 text-xs font-semibold text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 transition">
                                                    Cancelar
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center">
                                    <div class="max-w-md mx-auto">
                                        <p class="text-base font-semibold text-gray-700 dark:text-gray-200">
                                            No hay ingresos registrados
                                        </p>
                                        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                                            Comienza registrando un nuevo ingreso desde recepción.
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