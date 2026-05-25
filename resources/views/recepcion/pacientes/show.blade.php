<x-app-layout>
    @php
        $badge = 'inline-flex items-center rounded-full px-2.5 py-1 text-[11px] font-bold uppercase tracking-[0.12em]';
    @endphp

    <div class="  mx-auto space-y-6">

        {{-- ══════════════════════════════════════════
             CABECERA DEL PACIENTE
        ══════════════════════════════════════════ --}}
        <section class="rounded-[2rem] border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 shadow-sm overflow-hidden">

            {{-- Franja de color superior --}}
            <div class="h-2 w-full bg-gradient-to-r from-[#44B0B3] to-[#6dd5d8]"></div>

            <div class="p-5 sm:p-6">
                <div class="flex flex-col gap-5 xl:flex-row xl:items-start xl:justify-between">

                    {{-- Avatar + datos principales --}}
                    <div class="flex items-start gap-5">
                        {{-- Avatar con inicial --}}
                        <div class="shrink-0 h-16 w-16 rounded-2xl bg-gradient-to-br from-[#44B0B3] to-[#389a9d] flex items-center justify-center shadow-lg shadow-[#44B0B3]/30">
                            <span class="text-2xl font-black text-white">
                                {{ strtoupper(substr($paciente->nombres, 0, 1)) }}
                            </span>
                        </div>

                        <div>
                            <p class="text-xs font-black uppercase tracking-[0.18em] text-gray-400 dark:text-gray-500">
                                Recepción / Pacientes / Historial
                            </p>
                            <h1 class="mt-1 text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white">
                                {{ $paciente->nombre_completo }}
                            </h1>

                            <div class="mt-2 flex flex-wrap items-center gap-x-4 gap-y-1 text-sm text-gray-500 dark:text-gray-400">
                                <span>CI: <span class="font-semibold text-gray-700 dark:text-gray-200">{{ $paciente->ci }}</span></span>
                                @if($paciente->fecha_nacimiento)
                                    <span>·</span>
                                    <span>{{ $paciente->fecha_nacimiento->format('d/m/Y') }} <span class="text-gray-400">({{ $paciente->edad }} años)</span></span>
                                @endif
                                <span>·</span>
                                <span>{{ $paciente->sexo === 'M' ? 'Masculino' : ($paciente->sexo === 'F' ? 'Femenino' : 'Otro') }}</span>
                            </div>

                            <div class="mt-3 flex flex-wrap gap-2">
                                @php
                                    $estadoBadge = $paciente->estado === 'activo'
                                        ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300'
                                        : 'bg-gray-100 text-gray-500 dark:bg-gray-800 dark:text-gray-400';
                                @endphp
                                <span class="{{ $badge }} {{ $estadoBadge }}">
                                    {{ $paciente->estado }}
                                </span>
                                @if($paciente->ingresos->count())
                                    <span class="{{ $badge }} bg-[#44B0B3]/10 text-[#44B0B3]">
                                        {{ $paciente->ingresos->count() }} ingreso{{ $paciente->ingresos->count() !== 1 ? 's' : '' }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Acciones --}}
                    <div class="flex flex-wrap gap-2 shrink-0">
                        <a href="{{ route('recepcion.ingresos.create', ['paciente_id' => $paciente->id]) }}"
                           class="inline-flex items-center gap-2 rounded-2xl bg-[#44B0B3] hover:bg-[#389a9d] text-white font-bold px-5 py-3 shadow-lg shadow-[#44B0B3]/25 transition text-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                            Nuevo ingreso
                        </a>
                        <a href="{{ route('recepcion.pacientes.edit', $paciente) }}"
                           class="inline-flex items-center gap-2 rounded-2xl border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-800 font-semibold px-5 py-3 transition text-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 3.487a2.25 2.25 0 1 1 3.182 3.182L7.5 19.213l-4 1 1-4 12.362-12.726z"/></svg>
                            Editar datos
                        </a>
                        <a href="{{ route('recepcion.pacientes.index') }}"
                           class="inline-flex items-center gap-2 rounded-2xl border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-800 font-semibold px-4 py-3 transition text-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/></svg>
                            Volver
                        </a>
                    </div>
                </div>
            </div>
        </section>

        {{-- ══════════════════════════════════════════
             TARJETAS RESUMEN
        ══════════════════════════════════════════ --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            @php
                $totalIngresos  = $paciente->ingresos->count();
                $ultimoIngreso  = $paciente->ingresos->first();
                $activos        = $paciente->ingresos->whereNotIn('estado', ['finalizado','cancelado'])->count();
                $finalizados    = $paciente->ingresos->where('estado', 'finalizado')->count();
            @endphp

            <div class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 p-4 shadow-sm">
                <p class="text-xs font-black uppercase tracking-[0.14em] text-gray-400 dark:text-gray-500">Total ingresos</p>
                <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">{{ $totalIngresos }}</p>
            </div>

            <div class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 p-4 shadow-sm">
                <p class="text-xs font-black uppercase tracking-[0.14em] text-gray-400 dark:text-gray-500">En curso</p>
                <p class="mt-2 text-3xl font-bold {{ $activos > 0 ? 'text-[#44B0B3]' : 'text-gray-900 dark:text-white' }}">{{ $activos }}</p>
            </div>

            <div class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 p-4 shadow-sm">
                <p class="text-xs font-black uppercase tracking-[0.14em] text-gray-400 dark:text-gray-500">Finalizados</p>
                <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">{{ $finalizados }}</p>
            </div>

            <div class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 p-4 shadow-sm">
                <p class="text-xs font-black uppercase tracking-[0.14em] text-gray-400 dark:text-gray-500">Último ingreso</p>
                <p class="mt-2 text-sm font-bold text-gray-900 dark:text-white">
                    {{ $ultimoIngreso ? optional($ultimoIngreso->fecha_ingreso)->format('d/m/Y') : '—' }}
                </p>
            </div>
        </div>
<div class="">
    {{-- Columna Principal --}}
    <div class="col-span-8">
        {{-- Aquí puedes poner los ingresos o la evolución médica --}}
    </div>

    {{-- Columna Lateral --}}
    <div class="">
        <div class="bg-white dark:bg-gray-900 shadow-sm rounded-3xl p-6 border border-red-100 dark:border-red-900/20">
            {{-- Mantenemos solo esta llamada --}}
            @include('medico.atenciones.partials.alergias', [
                'paciente' => $paciente, 
                'medicamentos' => $medicamentos
            ])
        </div>
    </div>
</div>
        {{-- ══════════════════════════════════════════
             GRID: INFO PERSONAL + OBSERVACIONES
        ══════════════════════════════════════════ --}}
        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

            {{-- Datos de contacto --}}
            <section class="rounded-[2rem] border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 shadow-sm p-5 sm:p-6">
                <h2 class="text-sm font-black uppercase tracking-[0.15em] text-gray-400 dark:text-gray-500 mb-4">
                    Contacto
                </h2>
                <ul class="space-y-3">
                    <li class="flex items-start gap-3">
                        <span class="mt-0.5 shrink-0 h-7 w-7 rounded-xl bg-gray-100 dark:bg-gray-800 flex items-center justify-center">
                            <svg class="w-3.5 h-3.5 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 0 1-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 0 0-1.091-.852H4.5A2.25 2.25 0 0 0 2.25 4.5v2.25z"/></svg>
                        </span>
                        <div>
                            <p class="text-xs text-gray-400 dark:text-gray-500">Teléfono</p>
                            <p class="text-sm font-semibold text-gray-800 dark:text-gray-100">{{ $paciente->telefono ?: '—' }}</p>
                        </div>
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="mt-0.5 shrink-0 h-7 w-7 rounded-xl bg-gray-100 dark:bg-gray-800 flex items-center justify-center">
                            <svg class="w-3.5 h-3.5 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75"/></svg>
                        </span>
                        <div>
                            <p class="text-xs text-gray-400 dark:text-gray-500">Correo</p>
                            <p class="text-sm font-semibold text-gray-800 dark:text-gray-100">{{ $paciente->email ?: '—' }}</p>
                        </div>
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="mt-0.5 shrink-0 h-7 w-7 rounded-xl bg-gray-100 dark:bg-gray-800 flex items-center justify-center">
                            <svg class="w-3.5 h-3.5 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0z"/></svg>
                        </span>
                        <div>
                            <p class="text-xs text-gray-400 dark:text-gray-500">Dirección</p>
                            <p class="text-sm font-semibold text-gray-800 dark:text-gray-100">{{ $paciente->direccion ?: '—' }}</p>
                        </div>
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="mt-0.5 shrink-0 h-7 w-7 rounded-xl bg-gray-100 dark:bg-gray-800 flex items-center justify-center">
                            <svg class="w-3.5 h-3.5 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5"/></svg>
                        </span>
                        <div>
                            <p class="text-xs text-gray-400 dark:text-gray-500">Registrado el</p>
                            <p class="text-sm font-semibold text-gray-800 dark:text-gray-100">
                                {{ optional($paciente->created_at)->format('d/m/Y') }}
                            </p>
                        </div>
                    </li>
                </ul>
            </section>

            {{-- Observaciones --}}
            <section class="xl:col-span-2 rounded-[2rem] border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 shadow-sm p-5 sm:p-6">
                <h2 class="text-sm font-black uppercase tracking-[0.15em] text-gray-400 dark:text-gray-500 mb-4">
                    Observaciones clínicas
                </h2>
                @if($paciente->observacion)
                    <p class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed whitespace-pre-line">
                        {{ $paciente->observacion }}
                    </p>
                @else
                    <div class="flex items-center gap-3 text-sm text-gray-400 dark:text-gray-500">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9z"/></svg>
                        Sin observaciones registradas.
                        <a href="{{ route('recepcion.pacientes.edit', $paciente) }}" class="text-[#44B0B3] hover:underline font-semibold">Agregar</a>
                    </div>
                @endif
            </section>

        </div>

        {{-- ══════════════════════════════════════════
             HISTORIAL DE INGRESOS
        ══════════════════════════════════════════ --}}
        <section class="rounded-[2rem] border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 shadow-sm overflow-hidden">

            <div class="px-5 sm:px-6 py-5 border-b border-gray-200 dark:border-gray-700 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div>
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white">Historial de ingresos</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Todas las visitas registradas del paciente.</p>
                </div>
                <a href="{{ route('recepcion.ingresos.create', ['paciente_id' => $paciente->id]) }}"
                   class="inline-flex items-center gap-2 rounded-2xl bg-[#44B0B3] hover:bg-[#389a9d] text-white font-bold px-4 py-2.5 text-sm shadow-lg shadow-[#44B0B3]/25 transition shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                    Nuevo ingreso
                </a>
            </div>

            @if($paciente->ingresos->count())
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead class="bg-gray-50 dark:bg-gray-800/50">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-[0.14em] text-gray-400 dark:text-gray-500">#</th>
                                <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-[0.14em] text-gray-400 dark:text-gray-500">Fecha</th>
                                <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-[0.14em] text-gray-400 dark:text-gray-500">Tipo</th>
                                <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-[0.14em] text-gray-400 dark:text-gray-500">Prioridad</th>
                                <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-[0.14em] text-gray-400 dark:text-gray-500">Motivo</th>
                                <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-[0.14em] text-gray-400 dark:text-gray-500">Estado</th>
                                <th class="px-6 py-4 text-right text-xs font-black uppercase tracking-[0.14em] text-gray-400 dark:text-gray-500">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                            @foreach($paciente->ingresos as $ingreso)
                                <tr class="hover:bg-gray-50/80 dark:hover:bg-gray-800/40 transition">

                                    <td class="px-6 py-4">
                                        <span class="text-xs font-bold text-gray-500 dark:text-gray-400">
                                            {{ $ingreso->numero_preingreso ?: '—' }}
                                        </span>
                                    </td>

                                    <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-200 whitespace-nowrap">
                                        {{ optional($ingreso->fecha_ingreso)->format('d/m/Y') }}
                                        <div class="text-xs text-gray-400 dark:text-gray-500">
                                            {{ optional($ingreso->fecha_ingreso)->format('H:i') }}
                                        </div>
                                    </td>

                                    <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-200">
                                        {{ $ingreso->tipo_ingreso === 'enfermeria' ? 'Enfermeria' : 'Lab. directo' }}
                                    </td>

                                    <td class="px-6 py-4">
                                        @php
                                            $pc = $ingreso->prioridad_inicial === 'urgente'
                                                ? 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300'
                                                : 'bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-300';
                                        @endphp
                                        <span class="{{ $badge }} {{ $pc }}">{{ $ingreso->prioridad_inicial }}</span>
                                    </td>

                                    <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400 max-w-[200px] truncate">
                                        {{ $ingreso->motivo_ingreso ?: '—' }}
                                    </td>

                                    <td class="px-6 py-4">
                                        @php
                                            $ec = match($ingreso->estado) {
                                                'esperando_enfermeria' => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-300',
                                                'en_enfermeria'        => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300',
                                                'en_area'          => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300',
                                                'finalizado'       => 'bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-300',
                                                'cancelado'        => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300',
                                                default            => 'bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-300',
                                            };
                                        @endphp
                                        <span class="{{ $badge }} {{ $ec }}">
                                            {{ str_replace('_', ' ', $ingreso->estado) }}
                                        </span>
                                    </td>

                                    <td class="px-6 py-4 text-right">
                                        <a href="{{ route('recepcion.ingresos.show', $ingreso) }}"
                                           class="inline-flex items-center rounded-xl px-3 py-2 text-xs font-semibold text-[#44B0B3] hover:bg-[#44B0B3]/10 transition">
                                            Ver detalle →
                                        </a>
                                    </td>

                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="px-6 py-12 text-center">
                    <div class="mx-auto h-14 w-14 rounded-2xl bg-gray-100 dark:bg-gray-800 flex items-center justify-center mb-4">
                        <svg class="w-7 h-7 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z"/>
                        </svg>
                    </div>
                    <p class="text-base font-semibold text-gray-700 dark:text-gray-200">Sin ingresos registrados</p>
                    <p class="mt-1 text-sm text-gray-400 dark:text-gray-500">Este paciente no tiene visitas en el sistema todavía.</p>
                    <a href="{{ route('recepcion.ingresos.create', ['paciente_id' => $paciente->id]) }}"
                       class="mt-4 inline-flex items-center gap-2 rounded-2xl bg-[#44B0B3] hover:bg-[#389a9d] text-white font-bold px-5 py-3 text-sm shadow-lg shadow-[#44B0B3]/25 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                        Registrar primer ingreso
                    </a>
                </div>
            @endif

        </section>

        {{-- ══════════════════════════════════════════
             ÚLTIMO ENFERMERIA (si existe)
        ══════════════════════════════════════════ --}}
        @php
            $ultimoEnfermeria = $paciente->ingresos
                ->flatMap(fn($i) => $i->relationLoaded('enfermeria') ? $i-> enfermeria : collect())
                ->sortByDesc('fecha_enfermeria')
                ->first();
        @endphp

        @if($ultimoEnfermeria)
        <section class="rounded-[2rem] border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 shadow-sm p-5 sm:p-6">
            <h2 class="text-sm font-black uppercase tracking-[0.15em] text-gray-400 dark:text-gray-500 mb-4">
                Último enfermeria — {{ optional($ultimoEnfermeria->fecha_enfermeria)->format('d/m/Y H:i') }}
            </h2>
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-6 gap-4">
                @foreach([
                    ['Temperatura',    $ultimoEnfermeria->temperatura ? $ultimoEnfermeria->temperatura . ' °C' : '—'],
                    ['Presión',        $ultimoEnfermeria->presion_arterial ?: '—'],
                    ['Frec. cardíaca', $ultimoEnfermeria->frecuencia_cardiaca ? $ultimoEnfermeria->frecuencia_cardiaca . ' lpm' : '—'],
                    ['Saturación O₂',  $ultimoEnfermeria->saturacion_oxigeno ? $ultimoEnfermeria->saturacion_oxigeno . ' %' : '—'],
                    ['Peso',           $ultimoEnfermeria->peso ? $ultimoEnfermeria->peso . ' kg' : '—'],
                    ['Talla',          $ultimoEnfermeria->talla ? $ultimoEnfermeria->talla . ' m' : '—'],
                ] as [$label, $value])
                    <div class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50 p-3 text-center">
                        <p class="text-xs text-gray-400 dark:text-gray-500">{{ $label }}</p>
                        <p class="mt-1 text-base font-bold text-gray-900 dark:text-white">{{ $value }}</p>
                    </div>
                @endforeach
            </div>
            @if($ultimoEnfermeria->observacion)
                <p class="mt-4 text-sm text-gray-500 dark:text-gray-400">
                    <span class="font-semibold text-gray-700 dark:text-gray-300">Obs:</span> {{ $ultimoEnfermeria->observacion }}
                </p>
            @endif
        </section>
        @endif

    </div>
</x-app-layout>