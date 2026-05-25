<x-app-layout>
    @php
        $pageWrap = 'space-y-6';
        $card = 'rounded-[2rem] border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 shadow-sm';
        $badge = 'inline-flex items-center rounded-full px-2.5 py-1 text-[11px] font-bold uppercase tracking-[0.12em]';
        $primaryBtn = 'inline-flex items-center justify-center rounded-2xl bg-[#44B0B3] hover:bg-[#389a9d] text-white font-bold px-5 py-3 shadow-lg shadow-[#44B0B3]/25 transition';
        $secondaryBtn = 'inline-flex items-center justify-center rounded-2xl border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-800 px-5 py-3 transition';
        $inputClass = 'block w-full px-4 py-3 bg-white dark:bg-gray-800/40 border border-gray-200 dark:border-gray-700 focus:border-[#44B0B3] focus:ring-1 focus:ring-[#44B0B3] rounded-2xl text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 transition-all text-sm shadow-sm';
        $labelClass = 'block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2';

        $stClass = match($solicitud->estado) {
            'pendiente'  => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300',
            'en_proceso' => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-300',
            'completado' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300',
            default      => 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300',
        };
    @endphp

    <div class="mx-auto {{ $pageWrap }}">

        {{-- ── CABECERA ── --}}
        <section class="{{ $card }} p-5 sm:p-6">
            <div class="flex flex-col xl:flex-row xl:items-start xl:justify-between gap-5">
                <div>
                    <p class="text-xs font-black uppercase tracking-[0.18em] text-gray-400 dark:text-gray-500">
                        Laboratorio / Solicitud directa
                    </p>
                    <h1 class="mt-2 text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white">
                        {{ $solicitud->ingreso?->paciente?->nombre_completo ?? 'Paciente' }}
                    </h1>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        CI: {{ $solicitud->ingreso?->paciente?->ci ?? '-' }} ·
                        Ingreso: {{ $solicitud->ingreso?->numero_preingreso ?? '-' }} ·
                        {{ $solicitud->created_at?->format('d/m/Y H:i') }}
                    </p>
                </div>
                <div class="flex flex-wrap items-center gap-3">
                    <span class="{{ $badge }} {{ $stClass }}">
                        {{ str_replace('_', ' ', $solicitud->estado) }}
                    </span>
                    <a href="{{ route('laboratorio.index') }}" class="{{ $secondaryBtn }} !px-4 !py-2.5 !text-sm">
                        ← Volver
                    </a>
                </div>
            </div>

            {{-- Info del examen solicitado --}}
            <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50 px-4 py-4">
                    <p class="text-xs uppercase tracking-[0.14em] text-gray-400 dark:text-gray-500">Examen solicitado</p>
                    <p class="mt-2 text-base font-bold text-gray-900 dark:text-white">
                        {{ $solicitud->examen?->nombre_examen ?? 'Sin especificar' }}
                    </p>
                </div>
                <div class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50 px-4 py-4">
                    <p class="text-xs uppercase tracking-[0.14em] text-gray-400 dark:text-gray-500">Categoría</p>
                    <p class="mt-2 text-base font-bold text-gray-900 dark:text-white">
                        {{ $solicitud->examen?->categoria ?? '-' }}
                    </p>
                </div>
                <div class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50 px-4 py-4">
                    <p class="text-xs uppercase tracking-[0.14em] text-gray-400 dark:text-gray-500">Resultados subidos</p>
                    <p class="mt-2 text-3xl font-bold text-[#44B0B3]">{{ $solicitud->adjuntos->count() }}</p>
                </div>
            </div>
        </section>

        {{-- Mensajes --}}
        @if(session('success'))
            <div class="rounded-2xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700 dark:border-green-800 dark:bg-green-900/20 dark:text-green-300">
                {{ session('success') }}
            </div>
        @endif

        {{-- ── SUBIR RESULTADO ── --}}
        @if($solicitud->estado !== 'completado')
            <section class="{{ $card }} overflow-hidden">
                <div class="px-5 sm:px-6 py-5 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white">Subir resultado</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        Acepta PDF, JPG y PNG. El archivo se comprimirá automáticamente antes de guardarse.
                    </p>
                </div>

                <form method="POST"
                      action="{{ route('laboratorio.upload-solicitud', $solicitud) }}"
                      enctype="multipart/form-data"
                      class="px-5 sm:px-6 py-5 space-y-5">
                    @csrf

                    {{-- Drop zone --}}
                    <div>
                        <label class="{{ $labelClass }}">Archivo resultado <span class="text-red-500">*</span></label>
                        <label for="archivo"
                               class="flex flex-col items-center justify-center w-full h-36 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-2xl cursor-pointer hover:border-[#44B0B3] hover:bg-[#44B0B3]/5 transition-all group">
                            <svg class="w-8 h-8 text-gray-400 group-hover:text-[#44B0B3] transition mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"/>
                            </svg>
                            <p class="text-sm text-gray-500 dark:text-gray-400 group-hover:text-[#44B0B3] transition">
                                <span class="font-semibold">Clic para seleccionar</span> o arrastra el archivo
                            </p>
                            <p class="text-xs text-gray-400 mt-1">PDF, JPG, PNG · Máx. 20 MB</p>
                            <input id="archivo" name="archivo" type="file"
                                   accept=".pdf,.jpg,.jpeg,.png" class="hidden"
                                   onchange="mostrarNombreArchivo(this)">
                        </label>
                        <p id="nombre-archivo" class="mt-2 text-xs text-[#44B0B3] font-medium hidden"></p>
                        @error('archivo') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="{{ $labelClass }}">Observación (opcional)</label>
                        <textarea name="observacion" rows="3"
                                  class="{{ $inputClass }}"
                                  placeholder="Notas sobre el resultado...">{{ old('observacion') }}</textarea>
                        @error('observacion') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-2">
                        <button type="submit" class="{{ $primaryBtn }}">
                            Subir resultado
                        </button>
                    </div>
                </form>
            </section>
        @endif

        {{-- ── RESULTADOS SUBIDOS ── --}}
        @if($solicitud->adjuntos->count() > 0)
            <section class="{{ $card }} overflow-hidden">
                <div class="px-5 sm:px-6 py-5 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white">Resultados subidos</h2>

                    {{-- Marcar como completada --}}
                    @if($solicitud->estado !== 'completado')
                        <form method="POST" action="{{ route('laboratorio.completar-solicitud', $solicitud) }}">
                            @csrf @method('PATCH')
                            <button type="submit"
                                    class="inline-flex items-center rounded-xl px-4 py-2 text-xs font-bold bg-emerald-100 text-emerald-700 hover:bg-emerald-200 dark:bg-emerald-900/30 dark:text-emerald-300 transition">
                                ✓ Marcar como completada
                            </button>
                        </form>
                    @endif
                </div>

                <div class="divide-y divide-gray-100 dark:divide-gray-800">
                    @foreach($solicitud->adjuntos as $adjunto)
                        <div class="px-5 sm:px-6 py-4 flex items-center justify-between gap-4">
                            <div class="flex items-center gap-4 min-w-0">
                                {{-- Ícono según tipo --}}
                                @php
                                    $ext = strtolower(pathinfo($adjunto->nombre_archivo, PATHINFO_EXTENSION));
                                    $iconColor = $ext === 'pdf' ? 'text-red-500' : 'text-blue-500';
                                @endphp
                                <div class="flex-shrink-0 w-10 h-10 rounded-xl bg-gray-100 dark:bg-gray-800 flex items-center justify-center">
                                    @if($ext === 'pdf')
                                        <svg class="w-5 h-5 {{ $iconColor }}" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8l-6-6zm-1 1.5L18.5 9H13V3.5zM9.5 14.5h1.3c.7 0 1.2-.5 1.2-1.2 0-.7-.5-1.3-1.2-1.3H9.5v3.8H10v-1.3zm.5-2h.8c.4 0 .7.3.7.8 0 .4-.3.7-.7.7H10v-1.5zm4.5 1.5v1h1.5v.5H14.5V17H14v-3.5h2V14h-1.5zm-6.5 1v.5h1V16H8v1h-.5v-3.5h2v.5H8V15z"/>
                                        </svg>
                                    @else
                                        <svg class="w-5 h-5 {{ $iconColor }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                    @endif
                                </div>

                                <div class="min-w-0">
                                    <p class="text-sm font-semibold text-gray-900 dark:text-white truncate">
                                        {{ $adjunto->nombre_archivo }}
                                    </p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                        Subido por {{ $adjunto->usuario?->name ?? 'Sistema' }} ·
                                        {{ $adjunto->fecha_subida?->format('d/m/Y H:i') }}
                                    </p>
                                    @if($adjunto->observacion)
                                        <p class="text-xs text-gray-400 mt-0.5 italic">{{ $adjunto->observacion }}</p>
                                    @endif
                                </div>
                            </div>

                            <div class="flex items-center gap-2 flex-shrink-0">
                                {{-- Ver archivo --}}
                                <a href="{{ route('laboratorio.archivo', $adjunto) }}"
                                   target="_blank"
                                   class="inline-flex items-center rounded-xl px-3 py-2 text-xs font-semibold text-[#44B0B3] hover:bg-[#44B0B3]/10 transition">
                                    Ver
                                </a>

                                {{-- Eliminar --}}
                                <form method="POST" action="{{ route('laboratorio.destroy', $adjunto) }}"
                                      onsubmit="return confirm('¿Eliminar este archivo?')">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                            class="inline-flex items-center rounded-xl px-3 py-2 text-xs font-semibold text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 transition">
                                        Eliminar
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>
        @endif

    </div>

    <script>
        function mostrarNombreArchivo(input) {
            const label = document.getElementById('nombre-archivo');
            if (input.files && input.files[0]) {
                label.textContent = '✓ ' + input.files[0].name;
                label.classList.remove('hidden');
            }
        }
    </script>
</x-app-layout>