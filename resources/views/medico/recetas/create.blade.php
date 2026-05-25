<x-app-layout>
    @php
        $pageWrap     = 'space-y-6';
        $card         = 'rounded-[2rem] border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 shadow-sm';
        $inputClass   = 'block w-full px-4 py-3 bg-white dark:bg-gray-800/40 border border-gray-200 dark:border-gray-700 focus:border-[#44B0B3] focus:ring-1 focus:ring-[#44B0B3] rounded-2xl text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 transition-all text-sm shadow-sm';
        $inputSm      = 'block w-full px-3 py-2 bg-white dark:bg-gray-800/40 border border-gray-200 dark:border-gray-700 focus:border-[#44B0B3] focus:ring-1 focus:ring-[#44B0B3] rounded-xl text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 transition-all text-sm shadow-sm';
        $labelClass   = 'block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2';
        $labelSm      = 'block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1';
        $primaryBtn   = 'inline-flex items-center justify-center rounded-2xl bg-[#44B0B3] hover:bg-[#389a9d] text-white font-bold px-5 py-3 shadow-lg shadow-[#44B0B3]/25 transition';
        $secondaryBtn = 'inline-flex items-center justify-center rounded-2xl border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-800 px-5 py-3 transition';
    @endphp

    {{-- Preparar datos de medicamentos para JS --}}
    @php
        $medicamentosJS = $medicamentos->map(function ($m) {
            return [
                'id'               => $m->id,
                'nombre'           => $m->nombre,
                'presentacion'     => $m->presentacion,
                'concentracion'    => $m->concentracion,
                'via_administracion' => $m->via_administracion,
                'completo'         => filled($m->presentacion) && filled($m->concentracion) && filled($m->via_administracion),
            ];
        })->values();
    @endphp

    <script>
        const MEDICAMENTOS_DB = @json($medicamentosJS);

        const INPUT_CLASS = "block w-full px-3 py-2 bg-white dark:bg-gray-800/40 border border-gray-200 dark:border-gray-700 focus:border-[#44B0B3] focus:ring-1 focus:ring-[#44B0B3] rounded-xl text-gray-900 dark:text-white placeholder-gray-400 text-sm shadow-sm transition-all";
        const INPUT_RO    = "block w-full px-3 py-2 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl text-gray-500 dark:text-gray-400 text-sm shadow-sm cursor-not-allowed";
    </script>

    <div class="max-w-6xl mx-auto {{ $pageWrap }}">

        {{-- Cabecera --}}
        <section class="{{ $card }} p-5 sm:p-6">
            <p class="text-xs font-black uppercase tracking-[0.18em] text-gray-400 dark:text-gray-500">
                Recetas médicas / Nueva receta
            </p>
            <h1 class="mt-2 text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white">
                Crear receta médica
            </h1>
            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                Paciente: <strong>{{ $atencion->ticket?->paciente?->nombre_completo ?? '-' }}</strong>
                · Ticket: {{ $atencion->ticket?->numero_ticket ?? '-' }}
                · Receta: <strong>{{ $numeroReceta }}</strong>
            </p>
        </section>

        @if($errors->any())
            <div class="rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700 dark:border-red-800 dark:bg-red-900/20 dark:text-red-300">
                <p class="font-semibold mb-1">Revisa los campos del formulario:</p>
                <ul class="list-disc list-inside space-y-0.5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('recetas.store', $atencion) }}" class="space-y-6">
            @csrf

            {{-- Número de receta (hidden, se genera en el controller) --}}
            <input type="hidden" name="numero_receta" value="{{ $numeroReceta }}">

            {{-- Indicaciones generales --}}
            <div class="{{ $card }} overflow-hidden">
                <div class="px-5 sm:px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white">Indicaciones generales</h2>
                    <p class="text-xs text-gray-400 mt-0.5">Instrucciones generales para el paciente sobre la receta.</p>
                </div>
                <div class="px-5 sm:px-6 py-5">
                    <textarea name="indicacion_general"
                              rows="3"
                              class="{{ $inputClass }}"
                              placeholder="Ej: Tomar con alimentos, evitar alcohol durante el tratamiento...">{{ old('indicacion_general') }}</textarea>
                    @error('indicacion_general')
                        <p class="mt-2 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Medicamentos --}}
            <div class="{{ $card }} overflow-hidden">
                <div class="px-5 sm:px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white">Medicamentos</h2>
                    <p class="text-xs text-gray-400 mt-0.5">
                        Seleccione del catálogo o ingrese manualmente. Si el medicamento es nuevo, se registrará en el catálogo.
                    </p>
                </div>

                <div class="px-5 sm:px-6 py-5 space-y-4" id="medicamentos-container">
                    {{-- Se genera dinámicamente con JS --}}
                </div>

                <div class="px-5 sm:px-6 pb-5">
                    <button type="button"
                            onclick="agregarMedicamento()"
                            class="flex items-center gap-2 rounded-2xl border border-dashed border-gray-300 dark:border-gray-700 px-4 py-2.5 text-sm font-semibold text-gray-500 hover:border-[#44B0B3] hover:text-[#44B0B3] transition w-full justify-center">
                        <svg class="w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M12 5v14"/>
                        </svg>
                        Agregar medicamento
                    </button>
                </div>
            </div>

            {{-- Acciones --}}
            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('atenciones.show', $atencion) }}" class="{{ $secondaryBtn }}">Cancelar</a>
                <button type="submit" class="{{ $primaryBtn }}">Guardar receta</button>
            </div>
        </form>
    </div>

<script>
let medIndex = 0;

// Opciones para selects
const PRESENTACIONES = ['Tableta','Cápsula','Jarabe','Suspensión','Inyectable','Crema','Ungüento','Gotas','Parche','Supositorio','Polvo','Spray','Solución'];
const VIAS           = ['Oral','Intravenosa','Intramuscular','Subcutánea','Tópica','Inhalatoria','Sublingual','Rectal','Oftálmica','Ótica'];
const UNIDADES       = ['mg','g','mcg','UI','mEq','ml','l','%','mg/ml','mg/5ml','mg/dl','mcg/ml','UI/ml'];

        // Unidad de cantidad según presentación
        const CANT_UNIDAD_POR_PRESENTACION = {
            'Tableta':    'tableta(s)',
            'Capsula':    'capsula(s)',
            'Jarabe':     'ml',
            'Suspension': 'ml',
            'Inyectable': 'ampolla(s)',
            'Crema':      'g',
            'Unguento':   'g',
            'Gotas':      'gota(s)',
            'Parche':     'parche(s)',
            'Supositorio':'supositorio(s)',
            'Polvo':      'sobre(s)',
            'Spray':      'aplicacion(es)',
            'Solucion':   'ml',
        };

function selectOptions(arr, selected = '') {
    return arr.map(v => `<option value="${v}" ${v === selected ? 'selected' : ''}>${v}</option>`).join('');
}

function catalogoOptions() {
    let opts = '<option value="">— Escribir manualmente —</option>';
    MEDICAMENTOS_DB.forEach(m => {
        let label = m.nombre;
        if (m.presentacion)  label += ` · ${m.presentacion}`;
        if (m.concentracion) label += ` · ${m.concentracion}`;
        if (!m.completo)     label += ' ⚠ Incompleto';
        opts += `<option value="${m.id}"
                         data-nombre="${m.nombre}"
                         data-presentacion="${m.presentacion || ''}"
                         data-concentracion="${m.concentracion || ''}"
                         data-via="${m.via_administracion || ''}"
                         data-completo="${m.completo}">${label}</option>`;
    });
    return opts;
}

function agregarMedicamento() {
    const container = document.getElementById('medicamentos-container');
    const i = medIndex++;

    const html = `
    <div id="med-bloque-${i}" class="rounded-2xl border border-gray-200 dark:border-gray-700 overflow-hidden">

        {{-- Cabecera del bloque --}}
        <div class="flex items-center justify-between px-4 py-3 bg-gray-50 dark:bg-gray-800/50 border-b border-gray-200 dark:border-gray-700">
            <p class="text-xs font-bold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                Medicamento ${i + 1}
            </p>
            <button type="button" onclick="eliminarMedicamento(${i})"
                class="w-7 h-7 rounded-xl flex items-center justify-center text-gray-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 transition">
                <svg class="w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 7h14m-9 3v8m4-8v8M10 3h4a1 1 0 0 1 1 1v3H9V4a1 1 0 0 1 1-1ZM6 7h12v13a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1V7Z"/>
                </svg>
            </button>
        </div>

        <div class="p-4 space-y-4">

            {{-- Seleccionar del catálogo --}}
            <div>
                <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1">
                    Buscar en catálogo
                    <span class="normal-case font-normal text-gray-400">(seleccione o escriba manualmente abajo)</span>
                </label>
                <select id="catalogo-${i}" onchange="onCatalogoChange(${i})" class="${INPUT_CLASS}">
                    ${catalogoOptions()}
                </select>
            </div>

            {{-- Aviso medicamento incompleto --}}
            <div id="aviso-incompleto-${i}" class="hidden rounded-xl border border-amber-200 dark:border-amber-800 bg-amber-50 dark:bg-amber-900/10 px-3 py-2.5 flex items-start gap-2">
                <svg class="w-4 h-4 text-amber-600 flex-shrink-0 mt-0.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v4m0 4h.01M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0Z"/>
                </svg>
                <div>
                    <p class="text-xs font-bold text-amber-700 dark:text-amber-400">
                        Este medicamento tiene datos incompletos en el catálogo.
                    </p>
                    <p class="text-xs text-amber-600 dark:text-amber-500 mt-0.5">
                        Complete la presentación, concentración y vía de administración — se actualizarán en el catálogo al guardar.
                    </p>
                </div>
            </div>

            {{-- Nombre del medicamento --}}
            <div>
                <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1">
                    Nombre del medicamento <span class="text-red-500">*</span>
                </label>
                <input type="text"
                       name="medicamentos[${i}][nombre]"
                       id="nombre-${i}"
                       class="${INPUT_CLASS}"
                       placeholder="Ej: Amoxicilina"
                       required
                       autocomplete="off">
                {{-- ID del medicamento del catálogo (null si es nuevo) --}}
                <input type="hidden" name="medicamentos[${i}][medicamento_id]" id="med-id-${i}" value="">
            </div>

            {{-- Presentación / Concentración / Vía --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1">
                        Presentación <span class="text-red-500">*</span>
                    </label>
                    <select name="medicamentos[${i}][presentacion]" id="presentacion-${i}" onchange="onPresentacionChange(${i})" class="${INPUT_CLASS}" required>
                        <option value="">Seleccionar...</option>
                        ${selectOptions(PRESENTACIONES)}
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1">
                        Concentración <span class="text-red-500">*</span>
                    </label>
                    <div class="flex gap-1.5">
                        <input type="number"
                               id="conc-num-${i}"
                               class="block px-3 py-2 bg-white dark:bg-gray-800/40 border border-gray-200 dark:border-gray-700 focus:border-[#44B0B3] focus:ring-1 focus:ring-[#44B0B3] rounded-xl text-gray-900 dark:text-white text-sm shadow-sm transition-all w-24 flex-shrink-0"
                               placeholder="500"
                               min="0" step="any"
                               oninput="combinarConc(${i})"
                               required>
                        <select id="conc-unit-${i}" onchange="combinarConc(${i})" class="${INPUT_CLASS}">
                            ${selectOptions(UNIDADES, 'mg')}
                        </select>
                    </div>
                    <input type="hidden" name="medicamentos[${i}][concentracion]" id="conc-val-${i}">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1">
                        Vía de administración <span class="text-red-500">*</span>
                    </label>
                    <select name="medicamentos[${i}][via_administracion]" id="via-${i}" class="${INPUT_CLASS}" required>
                        <option value="">Seleccionar...</option>
                        ${selectOptions(VIAS)}
                    </select>
                </div>
            </div>

            {{-- Frecuencia / Duración / Cantidad --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 sm:items-end">

                {{-- Frecuencia: número + unidad --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1">
                        Frecuencia <span class="text-red-500">*</span>
                    </label>
                    <div class="flex gap-1.5">
                        <input type="number"
                               id="frec-num-${i}"
                               class="block px-3 py-2 bg-white dark:bg-gray-800/40 border border-gray-200 dark:border-gray-700 focus:border-[#44B0B3] focus:ring-1 focus:ring-[#44B0B3] rounded-xl text-gray-900 dark:text-white text-sm shadow-sm transition-all w-20 flex-shrink-0"
                               placeholder="8"
                               min="1" step="1"
                               oninput="combinarFrecuencia(${i})"
                               required>
                        <select id="frec-unit-${i}" onchange="combinarFrecuencia(${i})" class="${INPUT_CLASS}">
                            <option value="horas">Horas</option>
                            <option value="veces/dia">Veces/día</option>
                            <option value="dias">Días</option>
                            <option value="semanas">Semanas</option>
                        </select>
                    </div>
                    <input type="hidden" name="medicamentos[${i}][frecuencia]" id="frec-val-${i}">
                    <p id="frec-preview-${i}" class="mt-1 text-[10px] text-[#44B0B3] font-medium"></p>
                </div>

                {{-- Duración: número + unidad --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1">
                        Duración <span class="text-red-500">*</span>
                    </label>
                    <div class="flex gap-1.5">
                        <input type="number"
                               id="dur-num-${i}"
                               class="block px-3 py-2 bg-white dark:bg-gray-800/40 border border-gray-200 dark:border-gray-700 focus:border-[#44B0B3] focus:ring-1 focus:ring-[#44B0B3] rounded-xl text-gray-900 dark:text-white text-sm shadow-sm transition-all w-20 flex-shrink-0"
                               placeholder="7"
                               min="1" step="1"
                               oninput="combinarDuracion(${i})"
                               required>
                        <select id="dur-unit-${i}" onchange="combinarDuracion(${i})" class="${INPUT_CLASS}">
                            <option value="dias">Días</option>
                            <option value="semanas">Semanas</option>
                            <option value="meses">Meses</option>
                        </select>
                    </div>
                    <input type="hidden" name="medicamentos[${i}][duracion]" id="dur-val-${i}">
                    <p id="dur-preview-${i}" class="mt-1 text-[10px] text-[#44B0B3] font-medium"></p>
                </div>

                {{-- Cantidad: dosis por toma + total editable --}}
                <div class="sm:col-span-3">
                    <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1">
                        Dosis por toma y cantidad total <span class="text-red-500">*</span>
                    </label>
                    <div class="grid grid-cols-2 gap-2">
                        {{-- Dosis por toma --}}
                        <div>
                            <p class="text-[10px] text-gray-400 mb-1">Por toma</p>
                            <div class="flex gap-1.5 items-center">
                                <input type="number"
                                       id="cant-dosis-${i}"
                                       class="block px-3 py-2 bg-white dark:bg-gray-800/40 border border-gray-200 dark:border-gray-700 focus:border-[#44B0B3] focus:ring-1 focus:ring-[#44B0B3] rounded-xl text-gray-900 dark:text-white text-sm shadow-sm transition-all w-20 flex-shrink-0"
                                       placeholder="1"
                                       min="0.5" step="0.5"
                                       oninput="calcularCantidad(${i})"
                                       required>
                                <span id="cant-unidad-label-${i}"
                                      class="text-xs text-gray-500 dark:text-gray-400 whitespace-nowrap truncate">
                                    unidad(es)
                                </span>
                            </div>
                        </div>
                        {{-- Total editable (se autocompleta pero se puede cambiar) --}}
                        <div>
                            <p class="text-[10px] text-gray-400 mb-1">Total a dispensar</p>
                            <input type="text"
                                   name="medicamentos[${i}][cantidad]"
                                   id="cant-val-${i}"
                                   class="block w-full px-3 py-2 bg-white dark:bg-gray-800/40 border border-gray-200 dark:border-gray-700 focus:border-[#44B0B3] focus:ring-1 focus:ring-[#44B0B3] rounded-xl text-gray-900 dark:text-white text-sm shadow-sm transition-all font-semibold"
                                   placeholder="Ej: 21 tabletas"
                                   required>
                        </div>
                    </div>
                    {{-- Fórmula en tiempo real --}}
                    <p id="cant-formula-${i}" class="mt-1.5 text-[10px] text-gray-400"></p>
                </div>

            </div>

            {{-- Observación --}}
            <div>
                <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1">
                    Observación <span class="font-normal text-gray-400">(opcional)</span>
                </label>
                <input type="text"
                       name="medicamentos[${i}][observacion]"
                       class="${INPUT_CLASS}"
                       placeholder="Ej: Tomar con alimentos">
            </div>
        </div>
    </div>`;

    container.insertAdjacentHTML('beforeend', html);
}

function eliminarMedicamento(i) {
    const bloque = document.getElementById('med-bloque-' + i);
    if (bloque) bloque.remove();
}

function onCatalogoChange(i) {
    const select   = document.getElementById('catalogo-' + i);
    const opt      = select.options[select.selectedIndex];
    const esDeCat  = opt.value !== '';

    const inputNombre  = document.getElementById('nombre-' + i);
    const inputMedId   = document.getElementById('med-id-' + i);
    const selPresent   = document.getElementById('presentacion-' + i);
    const inputConcNum = document.getElementById('conc-num-' + i);
    const selConcUnit  = document.getElementById('conc-unit-' + i);
    const selVia       = document.getElementById('via-' + i);
    const avisoInc     = document.getElementById('aviso-incompleto-' + i);

    if (esDeCat) {
        const completo = opt.dataset.completo === 'true';

        // Nombre: readonly si viene del catálogo
        inputNombre.value    = opt.dataset.nombre;
        inputNombre.readOnly = true;
        inputNombre.classList.add('bg-gray-50', 'cursor-not-allowed', 'text-gray-500');

        inputMedId.value = opt.value;

        // Presentación
        selPresent.value = opt.dataset.presentacion || '';

        // Concentración: separar número y unidad si existe
        if (opt.dataset.concentracion) {
            const match = opt.dataset.concentracion.match(/^([\d.]+)\s*(.+)?$/);
            if (match) {
                inputConcNum.value = match[1] || '';
                selConcUnit.value  = match[2]?.trim() || 'mg';
            } else {
                inputConcNum.value = opt.dataset.concentracion;
            }
            combinarConc(i);
        }

        // Vía
        selVia.value = opt.dataset.via || '';

        // Si está incompleto: campos editables + aviso
        if (!completo) {
            avisoInc.classList.remove('hidden');
            inputNombre.readOnly = true; // nombre sigue readonly
            // Presentación, concentración y vía: editables para completar
            selPresent.disabled  = false;
            inputConcNum.disabled = false;
            selConcUnit.disabled = false;
            selVia.disabled      = false;
        } else {
            avisoInc.classList.add('hidden');
        }

    } else {
        // Manual: todo editable
        inputNombre.value    = '';
        inputNombre.readOnly = false;
        inputNombre.classList.remove('bg-gray-50', 'cursor-not-allowed', 'text-gray-500');
        inputMedId.value     = '';

        selPresent.value   = '';
        inputConcNum.value = '';
        selConcUnit.value  = 'mg';
        selVia.value       = '';

        avisoInc.classList.add('hidden');
        inputNombre.focus();
    }
}

function combinarConc(i) {
    const num    = document.getElementById('conc-num-' + i).value.trim();
    const unit   = document.getElementById('conc-unit-' + i).value;
    const hidden = document.getElementById('conc-val-' + i);
    hidden.value = num ? (num + ' ' + unit) : '';
}

function combinarFrecuencia(i) {
    const num    = document.getElementById('frec-num-' + i).value.trim();
    const unit   = document.getElementById('frec-unit-' + i).value;
    const hidden = document.getElementById('frec-val-' + i);
    const prev   = document.getElementById('frec-preview-' + i);

    if (!num) { hidden.value = ''; prev.textContent = ''; return; }

    let texto = '';
    switch(unit) {
        case 'horas':     texto = `Cada ${num} hora${num == 1 ? '' : 's'}`; break;
        case 'veces/dia': texto = `${num} vez${num == 1 ? '' : 'es'} al día`; break;
        case 'dias':      texto = `Cada ${num} día${num == 1 ? '' : 's'}`; break;
        case 'semanas':   texto = `Cada ${num} semana${num == 1 ? '' : 's'}`; break;
    }
    hidden.value    = texto;
    prev.textContent = texto;
    calcularCantidad(i); // recalcular cantidad al cambiar frecuencia
}

function combinarDuracion(i) {
    const num    = document.getElementById('dur-num-' + i).value.trim();
    const unit   = document.getElementById('dur-unit-' + i).value;
    const hidden = document.getElementById('dur-val-' + i);
    const prev   = document.getElementById('dur-preview-' + i);

    if (!num) { hidden.value = ''; prev.textContent = ''; return; }

    let texto = '';
    switch(unit) {
        case 'dias':    texto = `${num} día${num == 1 ? '' : 's'}`; break;
        case 'semanas': texto = `${num} semana${num == 1 ? '' : 's'}`; break;
        case 'meses':   texto = `${num} mes${num == 1 ? 'es' : 'es'}`; break;
    }
    hidden.value    = texto;
    prev.textContent = texto;
    calcularCantidad(i); // recalcular cantidad al cambiar duración
}

function calcularCantidad(i) {
    const dosisEl   = document.getElementById('cant-dosis-' + i);
    const totalEl   = document.getElementById('cant-val-' + i);
    const formulaEl = document.getElementById('cant-formula-' + i);
    const labelEl   = document.getElementById('cant-unidad-label-' + i);
    const present   = document.getElementById('presentacion-' + i)?.value || '';

    // Unidad según presentación seleccionada
    const unidad = CANT_UNIDAD_POR_PRESENTACION[present] || 'unidad(es)';
    labelEl.textContent = unidad;

    const dosis   = parseFloat(dosisEl.value) || 0;
    const frecNum = parseFloat(document.getElementById('frec-num-' + i)?.value) || 0;
    const frecUnit = document.getElementById('frec-unit-' + i)?.value || 'horas';
    const durNum  = parseFloat(document.getElementById('dur-num-' + i)?.value) || 0;
    const durUnit = document.getElementById('dur-unit-' + i)?.value || 'dias';

    if (!dosis || !frecNum || !durNum) {
        formulaEl.textContent = '';
        return; // no limpiar el campo para que el médico pueda escribir manual
    }

    // Tomas por día
    let tomasPorDia = 0;
    switch(frecUnit) {
        case 'horas':     tomasPorDia = 24 / frecNum; break;
        case 'veces/dia': tomasPorDia = frecNum; break;
        case 'dias':      tomasPorDia = 1 / frecNum; break;
        case 'semanas':   tomasPorDia = 1 / (frecNum * 7); break;
    }

    // Días totales
    let diasTotales = 0;
    switch(durUnit) {
        case 'dias':    diasTotales = durNum; break;
        case 'semanas': diasTotales = durNum * 7; break;
        case 'meses':   diasTotales = durNum * 30; break;
    }

    // Total = dosis × tomas/día × días (redondeado arriba)
    const total = Math.ceil(dosis * tomasPorDia * diasTotales);
    const sugerencia = total + ' ' + unidad;

    // Autocompletar el campo editable con la sugerencia
    totalEl.value = sugerencia;

    // Mostrar fórmula debajo como referencia
    const tpd = tomasPorDia % 1 === 0 ? tomasPorDia : tomasPorDia.toFixed(1);
    formulaEl.textContent = `${dosis} ${unidad}/toma × ${tpd} tomas/día × ${diasTotales} días = ${total} ${unidad}`;
    formulaEl.className   = 'mt-1.5 text-[10px] text-[#44B0B3] font-medium';
}

// Cuando cambia presentación, actualizar unidad y recalcular
function onPresentacionChange(i) {
    calcularCantidad(i);
}

// Agregar el primer medicamento automáticamente al cargar
window.addEventListener('DOMContentLoaded', () => agregarMedicamento());
</script>
</x-app-layout>