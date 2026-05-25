{{--
    Partial: alergias del paciente
    Variables requeridas:
        $paciente     → App\Models\Paciente (con alergias cargadas)
        $medicamentos → Collection de Medicamento activos
--}}
@php
    $badge   = 'inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-bold uppercase tracking-[0.1em]';
    $inputSm = 'block w-full px-3 py-2 bg-white dark:bg-gray-800/60 border border-gray-200 dark:border-gray-700 focus:border-[#44B0B3] focus:ring-1 focus:ring-[#44B0B3] rounded-xl text-gray-900 dark:text-white text-xs shadow-sm transition-all';
    $labelSm = 'block text-[10px] font-bold uppercase tracking-[0.1em] text-gray-500 dark:text-gray-400 mb-1';
@endphp

{{-- ── Lista de alergias existentes ── --}}
@if($paciente->alergias->count())
    <div class="space-y-2 mb-4">
        @foreach($paciente->alergias as $alergia)
            <div class="flex items-start gap-2 p-2.5 rounded-2xl border
                @if($alergia->severidad === 'grave')
                    border-red-200 bg-red-50 dark:border-red-900/40 dark:bg-red-900/10
                @elseif($alergia->severidad === 'moderada')
                    border-orange-200 bg-orange-50 dark:border-orange-900/40 dark:bg-orange-900/10
                @else
                    border-yellow-200 bg-yellow-50 dark:border-yellow-900/40 dark:bg-yellow-900/10
                @endif">

                <span class="flex-shrink-0 mt-0.5">
                    @if($alergia->tipo === 'medicamento')
                        <svg class="w-3.5 h-3.5 text-red-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m10.5 21-.5-1m0 0-.5-1M10 20l7.07-7.07a5 5 0 0 0-7.07-7.07L3 13a5 5 0 0 0 7 7l.5-.5.5.5ZM14 9l1 1"/>
                        </svg>
                    @elseif($alergia->tipo === 'alimento')
                        <svg class="w-3.5 h-3.5 text-orange-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 3v6a3 3 0 0 0 6 0V3M6 21V12m6 9v-4m3-14v18M18 3v18"/>
                        </svg>
                    @elseif($alergia->tipo === 'ambiental')
                        <svg class="w-3.5 h-3.5 text-green-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6.1 8H4m15.72 2.16c.25 1.47-.26 2.98-1.37 4.09a5.69 5.69 0 0 1-6.16 1.24L8 18H5v-3l3.51-3.49A5.62 5.62 0 0 1 7.5 5.6 5.7 5.7 0 0 1 16 4.1L13 7l1 3 3-1 1.72 1.16Z"/>
                        </svg>
                    @else
                        <svg class="w-3.5 h-3.5 text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v4m0 4h.01M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0Z"/>
                        </svg>
                    @endif
                </span>

                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-1.5 flex-wrap">
                        <span class="text-xs font-bold text-gray-900 dark:text-white">
                            {{ $alergia->descripcion }}
                        </span>
                        @if($alergia->severidad)
                            <span class="{{ $badge }}
                                @if($alergia->severidad === 'grave')
                                    bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300
                                @elseif($alergia->severidad === 'moderada')
                                    bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-300
                                @else
                                    bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-300
                                @endif">
                                {{ $alergia->severidad }}
                            </span>
                        @endif
                        <span class="{{ $badge }} bg-gray-100 text-gray-500 dark:bg-gray-800 dark:text-gray-400">
                            {{ $alergia->tipo }}
                        </span>
                    </div>
                    @if($alergia->reaccion)
                        <p class="text-[10px] text-gray-500 dark:text-gray-400 mt-0.5">{{ $alergia->reaccion }}</p>
                    @endif
                    @if($alergia->medicamento)
                        <p class="text-[10px] text-[#44B0B3] mt-0.5">
                            Vinculado al catálogo: {{ $alergia->medicamento->nombre }}
                        </p>
                    @endif
                </div>

                <form method="POST" action="{{ route('pacientes.alergias.destroy', $alergia) }}"
                      onsubmit="return confirm('¿Eliminar esta alergia?')">
                    @csrf @method('DELETE')
                    <button type="submit"
                        class="w-6 h-6 rounded-lg flex items-center justify-center text-gray-300 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 transition">
                        <svg class="w-3.5 h-3.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 7h14m-9 3v8m4-8v8M10 3h4a1 1 0 0 1 1 1v3H9V4a1 1 0 0 1 1-1ZM6 7h12v13a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1V7Z"/>
                        </svg>
                    </button>
                </form>
            </div>
        @endforeach
    </div>
@else
    <p class="text-xs text-gray-400 dark:text-gray-500 mb-4">Sin alergias registradas.</p>
@endif

{{-- ── Botón toggle ── --}}
<button type="button"
        onclick="document.getElementById('form-nueva-alergia').classList.toggle('hidden')"
        class="w-full flex items-center justify-center gap-1.5 rounded-2xl border border-dashed border-gray-300 dark:border-gray-700 py-2 text-xs font-semibold text-gray-500 hover:border-[#44B0B3] hover:text-[#44B0B3] transition mb-3">
    <svg class="w-3.5 h-3.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M12 5v14"/>
    </svg>
    Agregar alergia
</button>

{{-- ── Formulario nueva alergia ── --}}
<form id="form-nueva-alergia"
      method="POST"
      action="{{ route('pacientes.alergias.store', $paciente) }}"
      class="hidden space-y-3 p-3 rounded-2xl border border-[#44B0B3]/20 bg-[#44B0B3]/5 dark:bg-[#44B0B3]/10">
    @csrf

    {{-- Tipo --}}
    <div>
        <label class="{{ $labelSm }}">Tipo de alergia</label>
        <select name="tipo" id="tipo-alergia" onchange="onTipoChange()" class="{{ $inputSm }}">
            <option value="medicamento">Medicamento</option>
            <option value="alimento">Alimento</option>
            <option value="ambiental">Ambiental</option>
            <option value="otro">Otro</option>
        </select>
    </div>

    {{-- Catálogo — solo visible si tipo = medicamento --}}
    <div id="seccion-catalogo">
        <label class="{{ $labelSm }}">
            Buscar en catálogo
            <span class="normal-case font-normal text-gray-400">(opcional — autocompleta el nombre)</span>
        </label>
        <select name="medicamento_id"
                id="select-catalogo"
                onchange="onCatalogoChange()"
                class="{{ $inputSm }}">
            <option value="">— No está en el catálogo —</option>
            @foreach($medicamentos as $med)
                <option value="{{ $med->id }}" data-nombre="{{ $med->nombre }}">
                    {{ $med->nombre }}
                    @if($med->presentacion) · {{ $med->presentacion }}@endif
                    @if($med->concentracion) · {{ $med->concentracion }}@endif
                </option>
            @endforeach
        </select>
    </div>

    {{-- Nombre / descripción — siempre visible, label cambia según tipo --}}
    <div>
        <label class="{{ $labelSm }}" id="label-descripcion">
            Nombre del medicamento <span class="text-red-500">*</span>
        </label>
        <input type="text"
               name="descripcion"
               id="input-descripcion"
               class="{{ $inputSm }}"
               placeholder="Ej: Amoxicilina"
               required
               autocomplete="off">
    </div>

    {{-- Severidad --}}
    <div>
        <label class="{{ $labelSm }}">Severidad</label>
        <select name="severidad" class="{{ $inputSm }}">
            <option value="">Sin especificar</option>
            <option value="leve">Leve</option>
            <option value="moderada">Moderada</option>
            <option value="grave">Grave</option>
        </select>
    </div>

    {{-- Reacción --}}
    <div>
        <label class="{{ $labelSm }}">
            Reacción conocida
            <span class="normal-case font-normal text-gray-400">(opcional)</span>
        </label>
        <textarea name="reaccion" rows="2" class="{{ $inputSm }}"
            placeholder="Ej: urticaria, anafilaxia, edema..."></textarea>
    </div>

    <div class="flex justify-end gap-2 pt-1">
        <button type="button"
            onclick="document.getElementById('form-nueva-alergia').classList.add('hidden')"
            class="text-xs text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition px-2">
            Cancelar
        </button>
        <button type="submit"
            class="bg-[#44B0B3] hover:bg-[#389a9d] text-white text-xs font-bold px-4 py-2 rounded-xl transition">
            Guardar alergia
        </button>
    </div>
</form>

<script>
const tipoLabels = {
    medicamento: { label: 'Nombre del medicamento',    placeholder: 'Ej: Amoxicilina, Ibuprofeno...' },
    alimento:    { label: 'Nombre del alimento',       placeholder: 'Ej: Mariscos, Nueces, Leche...' },
    ambiental:   { label: 'Descripción del alérgeno',  placeholder: 'Ej: Polen, Ácaros, Polvo...'    },
    otro:        { label: 'Descripción de la alergia', placeholder: 'Ej: Níquel, Colorantes...'       },
};

function onTipoChange() {
    const tipo   = document.getElementById('tipo-alergia').value;
    const esMed  = tipo === 'medicamento';
    const config = tipoLabels[tipo] || tipoLabels['otro'];

    // Actualizar label y placeholder
    document.getElementById('label-descripcion').innerHTML =
        config.label + ' <span class="text-red-500">*</span>';
    document.getElementById('input-descripcion').placeholder = config.placeholder;

    // Catálogo solo para medicamentos
    document.getElementById('seccion-catalogo').style.display = esMed ? '' : 'none';

    // Si cambia de medicamento → limpiar select y campo
    if (!esMed) {
        document.getElementById('select-catalogo').value = '';
        document.getElementById('input-descripcion').readOnly = false;
        document.getElementById('input-descripcion').classList.remove(
            'bg-gray-50', 'dark:bg-gray-800', 'cursor-not-allowed', 'text-gray-500'
        );
    }
}

function onCatalogoChange() {
    const select    = document.getElementById('select-catalogo');
    const opt       = select.options[select.selectedIndex];
    const inputDesc = document.getElementById('input-descripcion');

    if (opt.value !== '') {
        // Seleccionó del catálogo → autocompleta el nombre y lo bloquea
        inputDesc.value    = opt.dataset.nombre;
        inputDesc.readOnly = true;
        inputDesc.classList.add('bg-gray-50', 'dark:bg-gray-800', 'cursor-not-allowed', 'text-gray-500');
    } else {
        // No en catálogo → nombre libre y editable
        inputDesc.value    = '';
        inputDesc.readOnly = false;
        inputDesc.classList.remove('bg-gray-50', 'dark:bg-gray-800', 'cursor-not-allowed', 'text-gray-500');
        inputDesc.focus();
    }
}

// Inicializar
onTipoChange();
</script>