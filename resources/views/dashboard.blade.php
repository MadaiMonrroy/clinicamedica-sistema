<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-1">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Dashboard clínico
            </h2>
            <p class="text-sm text-gray-500 dark:text-gray-400">
                Flujo diario de pacientes, estado actual y carga por áreas.
            </p>
        </div>
    </x-slot>

    @php
        $pageWrap = 'space-y-6';
        $card = 'rounded-[2rem] border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 shadow-sm';
        $miniCard = 'rounded-[1.5rem] border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/40 p-4';
        $badge = 'inline-flex items-center rounded-full px-2.5 py-1 text-[11px] font-bold uppercase tracking-[0.12em]';
    @endphp

    <div class="py-8">
        <div class=" mx-auto sm:px-6 lg:px-8 {{ $pageWrap }}">

            {{-- KPIs --}}
            <section class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
                <div class="{{ $card }} p-5">
                    <p class="text-xs uppercase tracking-[0.14em] text-gray-400 dark:text-gray-500">Ingresos hoy</p>
                    <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">{{ $ingresosHoy }}</p>
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Pacientes registrados durante la jornada.</p>
                </div>

                <div class="{{ $card }} p-5">
                    <p class="text-xs uppercase tracking-[0.14em] text-gray-400 dark:text-gray-500">Esperando enfermeria</p>
                    <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">{{ $esperandoEnfermeria }}</p>
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Pendientes de clasificación inicial.</p>
                </div>

                <div class="{{ $card }} p-5">
                    <p class="text-xs uppercase tracking-[0.14em] text-gray-400 dark:text-gray-500">En enfermeria</p>
                    <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">{{ $enEnfermeria }}</p>
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Pacientes siendo evaluados por enfermería.</p>
                </div>

                <div class="{{ $card }} p-5">
                    <p class="text-xs uppercase tracking-[0.14em] text-gray-400 dark:text-gray-500">En áreas clínicas</p>
                    <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">{{ $enArea }}</p>
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Pacientes derivados a consulta, observación o laboratorio.</p>
                </div>

                <div class="{{ $card }} p-5">
                    <p class="text-xs uppercase tracking-[0.14em] text-gray-400 dark:text-gray-500">Atendidos hoy</p>
                    <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">{{ $finalizadosHoy }}</p>
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Tickets cerrados como atendidos.</p>
                </div>

                <div class="{{ $card }} p-5">
                    <p class="text-xs uppercase tracking-[0.14em] text-gray-400 dark:text-gray-500">Urgentes pendientes</p>
                    <p class="mt-2 text-3xl font-bold text-red-600 dark:text-red-400">{{ $urgentesPendientes }}</p>
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Pacientes urgentes aún dentro del flujo activo.</p>
                </div>
            </section>

            {{-- GRAFICOS --}}
            <section class="grid grid-cols-1 xl:grid-cols-2 gap-6">
                <div class="{{ $card }} p-5 sm:p-6">
                    <div class="mb-4">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">Pacientes por área</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Carga operativa de hoy por área activa.</p>
                    </div>
                    <div id="chart-pacientes-area"></div>
                </div>

                <div class="{{ $card }} p-5 sm:p-6">
                    <div class="mb-4">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">Estado del flujo</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Distribución actual del proceso clínico del día.</p>
                    </div>
                    <div id="chart-estado-flujo"></div>
                </div>
            </section>

            <section class="{{ $card }} p-5 sm:p-6">
                <div class="mb-4">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Ingresos por hora</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Comportamiento del ingreso de pacientes durante el día.</p>
                </div>
                <div id="chart-ingresos-hora"></div>
            </section>

            {{-- AREAS --}}
            <section class="{{ $card }} p-5 sm:p-6">
                <div class="mb-5">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Estado actual por área</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Para saber dónde están y cuántos pacientes tiene cada área.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
                    @foreach($areasDashboard as $area)
                        <div class="{{ $miniCard }}">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <h4 class="text-base font-bold text-gray-900 dark:text-white">
                                        {{ $area['nombre'] }}
                                    </h4>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ $area['codigo'] ?: strtoupper(substr($area['nombre'], 0, 3)) }}
                                    </p>
                                </div>

                                <span class="{{ $badge }} bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300">
                                    {{ $area['tipo'] }}
                                </span>
                            </div>

                            <div class="mt-4 grid grid-cols-2 gap-3">
                                <div class="rounded-xl bg-white dark:bg-gray-900 px-3 py-3 border border-gray-200 dark:border-gray-700">
                                    <p class="text-xs text-gray-400 dark:text-gray-500 uppercase">Esperando</p>
                                    <p class="mt-1 text-2xl font-bold text-gray-900 dark:text-white">{{ $area['esperando'] }}</p>
                                </div>

                                <div class="rounded-xl bg-white dark:bg-gray-900 px-3 py-3 border border-gray-200 dark:border-gray-700">
                                    <p class="text-xs text-gray-400 dark:text-gray-500 uppercase">En turno</p>
                                    <p class="mt-1 text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $area['en_turno'] }}</p>
                                </div>

                                <div class="rounded-xl bg-white dark:bg-gray-900 px-3 py-3 border border-gray-200 dark:border-gray-700">
                                    <p class="text-xs text-gray-400 dark:text-gray-500 uppercase">Atendidos</p>
                                    <p class="mt-1 text-2xl font-bold text-emerald-600 dark:text-emerald-400">{{ $area['atendidos'] }}</p>
                                </div>

                                <div class="rounded-xl bg-white dark:bg-gray-900 px-3 py-3 border border-gray-200 dark:border-gray-700">
                                    <p class="text-xs text-gray-400 dark:text-gray-500 uppercase">Total hoy</p>
                                    <p class="mt-1 text-2xl font-bold text-gray-900 dark:text-white">{{ $area['total_hoy'] }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>

            {{-- MOVIMIENTOS --}}
            <section class="{{ $card }} overflow-hidden">
                <div class="px-5 sm:px-6 py-5 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Movimientos recientes</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Últimos ingresos y ubicación operativa actual.</p>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead class="bg-gray-50 dark:bg-gray-800/50">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-[0.14em] text-gray-400 dark:text-gray-500">Hora</th>
                                <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-[0.14em] text-gray-400 dark:text-gray-500">Paciente</th>
                                <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-[0.14em] text-gray-400 dark:text-gray-500">Preingreso</th>
                                <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-[0.14em] text-gray-400 dark:text-gray-500">Área actual</th>
                                <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-[0.14em] text-gray-400 dark:text-gray-500">Prioridad</th>
                                <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-[0.14em] text-gray-400 dark:text-gray-500">Estado</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                            @forelse($movimientosRecientes as $mov)
                                <tr class="hover:bg-gray-50/80 dark:hover:bg-gray-800/40 transition">
                                    <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-200">{{ $mov['hora'] }}</td>
                                    <td class="px-6 py-4">
                                        <div class="font-semibold text-gray-900 dark:text-white">{{ $mov['paciente'] }}</div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">CI: {{ $mov['ci'] }}</div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-200">{{ $mov['preingreso'] }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-200">{{ $mov['area'] }}</td>
                                    <td class="px-6 py-4">
                                        @php
                                            $prioridadClass = $mov['prioridad'] === 'urgente'
                                                ? 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300'
                                                : 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300';

                                            $estadoClass = match($mov['estado']) {
                                                'esperando_enfermeria' => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-300',
                                                'en_enfermeria' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300',
                                                'en_area' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300',
                                                'finalizado' => 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300',
                                                'cancelado' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300',
                                                default => 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300',
                                            };
                                        @endphp

                                        <span class="{{ $badge }} {{ $prioridadClass }}">{{ $mov['prioridad'] }}</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="{{ $badge }} {{ $estadoClass }}">
                                            {{ str_replace('_', ' ', $mov['estado']) }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center text-sm text-gray-500 dark:text-gray-400">
                                        No hay movimientos registrados hoy.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (typeof window.ApexCharts === 'undefined') return;

            const commonGrid = {
                borderColor: '#e5e7eb',
                strokeDashArray: 4
            };

            const areaEl = document.querySelector('#chart-pacientes-area');
            if (areaEl) {
                const areaChart = new window.ApexCharts(areaEl, {
                    chart: {
                        type: 'bar',
                        height: 320,
                        toolbar: { show: false }
                    },
                    series: [{
                        name: 'Pacientes',
                        data: @json($chartAreasData)
                    }],
                    xaxis: {
                        categories: @json($chartAreasLabels)
                    },
                    dataLabels: {
                        enabled: true
                    },
                    colors: ['#44B0B3'],
                    grid: commonGrid
                });

                areaChart.render();
            }

            const estadoEl = document.querySelector('#chart-estado-flujo');
            if (estadoEl) {
                const estadoChart = new window.ApexCharts(estadoEl, {
                    chart: {
                        type: 'donut',
                        height: 320
                    },
                    series: @json($chartEstadoData),
                    labels: @json($chartEstadoLabels),
                    colors: ['#f59e0b', '#3b82f6', '#10b981', '#6b7280'],
                    legend: {
                        position: 'bottom'
                    }
                });

                estadoChart.render();
            }

            const horaEl = document.querySelector('#chart-ingresos-hora');
            if (horaEl) {
                const horaChart = new window.ApexCharts(horaEl, {
                    chart: {
                        type: 'area',
                        height: 340,
                        toolbar: { show: false }
                    },
                    series: [{
                        name: 'Ingresos',
                        data: @json($chartHorasData)
                    }],
                    xaxis: {
                        categories: @json($chartHorasLabels)
                    },
                    dataLabels: {
                        enabled: false
                    },
                    stroke: {
                        curve: 'smooth',
                        width: 3
                    },
                    fill: {
                        type: 'gradient',
                        gradient: {
                            shadeIntensity: 1,
                            opacityFrom: 0.35,
                            opacityTo: 0.05
                        }
                    },
                    colors: ['#44B0B3'],
                    grid: commonGrid
                });

                horaChart.render();
            }
        });
    </script>
</x-app-layout>