<x-app-layout>
    @php
        $pageWrap = 'space-y-6';
        $card = 'rounded-[2rem] border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 shadow-sm';
        $inputClass = 'block w-full px-4 py-3 bg-white dark:bg-gray-800/40 border border-gray-200 dark:border-gray-700 focus:border-[#44B0B3] focus:ring-1 focus:ring-[#44B0B3] rounded-2xl text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 transition-all text-sm shadow-sm';
        $labelClass = 'block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2';
        $primaryBtn = 'inline-flex items-center justify-center rounded-2xl bg-[#44B0B3] hover:bg-[#389a9d] text-white font-bold px-5 py-3 shadow-lg shadow-[#44B0B3]/25 transition';
        $secondaryBtn = 'inline-flex items-center justify-center rounded-2xl border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-800 px-5 py-3 transition';
    @endphp

    <div class="max-w-6xl mx-auto {{ $pageWrap }}">
        <section class="{{ $card }} p-5 sm:p-6">
            <p class="text-xs font-black uppercase tracking-[0.18em] text-gray-400 dark:text-gray-500">
                Interconsultas / Nueva derivación
            </p>
            <h1 class="mt-2 text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white">
                Crear derivación
            </h1>
            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                Paciente: {{ $atencion->ticket?->paciente?->nombre_completo ?? '-' }} · Ticket: {{ $atencion->ticket?->numero_ticket ?? '-' }}
            </p>
        </section>

        @if($errors->any())
            <div class="rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700 dark:border-red-800 dark:bg-red-900/20 dark:text-red-300">
                Revisa los campos del formulario.
            </div>
        @endif

        <form method="POST" action="{{ route('interconsultas.store', $atencion) }}" class="{{ $card }} overflow-hidden">
            @csrf

            <div class="px-5 sm:px-6 py-5 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white">Datos de la derivación</h2>
            </div>

            <div class="px-5 sm:px-6 py-5 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="{{ $labelClass }}">Área origen</label>
                        <select name="area_origen_id" class="{{ $inputClass }}">
                            <option value="">Seleccione</option>
                            @foreach($areas as $area)
                                <option value="{{ $area->id }}" @selected(old('area_origen_id', $atencion->ticket?->area_id) == $area->id)>
                                    {{ $area->nombre }}
                                </option>
                            @endforeach
                        </select>
                        @error('area_origen_id') <p class="mt-2 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="{{ $labelClass }}">Área destino</label>
                        <select name="area_destino_id" class="{{ $inputClass }}">
                            <option value="">Seleccione</option>
                            @foreach($areas as $area)
                                <option value="{{ $area->id }}" @selected(old('area_destino_id') == $area->id)>
                                    {{ $area->nombre }}
                                </option>
                            @endforeach
                        </select>
                        @error('area_destino_id') <p class="mt-2 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div>
                    <label class="{{ $labelClass }}">Motivo de derivación</label>
                    <textarea name="motivo_interconsulta" rows="5" class="{{ $inputClass }}" placeholder="Describe por qué el paciente debe ser derivado">{{ old('motivo_interconsulta') }}</textarea>
                    @error('motivo_interconsulta') <p class="mt-2 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="{{ $labelClass }}">Observación</label>
                    <textarea name="observacion" rows="4" class="{{ $inputClass }}" placeholder="Observación adicional">{{ old('observacion') }}</textarea>
                    @error('observacion') <p class="mt-2 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="px-5 sm:px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900">
                <div class="flex items-center justify-end gap-3">
                    <a href="{{ route('atenciones.show', $atencion) }}" class="{{ $secondaryBtn }}">Cancelar</a>
                    <button type="submit" class="{{ $primaryBtn }}">Guardar derivación</button>
                </div>
            </div>
        </form>
    </div>
</x-app-layout>