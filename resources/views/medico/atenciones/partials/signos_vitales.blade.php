{{--
    Partial reutilizable: signos vitales con iconos originales y grid responsivo 2/3
    Uso: @include('medico.atenciones.partials.signos_vitales', ['enfermeria' => $enfermeria])
--}}
@php
    $badge    = 'inline-flex items-center rounded-full px-2.5 py-1 text-[11px] font-bold uppercase tracking-[0.12em]';
    $iconBox  = 'flex-shrink-0 w-8 h-8 rounded-xl bg-gray-50 dark:bg-gray-800 flex items-center justify-center border border-gray-100 dark:border-gray-700';
    $svgClass = 'w-4 h-4 text-gray-400 dark:text-gray-500';

    // Cálculo de IMC
    $imc = null; $imcLabel = null; $imcColor = null;
    if ($enfermeria->peso && $enfermeria->talla && $enfermeria->talla > 0) {
        $imc = round($enfermeria->peso / ($enfermeria->talla ** 2), 1);
        [$imcLabel, $imcColor] = match(true) {
            $imc < 18.5 => ['Bajo peso', 'text-blue-500'],
            $imc < 25   => ['Normal',    'text-emerald-600 dark:text-emerald-400'],
            $imc < 30   => ['Sobrepeso', 'text-amber-600 dark:text-amber-400'],
            default     => ['Obesidad',  'text-red-500'],
        };
    }

    $pcClass = match($enfermeria->prioridad_clinica) {
        'critica' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300',
        'alta'    => 'bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-300',
        'media'   => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-300',
        default   => 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300',
    };
@endphp

<div class="space-y-4">
    {{-- Header compacto --}}
    <div class="flex items-center justify-between">
        <p class="text-[10px] font-black uppercase tracking-[0.15em] text-gray-400">
            Triaje de Enfermería
        </p>
        <span class="{{ $badge }} {{ $pcClass }}">
            {{ $enfermeria->prioridad_clinica }}
        </span>
    </div>

    {{-- GRID DINÁMICO: 2 columnas por defecto, 3 columnas si el contenedor es ancho --}}
    <div class="grid grid-cols-2 lg:grid-cols-2 xl:grid-cols-3 gap-2">

        {{-- Temperatura --}}
        <div class="flex items-center gap-3 px-3 py-2 bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 rounded-2xl">
            <span class="{{ $iconBox }}">
                <svg class="{{ $svgClass }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 14V5a3 3 0 0 0-6 0v9a5 5 0 1 0 6 0Z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 17a1 1 0 1 0 0-2 1 1 0 0 0 0 2Z"/></svg>
            </span>
            <div class="min-w-0">
                <p class="text-[9px] uppercase tracking-tighter text-gray-400">Temperatura</p>
                <p class="text-sm font-bold text-gray-900 dark:text-white leading-none">{{ $enfermeria->temperatura }}<span class="text-[10px] font-normal">°C</span></p>
            </div>
        </div>

        {{-- Presión Arterial --}}
        <div class="flex items-center gap-3 px-3 py-2 bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 rounded-2xl">
            <span class="{{ $iconBox }}">
                <svg class="{{ $svgClass }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20h10M7 20v-5m10 5v-5M3 9l9-6 9 6M4 10v10M20 10v10M10 20v-5h4v5"/></svg>
            </span>
            <div class="min-w-0">
                <p class="text-[9px] uppercase tracking-tighter text-gray-400">Presión</p>
                <p class="text-sm font-bold text-gray-900 dark:text-white leading-none">{{ $enfermeria->presion_arterial }}</p>
            </div>
        </div>

        {{-- Frecuencia Cardiaca --}}
        <div class="flex items-center gap-3 px-3 py-2 bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 rounded-2xl">
            <span class="{{ $iconBox }}">
                <svg class="{{ $svgClass }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12h3l2-9 3 18 3-12 2 6 2-3h3"/></svg>
            </span>
            <div class="min-w-0">
                <p class="text-[9px] uppercase tracking-tighter text-gray-400">F. Cardiaca</p>
                <p class="text-sm font-bold text-gray-900 dark:text-white leading-none">{{ $enfermeria->frecuencia_cardiaca }}<span class="text-[10px] font-normal">lpm</span></p>
            </div>
        </div>

        {{-- Frecuencia Respiratoria --}}
        <div class="flex items-center gap-3 px-3 py-2 bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 rounded-2xl">
            <span class="{{ $iconBox }}">
                <svg class="{{ $svgClass }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v9m0 0c-1.5-1-3-1.5-5-1-2 .6-3 2-3 4s1.5 4 4 4 5-1.5 5-4m-1-3c1.5-1 3-1.5 5-1 2 .6 3 2 3 4s-1.5 4-4 4-5-1.5-5-4"/></svg>
            </span>
            <div class="min-w-0">
                <p class="text-[9px] uppercase tracking-tighter text-gray-400">F. Respiratoria</p>
                <p class="text-sm font-bold text-gray-900 dark:text-white leading-none">{{ $enfermeria->frecuencia_respiratoria }}<span class="text-[10px] font-normal">rpm</span></p>
            </div>
        </div>

        {{-- Saturación --}}
        <div class="flex items-center gap-3 px-3 py-2 bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 rounded-2xl">
            <span class="{{ $iconBox }}">
                <svg class="{{ $svgClass }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 21a9 9 0 1 1 0-18 9 9 0 0 1 0 18Z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.5 11.5 10 13l5.5-5.5"/></svg>
            </span>
            <div class="min-w-0">
                <p class="text-[9px] uppercase tracking-tighter text-gray-400">Saturacion O₂</p>
                <p class="text-sm font-bold text-gray-900 dark:text-white leading-none">{{ $enfermeria->saturacion_oxigeno }}<span class="text-[10px] font-normal">%</span></p>
            </div>
        </div>

        {{-- Peso y Talla combinado para ahorrar espacio --}}
        <div class="flex items-center gap-3 px-3 py-2 bg-[#44B0B3]/5 border border-[#44B0B3]/10 rounded-2xl">
            <span class="{{ $iconBox }} bg-white">
                <svg class="{{ $svgClass }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6h18M3 6a2 2 0 1 0 4 0M3 6a2 2 0 1 1 4 0m11 0a2 2 0 1 0 4 0m-4 0a2 2 0 1 1 4 0M5 6l2 14h10l2-14"/></svg>
            </span>
            <div class="min-w-0">
                <p class="text-[9px] font-bold text-[#44B0B3] uppercase">Peso / Talla</p>
                <p class="text-xs font-bold text-gray-700 dark:text-gray-200">{{ $enfermeria->peso }}k/{{ $enfermeria->talla }}m</p>
            </div>
        </div>

    </div>

    {{-- IMC y Registro --}}
    <div class="flex items-center justify-between border-t border-gray-50 dark:border-gray-800 pt-3">
        @if($imc)
            <div class="flex items-center gap-2">
                <p class="text-[10px] font-black uppercase text-gray-400">IMC:</p>
                <span class="text-xs font-bold text-gray-900 dark:text-white">{{ $imc }}</span>
                <span class="text-[9px] font-bold {{ $imcColor }} uppercase">[{{ $imcLabel }}]</span>
            </div>
        @endif
        
        {{-- RECUPERAMOS LA FECHA AQUÍ --}}
        <div class="text-[9px] text-gray-400 font-bold uppercase">
            {{ $enfermeria->fecha_enfermeria->diffForHumans() }}
        </div>
    </div>

    {{-- Nota si existe --}}
    @if($enfermeria->observacion)
        <div class="p-2 bg-gray-50 dark:bg-gray-800/50 rounded-xl border-l-2 border-[#44B0B3]">
            <p class="text-[11px] text-gray-600 dark:text-gray-300 italic">"{{ $enfermeria->observacion }}"</p>
        </div>
    @endif
</div>