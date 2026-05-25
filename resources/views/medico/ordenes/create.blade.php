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
                Órdenes médicas / Nueva orden
            </p>
            <h1 class="mt-2 text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white">
                Crear orden médica
            </h1>
            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                Paciente: {{ $atencion->ticket?->paciente?->nombre_completo ?? '-' }} · Atención asociada
            </p>
        </section>

        @if($errors->any())
            <div class="rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700 dark:border-red-800 dark:bg-red-900/20 dark:text-red-300">
                Revisa los campos del formulario.
            </div>
        @endif

        <form method="POST" action="{{ route('ordenes-medicas.store', $atencion) }}" class="{{ $card }} overflow-hidden">
            @csrf

            <div class="px-5 sm:px-6 py-5 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white">Datos de la orden</h2>
            </div>

            <div class="px-5 sm:px-6 py-5 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="{{ $labelClass }}">Número de orden</label>
                        <input type="text" name="num_orden" value="{{ old('num_orden') }}" class="{{ $inputClass }}" placeholder="Ej. ORD-0001">
                        @error('num_orden') <p class="mt-2 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="{{ $labelClass }}">Tipo</label>
                        <select name="tipo" class="{{ $inputClass }}">
                            <option value="">Seleccione</option>
                            <option value="laboratorio" @selected(old('tipo') === 'laboratorio')>Laboratorio</option>
                            <option value="imagen" @selected(old('tipo') === 'imagen')>Imagen</option>
                            <option value="procedimiento" @selected(old('tipo') === 'procedimiento')>Procedimiento</option>
                            <option value="interconsulta" @selected(old('tipo') === 'interconsulta')>Interconsulta</option>
                        </select>
                        @error('tipo') <p class="mt-2 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="{{ $labelClass }}">Estado</label>
                        <select name="estado" class="{{ $inputClass }}">
                            <option value="pendiente" @selected(old('estado', 'pendiente') === 'pendiente')>Pendiente</option>
                            <option value="en_proceso" @selected(old('estado') === 'en_proceso')>En proceso</option>
                            <option value="completada" @selected(old('estado') === 'completada')>Completada</option>
                            <option value="cancelada" @selected(old('estado') === 'cancelada')>Cancelada</option>
                        </select>
                        @error('estado') <p class="mt-2 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div>
                    <label class="{{ $labelClass }}">Descripción</label>
                    <textarea name="descripcion" rows="5" class="{{ $inputClass }}" placeholder="Describe la orden médica">{{ old('descripcion') }}</textarea>
                    @error('descripcion') <p class="mt-2 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="{{ $labelClass }}">Indicaciones</label>
                    <textarea name="indicaciones" rows="5" class="{{ $inputClass }}" placeholder="Indicaciones específicas para laboratorio, imagen o procedimiento">{{ old('indicaciones') }}</textarea>
                    @error('indicaciones') <p class="mt-2 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="px-5 sm:px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900">
                <div class="flex items-center justify-end gap-3">
                    <a href="{{ route('atenciones.show', $atencion) }}" class="{{ $secondaryBtn }}">Cancelar</a>
                    <button type="submit" class="{{ $primaryBtn }}">Guardar orden</button>
                </div>
            </div>
        </form>
    </div>
</x-app-layout>