<x-app-layout>
    @php
        $pageWrap = 'space-y-6';
        $card = 'rounded-[2rem] border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 shadow-sm';
        $inputClass = 'block w-full px-4 py-3 bg-white dark:bg-gray-800/40 border border-gray-200 dark:border-gray-700 focus:border-[#44B0B3] focus:ring-1 focus:ring-[#44B0B3] rounded-2xl text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 transition-all text-sm shadow-sm';
        $labelClass = 'block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2';
        $primaryBtn = 'inline-flex items-center justify-center rounded-2xl bg-[#44B0B3] hover:bg-[#389a9d] text-white font-bold px-5 py-3 shadow-lg shadow-[#44B0B3]/25 transition';
        $secondaryBtn = 'inline-flex items-center justify-center rounded-2xl border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-800 px-5 py-3 transition';
        $badge = 'inline-flex items-center rounded-full px-2.5 py-1 text-[11px] font-bold uppercase tracking-[0.12em]';
    @endphp

    <div class="max-w-6xl mx-auto {{ $pageWrap }}">
        <section class="{{ $card }} p-5 sm:p-6">
            <div class="flex flex-col xl:flex-row xl:items-start xl:justify-between gap-5">
                <div>
                    <p class="text-xs font-black uppercase tracking-[0.18em] text-gray-400 dark:text-gray-500">
                        Enfermeria / Evaluación
                    </p>
                    <h1 class="mt-2 text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white">
                        {{ $ingreso->paciente?->nombre_completo ?? 'Paciente' }}
                    </h1>
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                        Preingreso: {{ $ingreso->numero_preingreso ?: 'Sin número' }} · CI: {{ $ingreso->paciente?->ci ?? '-' }}
                    </p>
                </div>

                <div class="flex flex-wrap gap-3">
                    @php
                        $prioridadClass = $ingreso->prioridad_inicial === 'urgente'
                            ? 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300'
                            : 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300';
                    @endphp

                    <span class="{{ $badge }} {{ $prioridadClass }}">
                        {{ $ingreso->prioridad_inicial }}
                    </span>
                </div>
            </div>
        </section>

        @if($errors->any())
            <div class="rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700 dark:border-red-800 dark:bg-red-900/20 dark:text-red-300">
                Revisa los campos marcados en el formulario.
            </div>
        @endif

        <form method="POST" action="{{ route('enfermeria.store', $ingreso) }}" class="{{ $card }} overflow-hidden">
            @csrf

            <div class="px-5 sm:px-6 py-5 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white">Registro de enfermeria</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">Completa signos vitales, prioridad clínica y área destino.</p>
            </div>

            <div class="px-5 sm:px-6 py-5 space-y-8">
                <section class="space-y-4">
                    <div>
                        <h4 class="text-sm font-black uppercase tracking-[0.15em] text-gray-400 dark:text-gray-500">
                            Signos vitales
                        </h4>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                        <div>
                            <label class="{{ $labelClass }}">Temperatura</label>
                            <input type="number" step="0.01" name="temperatura" value="{{ old('temperatura') }}" class="{{ $inputClass }}" placeholder="Ej. 36.8">
                            @error('temperatura') <p class="mt-2 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="{{ $labelClass }}">Presión arterial</label>
                            <input type="text" name="presion_arterial" value="{{ old('presion_arterial') }}" class="{{ $inputClass }}" placeholder="Ej. 120/80">
                            @error('presion_arterial') <p class="mt-2 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="{{ $labelClass }}">Frecuencia cardiaca</label>
                            <input type="number" name="frecuencia_cardiaca" value="{{ old('frecuencia_cardiaca') }}" class="{{ $inputClass }}" placeholder="Ej. 78">
                            @error('frecuencia_cardiaca') <p class="mt-2 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="{{ $labelClass }}">Frecuencia respiratoria</label>
                            <input type="number" name="frecuencia_respiratoria" value="{{ old('frecuencia_respiratoria') }}" class="{{ $inputClass }}" placeholder="Ej. 18">
                            @error('frecuencia_respiratoria') <p class="mt-2 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="{{ $labelClass }}">Saturación de oxígeno</label>
                            <input type="number" step="0.01" name="saturacion_oxigeno" value="{{ old('saturacion_oxigeno') }}" class="{{ $inputClass }}" placeholder="Ej. 97">
                            @error('saturacion_oxigeno') <p class="mt-2 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="{{ $labelClass }}">Peso</label>
                            <input type="number" step="0.01" name="peso" value="{{ old('peso') }}" class="{{ $inputClass }}" placeholder="Ej. 65.50">
                            @error('peso') <p class="mt-2 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="{{ $labelClass }}">Talla</label>
                            <input type="number" step="0.01" name="talla" value="{{ old('talla') }}" class="{{ $inputClass }}" placeholder="Ej. 1.68">
                            @error('talla') <p class="mt-2 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </section>

                <section class="space-y-4">
                    <div>
                        <h4 class="text-sm font-black uppercase tracking-[0.15em] text-gray-400 dark:text-gray-500">
                            Clasificación clínica
                        </h4>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="{{ $labelClass }}">Prioridad clínica</label>
                            <select name="prioridad_clinica" class="{{ $inputClass }}">
                                <option value="">Seleccione</option>
                                <option value="baja" @selected(old('prioridad_clinica') === 'baja')>Baja</option>
                                <option value="media" @selected(old('prioridad_clinica') === 'media')>Media</option>
                                <option value="alta" @selected(old('prioridad_clinica') === 'alta')>Alta</option>
                                <option value="critica" @selected(old('prioridad_clinica') === 'critica')>Crítica</option>
                            </select>
                            @error('prioridad_clinica') <p class="mt-2 text-xs text-red-500">{{ $message }}</p> @enderror
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
                </section>

                <section class="space-y-4">
                    <div>
                        <h4 class="text-sm font-black uppercase tracking-[0.15em] text-gray-400 dark:text-gray-500">
                            Observación inicial
                        </h4>
                    </div>

                    <div>
                        <label class="{{ $labelClass }}">Observación</label>
                        <textarea name="observacion" rows="4" class="{{ $inputClass }}" placeholder="Describe el estado general del paciente, signos visibles o notas relevantes">{{ old('observacion') }}</textarea>
                        @error('observacion') <p class="mt-2 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                </section>
            </div>

            <div class="px-5 sm:px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900">
                <div class="flex items-center justify-end gap-3">
                    <a href="{{ route('enfermeria.index') }}" class="{{ $secondaryBtn }}">
                        Cancelar
                    </a>

                    <button type="submit" class="{{ $primaryBtn }}">
                        Guardar enfermeria y generar ticket
                    </button>
                </div>
            </div>
        </form>
    </div>
</x-app-layout>