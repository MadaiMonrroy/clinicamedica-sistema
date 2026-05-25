{{--
    Componente: resources/views/recepcion/components/panel-espera.blade.php
    Uso: @include('recepcion.components.panel-espera')
--}}

<div
    x-data="panelEspera()"
    x-init="init()"
    class="rounded-[2rem] border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 shadow-sm overflow-hidden"
>

    {{-- ══ CABECERA ══════════════════════════════════════════════════════ --}}
    <div class="px-6 pt-5 pb-4 border-b border-gray-100 dark:border-gray-800">
        <div class="flex items-start justify-between gap-3">
            <div>
                <p class="text-xs font-black uppercase tracking-[0.18em] text-gray-400 dark:text-gray-500">
                    Tiempo real
                </p>
                <h2 class="mt-0.5 text-xl font-bold text-gray-900 dark:text-white">
                    Mapa de atención
                </h2>
            </div>
            <div class="flex items-center gap-1.5 px-3 py-1.5 rounded-full border border-emerald-200 dark:border-emerald-800 bg-emerald-50 dark:bg-emerald-900/20 flex-shrink-0">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                <span class="text-[10px] font-black uppercase tracking-wider text-emerald-600 dark:text-emerald-400">En vivo</span>
            </div>
        </div>
        <div class="mt-2.5 flex items-center gap-2 text-xs text-gray-400 dark:text-gray-500">
            <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
            </svg>
            <span x-text="fecha || '—'"></span>
            <span class="text-gray-300 dark:text-gray-700">·</span>
            <span>Act: <strong x-text="horaActualizacion || '--:--:--'" class="text-gray-700 dark:text-gray-300 tabular-nums"></strong></span>
            <span class="ml-auto text-gray-300 dark:text-gray-600 tabular-nums" x-text="'↻ ' + countdown + 's'"></span>
        </div>
    </div>

    {{-- ══ CARGA INICIAL ══════════════════════════════════════════════════ --}}
    <div x-show="!inicializado" class="px-6 py-8 text-center">
        <svg class="w-5 h-5 animate-spin text-[#52ABB1] mx-auto mb-2" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
        </svg>
        <p class="text-sm text-gray-400 dark:text-gray-500">Cargando...</p>
    </div>

    {{-- ══ CONTENIDO ══════════════════════════════════════════════════════ --}}
    <div x-show="inicializado" x-cloak class="p-5 space-y-5">

        {{-- ── 1. RESUMEN DEL DÍA ──────────────────────────────────────────── --}}
        <div>
            <p class="text-[10px] font-black uppercase tracking-[0.18em] text-gray-400 dark:text-gray-500 mb-2.5">
                Resumen del día
            </p>
            {{-- Total --}}
            <div class="flex items-center justify-between rounded-2xl border border-gray-100 dark:border-gray-800
                        bg-gray-50 dark:bg-gray-800/60 px-4 py-3 mb-2">
                <div>
                    <p class="text-2xl font-black text-gray-900 dark:text-white tabular-nums" x-text="resumen.total ?? '0'"></p>
                    <p class="text-[10px] font-semibold text-gray-400 dark:text-gray-500 mt-0.5">Total ingresos hoy</p>
                </div>
                <svg class="w-7 h-7 text-gray-200 dark:text-gray-700" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/>
                </svg>
            </div>
            {{-- 4 estados --}}
            <div class="grid grid-cols-4 gap-2">
                <div class="rounded-2xl border border-amber-100 dark:border-amber-900/40 bg-amber-50 dark:bg-amber-900/10 px-3.5 py-2.5">
                    <p class="text-xl font-black text-amber-600 dark:text-amber-400 tabular-nums" x-text="resumen.en_espera ?? '0'"></p>
                    <div class="flex items-center gap-1.5 mt-0.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-amber-400 flex-shrink-0"></span>
                        <p class="text-[10px] font-semibold text-amber-500/80">En espera</p>
                    </div>
                </div>
                <div class="rounded-2xl border border-[#52ABB1]/25 bg-[#52ABB1]/5 dark:bg-[#52ABB1]/10 px-3.5 py-2.5">
                    <p class="text-xl font-black text-[#52ABB1] tabular-nums" x-text="resumen.en_curso ?? '0'"></p>
                    <div class="flex items-center gap-1.5 mt-0.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-[#52ABB1] animate-pulse flex-shrink-0"></span>
                        <p class="text-[10px] font-semibold text-[#52ABB1]/70">En curso</p>
                    </div>
                </div>
                <div class="rounded-2xl border border-emerald-100 dark:border-emerald-900/40 bg-emerald-50 dark:bg-emerald-900/10 px-3.5 py-2.5">
                    <p class="text-xl font-black text-emerald-600 dark:text-emerald-400 tabular-nums" x-text="resumen.finalizados ?? '0'"></p>
                    <div class="flex items-center gap-1.5 mt-0.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 flex-shrink-0"></span>
                        <p class="text-[10px] font-semibold text-emerald-500/80">Finalizados</p>
                    </div>
                </div>
                <div class="rounded-2xl border border-red-100 dark:border-red-900/40 bg-red-50 dark:bg-red-900/10 px-3.5 py-2.5">
                    <p class="text-xl font-black text-red-500 dark:text-red-400 tabular-nums" x-text="resumen.cancelados ?? '0'"></p>
                    <div class="flex items-center gap-1.5 mt-0.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-red-400 flex-shrink-0"></span>
                        <p class="text-[10px] font-semibold text-red-400/80">Cancelados</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── 2. PENDIENTES DE TRIAJE ─────────────────────────────────────── --}}
        <div>
            <p class="text-[10px] font-black uppercase tracking-[0.18em] text-gray-400 dark:text-gray-500 mb-2.5">
                Pendientes de triaje
            </p>
            <div class="flex items-center gap-3 rounded-2xl border px-4 py-3 transition"
                 :class="esperandoEnfermeria > 0
                     ? 'border-amber-200 dark:border-amber-800/60 bg-amber-50 dark:bg-amber-900/10'
                     : 'border-gray-100 dark:border-gray-800 bg-gray-50 dark:bg-gray-800/40'">
                <div class="w-9 h-9 rounded-xl flex items-center justify-center flex-shrink-0"
                     :class="esperandoEnfermeria > 0
                         ? 'bg-amber-100 dark:bg-amber-800/40 text-amber-500'
                         : 'bg-gray-100 dark:bg-gray-700 text-gray-300 dark:text-gray-600'">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-3-3v6m8-3A9 9 0 113 12a9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-bold text-gray-800 dark:text-gray-100">Enfermería</p>
                    <p class="text-[10px] text-gray-400 dark:text-gray-500">Esperando evaluación inicial</p>
                </div>
                <div class="text-right flex-shrink-0">
                    <p class="text-3xl font-black leading-none tabular-nums"
                       :class="esperandoEnfermeria > 0 ? 'text-amber-500 dark:text-amber-400' : 'text-gray-200 dark:text-gray-700'"
                       x-text="esperandoEnfermeria"></p>
                    <p class="text-[9px] font-semibold mt-0.5"
                       :class="esperandoEnfermeria > 0 ? 'text-amber-400/80' : 'text-gray-300 dark:text-gray-600'">pacientes</p>
                </div>
            </div>
        </div>

        {{-- ── 3. PACIENTES POR ÁREA — grid 2 columnas ────────────────────── --}}
        <div>
            <p class="text-[10px] font-black uppercase tracking-[0.18em] text-gray-400 dark:text-gray-500 mb-2.5">
                Pacientes por área
            </p>

            {{-- Sin datos --}}
            <template x-if="areas.length === 0">
                <div class="flex flex-col items-center justify-center py-6 rounded-2xl border border-dashed border-gray-200 dark:border-gray-700">
                    <svg class="w-8 h-8 text-gray-200 dark:text-gray-700 mb-2" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25"/>
                    </svg>
                    <p class="text-sm font-semibold text-gray-400 dark:text-gray-500">Todas las áreas libres</p>
                    <p class="text-[10px] text-gray-300 dark:text-gray-600 mt-0.5">Sin pacientes activos hoy</p>
                </div>
            </template>

            {{-- Grid 2 columnas. Cada área = 1 card --}}
            <div class="grid grid-cols-2 gap-2.5">
                <template x-for="area in areas" :key="area.area_id">

                    <div
                        class="rounded-2xl border overflow-hidden flex flex-col transition-all duration-200"
                        :style="`border-color: ${area.color_border}`"
                    >
                        {{-- ── Cabecera del área ── --}}
                        <div
                            class="flex items-center gap-2 px-3 py-2.5 cursor-default"
                            :style="`background: ${area.color_bg}`"
                        >
                            {{-- Dot color dinámico --}}
                            <span class="w-2 h-2 rounded-full flex-shrink-0"
                                  :style="`background: ${area.color_dot}`"></span>

                            <div class="flex-1 min-w-0">
                                <p class="text-[11px] font-bold text-gray-800 dark:text-gray-100 truncate leading-tight"
                                   x-text="area.area_nombre"></p>
                                <p class="text-[9px] font-mono font-bold text-gray-400 dark:text-gray-500 leading-tight"
                                   x-text="area.area_codigo"></p>
                            </div>

                            {{-- Contadores en espera / en curso --}}
                            <div class="flex items-center gap-1.5 flex-shrink-0">
                                <template x-if="area.en_espera > 0">
                                    <div class="text-center">
                                        <p class="text-sm font-black tabular-nums leading-none text-amber-500 dark:text-amber-400"
                                           x-text="area.en_espera"></p>
                                        <p class="text-[8px] font-semibold text-amber-400/70">espera</p>
                                    </div>
                                </template>

                                <template x-if="area.en_espera > 0 && area.en_turno > 0">
                                    <div class="w-px h-4 bg-gray-300/50 dark:bg-gray-600/40 mx-0.5"></div>
                                </template>

                                <template x-if="area.en_turno > 0">
                                    <div class="text-center">
                                        <p class="text-sm font-black tabular-nums leading-none text-[#52ABB1]"
                                           x-text="area.en_turno"></p>
                                        <p class="text-[8px] font-semibold text-[#52ABB1]/70">curso</p>
                                    </div>
                                </template>

                                <template x-if="area.en_espera === 0 && area.en_turno === 0">
                                    <span class="text-xs font-bold text-gray-300 dark:text-gray-600">—</span>
                                </template>
                            </div>
                        </div>

                        {{-- ── Paciente EN ATENCIÓN (siempre visible si existe) ── --}}
                        <template x-if="area.pacientes_en_curso.length > 0">
                            <div class="border-t border-gray-100 dark:border-gray-800">
                                <template x-for="pac in area.pacientes_en_curso" :key="pac.ticket_id">
                                    <div class="flex items-center gap-2 px-3 py-2
                                                bg-[#52ABB1]/5 dark:bg-[#52ABB1]/8">
                                        <div class="w-5 h-5 rounded-full flex items-center justify-center text-[8px] font-black flex-shrink-0"
                                             style="background:rgba(82,171,177,.15);color:#52ABB1">
                                            <span x-text="pac.iniciales"></span>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-[10px] font-semibold text-gray-800 dark:text-gray-200 truncate leading-tight"
                                               x-text="pac.nombre"></p>
                                            <p class="text-[8px] text-gray-400 dark:text-gray-500 leading-tight"
                                               x-text="pac.numero_ticket"></p>
                                        </div>
                                        <div class="flex items-center gap-1 flex-shrink-0">
                                            <span class="w-1 h-1 rounded-full bg-[#52ABB1] animate-pulse"></span>
                                            <span class="text-[8px] font-bold text-[#52ABB1]">Atención</span>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </template>

                        {{-- ── Cola de espera ── --}}
                        <template x-if="area.pacientes_en_espera.length > 0">
                            <div class="border-t border-gray-100 dark:border-gray-800 flex flex-col">

                                {{-- 1er paciente: siempre visible --}}
                                <div class="flex items-center gap-2 px-3 py-2 border-b border-gray-50 dark:border-gray-800/60">
                                    <div class="w-4 h-4 rounded-full flex items-center justify-center text-[8px] font-black flex-shrink-0
                                                bg-gray-100 dark:bg-gray-700 text-gray-400 dark:text-gray-500">1</div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-[10px] font-semibold text-gray-700 dark:text-gray-300 truncate leading-tight"
                                           x-text="area.pacientes_en_espera[0].nombre"></p>
                                        <p class="text-[8px] text-gray-400 dark:text-gray-500 leading-tight"
                                           x-text="area.pacientes_en_espera[0].numero_ticket
                                               + (area.pacientes_en_espera[0].prioridad === 'urgente' ? ' · ⚡' : area.pacientes_en_espera[0].prioridad === 'critico' ? ' · 🔴' : '')">
                                        </p>
                                    </div>
                                    <span class="inline-flex items-center gap-0.5 text-[8px] font-bold px-1.5 py-0.5 rounded-full flex-shrink-0"
                                          :class="area.pacientes_en_espera[0].prioridad === 'urgente' || area.pacientes_en_espera[0].prioridad === 'critico'
                                              ? 'bg-red-100 dark:bg-red-900/30 text-red-500'
                                              : 'bg-amber-100 dark:bg-amber-900/30 text-amber-600'">
                                        <span class="w-1 h-1 rounded-full"
                                              :class="area.pacientes_en_espera[0].prioridad === 'urgente' || area.pacientes_en_espera[0].prioridad === 'critico'
                                                  ? 'bg-red-500' : 'bg-amber-400'"></span>
                                        Espera
                                    </span>
                                </div>

                                {{-- Resto de pacientes: colapsado por defecto --}}
                                <template x-if="area.pacientes_en_espera.length > 1">
                                    <div>
                                        {{-- Lista expandida --}}
                                        <div
                                            x-show="expandidos.includes(area.area_id)"
                                            x-transition:enter="transition ease-out duration-150"
                                            x-transition:enter-start="opacity-0 -translate-y-1"
                                            x-transition:enter-end="opacity-100 translate-y-0"
                                        >
                                            <template x-for="(pac, idx) in area.pacientes_en_espera.slice(1)" :key="pac.ticket_id">
                                                <div class="flex items-center gap-2 px-3 py-1.5
                                                            border-b border-gray-50 dark:border-gray-800/50 last:border-b-0">
                                                    <div class="w-4 h-4 rounded-full flex items-center justify-center text-[8px] font-black flex-shrink-0
                                                                bg-gray-100 dark:bg-gray-700 text-gray-400 dark:text-gray-500"
                                                         x-text="idx + 2"></div>
                                                    <div class="flex-1 min-w-0">
                                                        <p class="text-[10px] font-semibold text-gray-700 dark:text-gray-300 truncate leading-tight"
                                                           x-text="pac.nombre"></p>
                                                        <p class="text-[8px] text-gray-400 dark:text-gray-500 leading-tight"
                                                           x-text="pac.numero_ticket + (pac.prioridad === 'urgente' ? ' · ⚡' : pac.prioridad === 'critico' ? ' · 🔴' : '')"></p>
                                                    </div>
                                                    <span class="inline-flex items-center gap-0.5 text-[8px] font-bold px-1.5 py-0.5 rounded-full flex-shrink-0"
                                                          :class="pac.prioridad === 'urgente' || pac.prioridad === 'critico'
                                                              ? 'bg-red-100 dark:bg-red-900/30 text-red-500'
                                                              : 'bg-amber-100 dark:bg-amber-900/30 text-amber-600'">
                                                        <span class="w-1 h-1 rounded-full"
                                                              :class="pac.prioridad === 'urgente' || pac.prioridad === 'critico'
                                                                  ? 'bg-red-500' : 'bg-amber-400'"></span>
                                                        Espera
                                                    </span>
                                                </div>
                                            </template>
                                        </div>

                                        {{-- Botón expandir / colapsar --}}
                                        <button
                                            type="button"
                                            @click="toggleExpandido(area.area_id)"
                                            class="w-full flex items-center justify-center gap-1 py-1.5
                                                   text-[9px] font-bold transition
                                                   text-gray-400 dark:text-gray-500
                                                   hover:text-gray-600 dark:hover:text-gray-300
                                                   bg-gray-50/80 dark:bg-gray-800/40
                                                   hover:bg-gray-100 dark:hover:bg-gray-700/40
                                                   border-t border-gray-100 dark:border-gray-800"
                                        >
                                            {{-- Icono chevron animado --}}
                                            <svg
                                                class="w-3 h-3 transition-transform duration-200"
                                                :class="expandidos.includes(area.area_id) ? 'rotate-180' : ''"
                                                fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                                            </svg>
                                            <span x-text="expandidos.includes(area.area_id)
                                                ? 'Ocultar'
                                                : '+ ' + (area.pacientes_en_espera.length - 1) + ' más en espera'">
                                            </span>
                                        </button>
                                    </div>
                                </template>

                            </div>
                        </template>

                    </div>

                </template>
            </div>
        </div>

        {{-- ── LEYENDA dinámica ──────────────────────────────────────────────── --}}
        <div class="flex flex-wrap gap-x-3 gap-y-1.5 pt-1 border-t border-gray-100 dark:border-gray-800">
            <template x-for="area in legendaAreas" :key="area.area_tipo">
                <div class="flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full flex-shrink-0" :style="`background: ${area.color_dot}`"></span>
                    <span class="text-[9px] font-medium text-gray-400 dark:text-gray-500 capitalize" x-text="area.area_tipo"></span>
                </div>
            </template>
            <div class="flex items-center gap-3 ml-auto">
                <div class="flex items-center gap-1">
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span>
                    <span class="text-[9px] text-gray-400 dark:text-gray-500">Espera</span>
                </div>
                <div class="flex items-center gap-1">
                    <span class="w-1.5 h-1.5 rounded-full bg-[#52ABB1]"></span>
                    <span class="text-[9px] text-gray-400 dark:text-gray-500">En curso</span>
                </div>
            </div>
        </div>

    </div>
</div>

@once
@push('scripts')
<script>
function panelEspera() {
    return {
        cargando:            true,
        inicializado:        false,
        fecha:               null,
        horaActualizacion:   null,
        esperandoEnfermeria: 0,
        areas:               [],
        expandidos:          [], // array de area_ids que están expandidos
        resumen: { total: 0, en_espera: 0, en_curso: 0, finalizados: 0, cancelados: 0 },
        countdown:  20,
        _interval:  null,
        _countdown: null,

        // Tipos únicos para la leyenda
        get legendaAreas() {
            const seen = new Set();
            return this.areas.filter(a => {
                if (seen.has(a.area_tipo)) return false;
                seen.add(a.area_tipo);
                return true;
            });
        },

        // Toggle expandir/colapsar una área
        toggleExpandido(areaId) {
            if (this.expandidos.includes(areaId)) {
                this.expandidos = this.expandidos.filter(id => id !== areaId);
            } else {
                this.expandidos.push(areaId);
            }
        },

        init() {
            this.fetchData();
            this._interval  = setInterval(() => this.fetchData(), 20_000);
            this._countdown = setInterval(() => {
                this.countdown--;
                if (this.countdown <= 0) this.countdown = 20;
            }, 1_000);
        },

        async fetchData() {
            this.cargando  = true;
            this.countdown = 20;
            try {
                const res = await fetch('{{ route('recepcion.espera.panel') }}', {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    },
                });
                if (!res.ok) throw new Error('HTTP ' + res.status);
                const data             = await res.json();
                this.fecha               = data.fecha;
                this.horaActualizacion   = data.hora_actualizacion;
                this.esperandoEnfermeria = data.esperando_enfermeria ?? 0;
                this.areas               = data.areas ?? [];
                this.resumen             = data.resumen_dia ?? this.resumen;
                this.inicializado        = true;
            } catch (e) {
                console.error('Panel espera error:', e);
            } finally {
                this.cargando = false;
            }
        },
    };
}
</script>
@endpush
@endonce

<style>
[x-cloak] { display: none !important; }
</style>