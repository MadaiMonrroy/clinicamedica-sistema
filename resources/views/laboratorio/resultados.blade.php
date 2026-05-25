<x-app-layout>
@php
    $card       = 'rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 shadow-sm';
    $badge      = 'inline-flex items-center rounded-full px-2.5 py-1 text-[11px] font-bold uppercase tracking-[0.12em]';
    $inputClass = 'block w-full px-4 py-3 rounded-2xl text-sm
                   bg-white dark:bg-gray-800
                   border border-gray-200 dark:border-gray-700
                   text-gray-900 dark:text-white
                   placeholder-gray-400 dark:placeholder-gray-500
                   focus:outline-none focus:ring-2 focus:ring-[#52ABB1]/40 focus:border-[#52ABB1]
                   transition';
    $ticketCerrado = $ticket->ingreso->solicitudesLab->every(fn($s) => $s->estado === 'completado');
@endphp

    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('laboratorio.index') }}"
               class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/>
                </svg>
            </a>
            <div class="flex-1">
                <div class="flex items-center gap-2">
                    <span class="font-mono font-bold text-[#52ABB1] dark:text-[#6dbec4] text-lg">
                        {{ $ticket->numero_ticket }}
                    </span>
                    <span class="h-4 w-px bg-gray-200 dark:bg-gray-700"></span>
                    <span class="font-semibold text-gray-800 dark:text-gray-200">
                        {{ $ticket->paciente->nombres }} {{ $ticket->paciente->apellido_paterno }}
                    </span>
                </div>
                <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">Subir y gestionar resultados</p>
            </div>
            @if($ticket->adjuntosLab->count() > 0 && !$ticketCerrado && $ticket->estado === 'atendido')
            {{-- Formulario oculto cerrar — se envía desde el modal --}}
            <form id="form-cerrar-ticket" method="POST"
                  action="{{ route('laboratorio.cerrar', $ticket) }}" class="hidden">
                @csrf
            </form>
            <button type="button" onclick="abrirModalCerrar()"
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-xl
                           bg-green-500 hover:bg-green-600 text-white
                           font-bold text-sm transition-colors shadow-sm shadow-green-500/20">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Cerrar ticket
            </button>
            @endif
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-screen-2xl mx-auto sm:px-6 lg:px-8 space-y-5">

            {{-- FLASH --}}
            @if(session('success'))
            <div class="flex items-center gap-3 px-5 py-4 rounded-2xl bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-800 dark:text-green-300">
                <svg class="w-5 h-5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd"/>
                </svg>
                <span class="text-sm font-medium">{{ session('success') }}</span>
            </div>
            @endif
            @if(session('error'))
            <div class="flex items-center gap-3 px-5 py-4 rounded-2xl bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-800 dark:text-red-300">
                <svg class="w-5 h-5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd"/>
                </svg>
                <span class="text-sm font-medium">{{ session('error') }}</span>
            </div>
            @endif

            {{-- GRID: [Paciente/Exámenes 3col] [Subir/Lista 4col] [Visor PDF 5col] --}}
            <div class="grid grid-cols-1 xl:grid-cols-12 gap-5 items-start">

                {{-- ── COL 1: Paciente + Exámenes ── --}}
                <div class="xl:col-span-3 space-y-4">
                    <div class="{{ $card }} p-5">
                        <p class="text-xs font-black uppercase tracking-[0.18em] text-gray-400 dark:text-gray-500 mb-3">Paciente</p>
                        <p class="font-semibold text-gray-900 dark:text-white">
                            {{ $ticket->paciente->nombres }} {{ $ticket->paciente->apellido_paterno }}
                            {{ $ticket->paciente->apellido_materno }}
                        </p>
                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">
                            CI: {{ $ticket->paciente->ci }} ·
                            {{ \Carbon\Carbon::parse($ticket->paciente->fecha_nacimiento)->age }} años
                        </p>
                        @php $primeraSolicitud = $ticket->ingreso->solicitudesLab->first(); @endphp
                        @if($primeraSolicitud?->observacion_muestra)
                        <div class="mt-3 p-3 rounded-xl bg-blue-50 dark:bg-blue-900/20 border border-blue-100 dark:border-blue-800">
                            <p class="text-[11px] font-bold uppercase tracking-wide text-blue-600 dark:text-blue-400 mb-1">Obs. muestra</p>
                            <p class="text-xs text-blue-700 dark:text-blue-300">{{ $primeraSolicitud->observacion_muestra }}</p>
                        </div>
                        @endif
                        @if($primeraSolicitud?->muestra_tomada_at)
                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-2">
                            Muestra: {{ \Carbon\Carbon::parse($primeraSolicitud->muestra_tomada_at)->format('d/m/Y H:i') }}
                        </p>
                        @endif
                    </div>

                    <div class="{{ $card }} p-5">
                        <p class="text-xs font-black uppercase tracking-[0.18em] text-gray-400 dark:text-gray-500 mb-3">
                            Exámenes ({{ $ticket->ingreso->solicitudesLab->count() }})
                        </p>
                        <ul class="space-y-2">
                            @foreach($ticket->ingreso->solicitudesLab as $sol)
                            @php $b = $sol->estadoBadge(); @endphp
                            <li class="flex items-center justify-between gap-2">
                                <div class="flex items-center gap-2 min-w-0">
                                    <span class="font-mono text-[11px] text-gray-400 dark:text-gray-500 shrink-0">{{ $sol->examen->cod_examen }}</span>
                                    <span class="text-sm text-gray-700 dark:text-gray-300 truncate">{{ $sol->examen->nombre_examen }}</span>
                                </div>
                                <span class="{{ $badge }} shrink-0
                                    @switch($b['color'])
                                        @case('yellow') bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-300 @break
                                        @case('blue')   bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 @break
                                        @case('green')  bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300 @break
                                        @default        bg-gray-100 dark:bg-gray-800 text-gray-500 dark:text-gray-400
                                    @endswitch">
                                    {{ $b['label'] }}
                                </span>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                </div>

                {{-- ── COL 2: Subir + Lista archivos ── --}}
                <div class="xl:col-span-4 space-y-4">

                    @unless($ticketCerrado)
                    <div class="{{ $card }} p-5">
                        <p class="text-xs font-black uppercase tracking-[0.18em] text-gray-400 dark:text-gray-500 mb-4">Subir resultado</p>
                        <form method="POST" action="{{ route('laboratorio.subir-resultado', $ticket) }}"
                              enctype="multipart/form-data" class="space-y-4">
                            @csrf
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">
                                    Nombre del archivo <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="nombre_archivo" required
                                       placeholder="Ej: Hemograma y Bioquímica..."
                                       value="{{ old('nombre_archivo') }}"
                                       class="{{ $inputClass }}">
                                @error('nombre_archivo')<p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">
                                    Archivo <span class="text-red-500">*</span>
                                    <span class="text-gray-400 font-normal text-xs">(PDF, JPG, PNG · máx. 10MB)</span>
                                </label>
                                <div id="drop-zone"
                                     class="rounded-2xl border-2 border-dashed border-gray-200 dark:border-gray-700
                                            hover:border-[#52ABB1] dark:hover:border-[#52ABB1]
                                            p-6 text-center cursor-pointer transition-colors">
                                    <input type="file" name="archivo" id="archivo-input" required
                                           accept=".pdf,.jpg,.jpeg,.png" class="hidden">
                                    <div id="drop-placeholder">
                                        <svg class="w-8 h-8 text-gray-300 dark:text-gray-600 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">
                                            Arrastra o <button type="button" onclick="document.getElementById('archivo-input').click()"
                                                class="text-[#52ABB1] font-semibold hover:underline">selecciona</button>
                                        </p>
                                    </div>
                                    <div id="file-selected" class="hidden">
                                        <svg class="w-7 h-7 text-[#52ABB1] mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        <p id="file-name" class="text-sm font-semibold text-gray-800 dark:text-gray-200"></p>
                                        <p id="file-size" class="text-xs text-gray-400 mt-0.5"></p>
                                        <button type="button" onclick="clearFile()" class="mt-1 text-xs text-red-500 hover:underline">Quitar</button>
                                    </div>
                                </div>
                                @error('archivo')<p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">
                                    Observación <span class="text-gray-400 font-normal">(opcional)</span>
                                </label>
                                <textarea name="observacion" rows="2"
                                          placeholder="Ej: Incluye hemograma y glucosa..."
                                          class="{{ $inputClass }} resize-none">{{ old('observacion') }}</textarea>
                            </div>
                            <button type="submit"
                                    class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl
                                           bg-[#52ABB1] hover:bg-[#3d8f95] text-white
                                           font-bold text-sm transition-colors shadow-sm shadow-[#52ABB1]/20">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                                </svg>
                                Subir resultado
                            </button>
                        </form>
                    </div>
                    @endunless

                    {{-- Lista archivos --}}
                    <div class="{{ $card }} p-5">
                        <div class="flex items-center justify-between mb-4">
                            <p class="text-xs font-black uppercase tracking-[0.18em] text-gray-400 dark:text-gray-500">Resultados subidos</p>
                            <span class="{{ $badge }} {{ $ticket->adjuntosLab->count() > 0 ? 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300' : 'bg-gray-100 dark:bg-gray-800 text-gray-500 dark:text-gray-400' }}">
                                {{ $ticket->adjuntosLab->count() }} archivo(s)
                            </span>
                        </div>

                        @if($ticket->adjuntosLab->isEmpty())
                        <div class="text-center py-8">
                            <svg class="w-10 h-10 text-gray-200 dark:text-gray-700 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <p class="text-sm text-gray-400 dark:text-gray-500">Aún no se han subido resultados</p>
                        </div>
                        @else
                        <ul class="space-y-2">
                            @foreach($ticket->adjuntosLab as $adj)
                            @php $ext = strtolower(pathinfo($adj->ruta_archivo, PATHINFO_EXTENSION)); @endphp
                            <li class="flex items-center gap-3 p-3 rounded-xl
                                       bg-gray-50 dark:bg-gray-800/60 border border-gray-100 dark:border-gray-700
                                       hover:border-[#52ABB1]/40 transition-colors cursor-pointer group"
                                onclick="{{ $ext === 'pdf'
                                    ? "abrirVisor('".Storage::url($adj->ruta_archivo)."', '".addslashes($adj->nombre_archivo)."')"
                                    : "window.open('".Storage::url($adj->ruta_archivo)."','_blank')" }}">
                                <div class="w-9 h-9 rounded-lg flex items-center justify-center shrink-0
                                    {{ $ext === 'pdf' ? 'bg-red-100 dark:bg-red-900/30' : 'bg-blue-100 dark:bg-blue-900/30' }}">
                                    @if($ext === 'pdf')
                                    <svg class="w-4 h-4 text-red-500 dark:text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"/>
                                    </svg>
                                    @else
                                    <svg class="w-4 h-4 text-blue-500 dark:text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"/>
                                    </svg>
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-semibold text-gray-800 dark:text-gray-200 truncate group-hover:text-[#52ABB1] transition-colors">
                                        {{ $adj->nombre_archivo }}
                                    </p>
                                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">
                                        {{ \Carbon\Carbon::parse($adj->fecha_subida)->format('d/m H:i') }}
                                        @if($adj->observacion) · {{ Str::limit($adj->observacion, 25) }}@endif
                                    </p>
                                </div>
                                <div class="flex items-center gap-1 shrink-0" onclick="event.stopPropagation()">
                                    <a href="{{ Storage::url($adj->ruta_archivo) }}" target="_blank"
                                       class="p-1.5 rounded-lg text-gray-400 hover:text-[#52ABB1] hover:bg-[#52ABB1]/10 transition-colors"
                                       title="Abrir en pestaña">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                        </svg>
                                    </a>
                                    @if($adj->estado === 'subido' && !$ticketCerrado)
                                    {{-- Formulario oculto — se envía desde el modal --}}
                                    <form id="form-eliminar-{{ $adj->id }}"
                                          method="POST"
                                          action="{{ route('laboratorio.eliminar-adjunto', $adj) }}"
                                          class="hidden">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                    {{-- Botón que abre el modal --}}
                                    <button type="button"
                                            onclick="confirmarEliminar({{ $adj->id }}, '{{ addslashes($adj->nombre_archivo) }}')"
                                            class="p-1.5 rounded-lg text-gray-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors"
                                            title="Eliminar archivo">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                    @endif
                                </div>
                            </li>
                            @endforeach
                        </ul>

                        @if($ticketCerrado)
                        <div class="mt-3 flex items-center gap-2 p-3.5 rounded-xl bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800">
                            <svg class="w-4 h-4 text-green-600 dark:text-green-400 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd"/>
                            </svg>
                            <p class="text-xs font-semibold text-green-700 dark:text-green-300">Ticket cerrado — resultados entregados.</p>
                        </div>
                        @endif
                        @endif {{-- cierra @if($ticket->adjuntosLab->isEmpty()) --}}
                    </div>

                    @if($ticket->adjuntosLab->count() > 0 && !$ticketCerrado && $ticket->estado === 'atendido')
                    <div class="{{ $card }} p-4 border-green-200 dark:border-green-800/40">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl bg-green-100 dark:bg-green-900/30 flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <p class="font-semibold text-gray-900 dark:text-white text-sm">¿Todos los resultados listos?</p>
                                <button type="button" onclick="abrirModalCerrar()"
                                        class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-green-500 hover:bg-green-600 text-white font-bold text-sm transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    Cerrar y entregar resultados
                                </button>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>

                {{-- ── COL 3: VISOR PDF (panel derecho fijo) ── --}}
                <div class="xl:col-span-5">
                    <div class="rounded-2xl overflow-hidden border border-gray-200 dark:border-gray-700 shadow-sm sticky top-6"
                         style="height: calc(100vh - 140px);">

                        {{-- Barra del visor --}}
                        <div class="flex items-center justify-between px-4 py-3
                                    bg-gray-900 dark:bg-gray-950 border-b border-gray-700 shrink-0">
                            <div class="flex items-center gap-2 min-w-0 flex-1">
                                <div class="w-6 h-6 rounded-md bg-red-500/20 flex items-center justify-center shrink-0">
                                    <svg class="w-3.5 h-3.5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <span id="visor-titulo" class="text-xs font-semibold text-gray-400 truncate">
                                    Selecciona un PDF para visualizarlo
                                </span>
                            </div>

                            <div id="visor-controles" class="hidden items-center gap-1.5 ml-2 shrink-0">
                                <div class="flex items-center gap-0.5 bg-gray-800 rounded-lg px-1.5 py-1">
                                    <button onclick="paginaAnterior()" id="btn-prev"
                                            class="p-0.5 rounded text-gray-400 hover:text-white transition-colors disabled:opacity-30" disabled>
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                                        </svg>
                                    </button>
                                    <span class="text-[11px] text-gray-300 px-1.5 min-w-[50px] text-center whitespace-nowrap">
                                        <span id="pagina-actual">1</span> / <span id="total-paginas">1</span>
                                    </span>
                                    <button onclick="paginaSiguiente()" id="btn-next"
                                            class="p-0.5 rounded text-gray-400 hover:text-white transition-colors disabled:opacity-30" disabled>
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                        </svg>
                                    </button>
                                </div>
                                <div class="flex items-center gap-0.5 bg-gray-800 rounded-lg px-1.5 py-1">
                                    <button onclick="zoomOut()" class="p-0.5 rounded text-gray-400 hover:text-white transition-colors">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                                        </svg>
                                    </button>
                                    <span id="zoom-label" class="text-[11px] text-gray-300 min-w-[36px] text-center">120%</span>
                                    <button onclick="zoomIn()" class="p-0.5 rounded text-gray-400 hover:text-white transition-colors">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                        </svg>
                                    </button>
                                </div>
                                <a id="btn-abrir-tab" href="#" target="_blank"
                                   class="p-1.5 rounded-lg text-gray-400 hover:text-[#52ABB1] hover:bg-[#52ABB1]/10 transition-colors"
                                   title="Abrir en pestaña nueva">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                    </svg>
                                </a>
                            </div>
                        </div>

                        {{-- Área de renderizado --}}
                        <div id="visor-scroll"
                             class="overflow-auto bg-gray-100 dark:bg-gray-800 flex flex-col items-center justify-center p-4"
                             style="height: calc(100% - 45px);">

                            {{-- Placeholder --}}
                            <div id="visor-placeholder" class="flex flex-col items-center gap-3 text-center">
                                <div class="w-16 h-16 rounded-2xl bg-gray-200 dark:bg-gray-700 flex items-center justify-center">
                                    <svg class="w-8 h-8 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Sin archivo seleccionado</p>
                                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">
                                        Haz clic en un PDF de la lista para verlo aquí
                                    </p>
                                </div>
                            </div>

                            {{-- Loading --}}
                            <div id="visor-loading" class="hidden flex-col items-center gap-3">
                                <div class="w-8 h-8 border-2 border-[#52ABB1] border-t-transparent rounded-full animate-spin"></div>
                                <p class="text-xs text-gray-400 dark:text-gray-500">Cargando PDF...</p>
                            </div>

                            {{-- Error --}}
                            <div id="visor-error" class="hidden flex-col items-center gap-2 text-center p-4">
                                <svg class="w-8 h-8 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                </svg>
                                <p class="text-xs text-red-400 font-medium">No se pudo cargar el archivo.</p>
                                <p class="text-xs text-gray-400 dark:text-gray-500">
                                    Asegúrate de haber ejecutado:<br>
                                    <code class="mt-1 inline-block bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 px-2 py-0.5 rounded text-[11px]">
                                        php artisan storage:link
                                    </code>
                                </p>
                            </div>

                            {{-- Canvas --}}
                            <canvas id="pdf-canvas" class="hidden shadow-lg" style="max-width:100%;"></canvas>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════
         MODAL CONFIRMACIÓN CERRAR TICKET
    ══════════════════════════════════════════ --}}
    <div id="modal-cerrar"
         class="fixed inset-0 z-50 hidden items-center justify-center p-4"
         role="dialog" aria-modal="true">

        {{-- Backdrop --}}
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm"
             onclick="cerrarModalCerrar()"></div>

        {{-- Panel --}}
        <div class="relative z-10 w-full max-w-md bg-white dark:bg-gray-900
                    rounded-2xl border border-gray-200 dark:border-gray-700
                    shadow-2xl shadow-black/20 overflow-hidden">

            {{-- Franja verde superior --}}
            <div class="h-1 w-full bg-gradient-to-r from-green-500 to-green-400"></div>

            <div class="p-6">
                {{-- Ícono --}}
                <div class="flex items-center gap-4 mb-5">
                    <div class="w-12 h-12 rounded-2xl bg-green-100 dark:bg-green-900/30
                                flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-900 dark:text-white text-base">
                            Cerrar y entregar resultados
                        </h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                            Ticket <span class="font-mono font-bold text-[#52ABB1]">{{ $ticket->numero_ticket }}</span>
                            · {{ $ticket->paciente->nombres }} {{ $ticket->paciente->apellido_paterno }}
                        </p>
                    </div>
                </div>

                {{-- Info --}}
                <div class="space-y-3 mb-6">
                    {{-- Archivos que se entregarán --}}
                    <div class="p-4 rounded-xl bg-green-50 dark:bg-green-900/20
                                border border-green-200 dark:border-green-800">
                        <p class="text-xs font-bold uppercase tracking-wide text-green-700 dark:text-green-300 mb-2">
                            Archivos que se marcarán como entregados:
                        </p>
                        <ul class="space-y-1">
                            @foreach($ticket->adjuntosLab as $adj)
                            <li class="flex items-center gap-2 text-sm text-green-700 dark:text-green-300">
                                <svg class="w-3.5 h-3.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd"/>
                                </svg>
                                {{ $adj->nombre_archivo }}
                            </li>
                            @endforeach
                        </ul>
                    </div>

                    {{-- Advertencia acción irreversible --}}
                    <div class="flex items-start gap-3 p-3.5 rounded-xl
                                bg-yellow-50 dark:bg-yellow-900/20
                                border border-yellow-200 dark:border-yellow-800">
                        <svg class="w-4 h-4 text-yellow-500 shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495zM10 5a.75.75 0 01.75.75v3.5a.75.75 0 01-1.5 0v-3.5A.75.75 0 0110 5zm0 9a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"/>
                        </svg>
                        <p class="text-xs text-yellow-700 dark:text-yellow-300">
                            Esta acción cerrará el ticket definitivamente. Ya no podrás agregar ni eliminar archivos.
                            El ingreso quedará marcado como <strong>finalizado</strong>.
                        </p>
                    </div>
                </div>

                {{-- Botones --}}
                <div class="flex items-center justify-end gap-3">
                    <button type="button"
                            onclick="cerrarModalCerrar()"
                            class="px-4 py-2.5 rounded-xl text-sm font-semibold
                                   text-gray-600 dark:text-gray-400
                                   hover:bg-gray-100 dark:hover:bg-gray-800
                                   border border-gray-200 dark:border-gray-700
                                   transition-colors">
                        Cancelar
                    </button>
                    <button type="button"
                            id="btn-confirmar-cerrar"
                            onclick="ejecutarCerrar()"
                            class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl
                                   text-sm font-bold text-white
                                   bg-green-600 hover:bg-green-700
                                   transition-colors shadow-sm shadow-green-600/20">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Confirmar entrega y cerrar
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════
         MODAL CONFIRMACIÓN ELIMINACIÓN
    ══════════════════════════════════════════ --}}
    <div id="modal-eliminar"
         class="fixed inset-0 z-50 hidden items-center justify-center p-4"
         role="dialog" aria-modal="true">

        {{-- Backdrop --}}
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm"
             onclick="cerrarModalEliminar()"></div>

        {{-- Panel --}}
        <div class="relative z-10 w-full max-w-md bg-white dark:bg-gray-900
                    rounded-2xl border border-gray-200 dark:border-gray-700
                    shadow-2xl shadow-black/20 overflow-hidden">

            {{-- Franja roja superior --}}
            <div class="h-1 w-full bg-gradient-to-r from-red-500 to-red-400"></div>

            <div class="p-6">
                {{-- Ícono --}}
                <div class="flex items-center gap-4 mb-5">
                    <div class="w-12 h-12 rounded-2xl bg-red-100 dark:bg-red-900/30
                                flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-900 dark:text-white text-base">
                            Eliminar archivo
                        </h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                            Esta acción no se puede deshacer
                        </p>
                    </div>
                </div>

                {{-- Advertencia --}}
                <div class="flex items-start gap-3 p-4 rounded-xl
                            bg-red-50 dark:bg-red-900/20
                            border border-red-200 dark:border-red-800 mb-5">
                    <svg class="w-4 h-4 text-red-500 dark:text-red-400 shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495zM10 5a.75.75 0 01.75.75v3.5a.75.75 0 01-1.5 0v-3.5A.75.75 0 0110 5zm0 9a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"/>
                    </svg>
                    <div>
                        <p class="text-sm font-semibold text-red-700 dark:text-red-300">
                            El siguiente archivo será eliminado definitivamente:
                        </p>
                        <p id="modal-nombre-archivo"
                           class="text-sm text-red-600 dark:text-red-400 font-mono mt-1 break-all">
                        </p>
                        <p class="text-xs text-red-500 dark:text-red-400 mt-2">
                            El archivo se borrará del servidor y no podrá recuperarse.
                        </p>
                    </div>
                </div>

                {{-- Botones --}}
                <div class="flex items-center justify-end gap-3">
                    <button type="button"
                            onclick="cerrarModalEliminar()"
                            class="px-4 py-2.5 rounded-xl text-sm font-semibold
                                   text-gray-600 dark:text-gray-400
                                   hover:bg-gray-100 dark:hover:bg-gray-800
                                   border border-gray-200 dark:border-gray-700
                                   transition-colors">
                        Cancelar
                    </button>
                    <button type="button"
                            id="btn-confirmar-eliminar"
                            onclick="ejecutarEliminar()"
                            class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl
                                   text-sm font-bold text-white
                                   bg-red-600 hover:bg-red-700
                                   transition-colors shadow-sm shadow-red-600/20">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        Sí, eliminar definitivamente
                    </button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
    <script>
    pdfjsLib.GlobalWorkerOptions.workerSrc =
        'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';

    // ── Modal cerrar ticket ───────────────────────────────────
    const modalCerrar = document.getElementById('modal-cerrar');

    function abrirModalCerrar() {
        modalCerrar.classList.remove('hidden');
        modalCerrar.classList.add('flex');
        document.body.style.overflow = 'hidden';
    }

    function cerrarModalCerrar() {
        modalCerrar.classList.add('hidden');
        modalCerrar.classList.remove('flex');
        document.body.style.overflow = '';
    }

    function ejecutarCerrar() {
        const btn = document.getElementById('btn-confirmar-cerrar');
        btn.disabled = true;
        btn.innerHTML = `
            <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
            </svg>
            Cerrando...`;
        document.getElementById('form-cerrar-ticket').submit();
    }

    // ── Modal eliminación ─────────────────────────────────────
    let formIdActivo = null;
    const modalEliminar = document.getElementById('modal-eliminar');

    function confirmarEliminar(adjId, nombreArchivo) {
        formIdActivo = adjId;
        document.getElementById('modal-nombre-archivo').textContent = nombreArchivo;
        modalEliminar.classList.remove('hidden');
        modalEliminar.classList.add('flex');
        document.body.style.overflow = 'hidden';
    }

    function cerrarModalEliminar() {
        modalEliminar.classList.add('hidden');
        modalEliminar.classList.remove('flex');
        document.body.style.overflow = '';
        formIdActivo = null;
    }

    function ejecutarEliminar() {
        if (!formIdActivo) return;
        const btn = document.getElementById('btn-confirmar-eliminar');
        btn.disabled = true;
        btn.innerHTML = `
            <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
            </svg>
            Eliminando...`;
        document.getElementById('form-eliminar-' + formIdActivo).submit();
    }

    // Cerrar con Escape (ambos modales)
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') {
            if (!modalCerrar.classList.contains('hidden'))   cerrarModalCerrar();
            if (!modalEliminar.classList.contains('hidden')) cerrarModalEliminar();
        }
    });

    // ── Estado ────────────────────────────────────────────────
    let pdfDoc     = null;
    let paginaNum  = 1;
    let escala     = 1.2;
    let renderTask = null;

    const canvas   = document.getElementById('pdf-canvas');
    const ctx      = canvas.getContext('2d');
    const elTitulo = document.getElementById('visor-titulo');
    const elCtrls  = document.getElementById('visor-controles');
    const elLoad   = document.getElementById('visor-loading');
    const elErr    = document.getElementById('visor-error');
    const elPlace  = document.getElementById('visor-placeholder');
    const elScroll = document.getElementById('visor-scroll');
    const elPagAct = document.getElementById('pagina-actual');
    const elPagTot = document.getElementById('total-paginas');
    const elZoom   = document.getElementById('zoom-label');
    const btnPrev  = document.getElementById('btn-prev');
    const btnNext  = document.getElementById('btn-next');
    const btnTab   = document.getElementById('btn-abrir-tab');

    // Muestra solo el elemento indicado dentro del área de scroll
    function mostrar(el) {
        [elLoad, elErr, elPlace, canvas].forEach(e => {
            e.classList.add('hidden');
            e.classList.remove('flex');
        });
        el.classList.remove('hidden');
        if (el === elLoad || el === elErr || el === elPlace) {
            el.classList.add('flex');
        }
    }

    // ── Abrir visor ───────────────────────────────────────────
    function abrirVisor(url, titulo) {
        elTitulo.textContent = titulo;
        btnTab.href = url;
        elCtrls.classList.remove('hidden');
        elCtrls.classList.add('flex');
        paginaNum = 1;
        escala    = 1.2;
        pdfDoc    = null;
        elZoom.textContent = Math.round(escala * 100) + '%';
        mostrar(elLoad);
        cargarPDF(url);
    }

    // ── Cargar PDF ────────────────────────────────────────────
    async function cargarPDF(url) {
        try {
            pdfDoc = await pdfjsLib.getDocument(url).promise;
            elPagTot.textContent = pdfDoc.numPages;
            await renderPagina(1);
        } catch(e) {
            console.error('PDF error:', e);
            mostrar(elErr);
        }
    }

    // ── Renderizar página ─────────────────────────────────────
    async function renderPagina(num) {
        if (!pdfDoc) return;
        if (renderTask) { try { renderTask.cancel(); } catch(e){} }
        mostrar(elLoad);
        try {
            const page  = await pdfDoc.getPage(num);
            // Calcular escala para que quepa en el ancho del contenedor
            const contenedor = elScroll.clientWidth - 32; // padding
            const vp0        = page.getViewport({ scale: 1 });
            const escalaAuto = Math.min(escala, contenedor / vp0.width);
            const vp         = page.getViewport({ scale: escalaAuto });

            canvas.width  = vp.width;
            canvas.height = vp.height;

            renderTask = page.render({ canvasContext: ctx, viewport: vp });
            await renderTask.promise;
            mostrar(canvas);
            elScroll.scrollTop = 0;
            elPagAct.textContent = num;
            btnPrev.disabled = num <= 1;
            btnNext.disabled = num >= pdfDoc.numPages;
        } catch(e) {
            if (e?.name !== 'RenderingCancelledException') mostrar(elErr);
        }
    }

    function paginaAnterior() { if(paginaNum > 1) renderPagina(--paginaNum); }
    function paginaSiguiente() { if(pdfDoc && paginaNum < pdfDoc.numPages) renderPagina(++paginaNum); }
    function zoomIn()  { if(escala < 3)   { escala = Math.round((escala+0.25)*100)/100; elZoom.textContent=Math.round(escala*100)+'%'; renderPagina(paginaNum); } }
    function zoomOut() { if(escala > 0.5) { escala = Math.round((escala-0.25)*100)/100; elZoom.textContent=Math.round(escala*100)+'%'; renderPagina(paginaNum); } }

    document.addEventListener('keydown', e => {
        if (!pdfDoc) return;
        if (e.key === 'ArrowRight' || e.key === 'ArrowDown') paginaSiguiente();
        if (e.key === 'ArrowLeft'  || e.key === 'ArrowUp')   paginaAnterior();
        if (e.key === '+') zoomIn();
        if (e.key === '-') zoomOut();
    });

    // ── Drop zone ──────────────────────────────────────────────
    const dropZone    = document.getElementById('drop-zone');
    const fileInput   = document.getElementById('archivo-input');
    const placeholder = document.getElementById('drop-placeholder');
    const fileSelected= document.getElementById('file-selected');
    const fileName    = document.getElementById('file-name');
    const fileSize    = document.getElementById('file-size');

    function formatBytes(b) {
        if(b < 1024)     return b + ' B';
        if(b < 1048576)  return (b/1024).toFixed(1) + ' KB';
        return (b/1048576).toFixed(1) + ' MB';
    }
    function showFile(file) {
        fileName.textContent = file.name;
        fileSize.textContent = formatBytes(file.size);
        placeholder.classList.add('hidden');
        fileSelected.classList.remove('hidden');
    }
    function clearFile() {
        fileInput.value = '';
        placeholder.classList.remove('hidden');
        fileSelected.classList.add('hidden');
    }

    fileInput?.addEventListener('change', () => { if(fileInput.files[0]) showFile(fileInput.files[0]); });
    dropZone?.addEventListener('click', e => {
        if(!e.target.closest('button[type="button"]') && !fileSelected.contains(e.target)) fileInput.click();
    });
    dropZone?.addEventListener('dragover', e => { e.preventDefault(); dropZone.classList.add('border-[#52ABB1]'); });
    dropZone?.addEventListener('dragleave', () => dropZone.classList.remove('border-[#52ABB1]'));
    dropZone?.addEventListener('drop', e => {
        e.preventDefault();
        dropZone.classList.remove('border-[#52ABB1]');
        const file = e.dataTransfer.files[0];
        if(file) {
            const dt = new DataTransfer();
            dt.items.add(file);
            fileInput.files = dt.files;
            showFile(file);
        }
    });
    </script>
    @endpush
</x-app-layout>