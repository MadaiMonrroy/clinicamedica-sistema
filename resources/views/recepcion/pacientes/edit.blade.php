<x-app-layout>
@php
    $card       = 'rounded-[2rem] border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 shadow-sm';
    $inputClass = 'block w-full px-4 py-3 rounded-2xl text-sm
                   bg-white dark:bg-gray-800
                   border border-gray-200 dark:border-gray-700
                   text-gray-900 dark:text-white
                   placeholder-gray-400 dark:placeholder-gray-500
                   focus:outline-none focus:ring-2 focus:ring-[#52ABB1]/40 focus:border-[#52ABB1]
                   transition';
    $labelClass = 'block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5';
    $maxDate    = now()->format('d/m/Y');
@endphp



<div class="space-y-6">

    {{-- ── HEADER ── --}}
    <section class="{{ $card }} p-5 sm:p-6">
        <div class="flex items-start justify-between gap-4">
            <div>
                <p class="text-xs font-black uppercase tracking-[0.18em] text-gray-400 dark:text-gray-500">
                    Recepción / Pacientes
                </p>
                <h1 class="mt-1.5 text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white">
                    Editar paciente
                </h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Actualizando datos de
                    <span class="font-semibold text-gray-700 dark:text-gray-200">
                        {{ $paciente->nombre_completo }}
                    </span>
                </p>
            </div>
            @php
                $iniciales   = strtoupper(substr($paciente->nombres,0,1).substr($paciente->apellido_paterno,0,1));
                $avatarStyle = match($paciente->sexo) {
                    'M'     => 'background:rgba(59,130,246,.12);color:#1d4ed8',
                    'F'     => 'background:rgba(236,72,153,.12);color:#9d174d',
                    default => 'background:rgba(82,171,177,.12);color:#0e7490',
                };
            @endphp
            <div class="w-14 h-14 rounded-2xl flex items-center justify-center
                        text-lg font-bold flex-shrink-0"
                 style="{{ $avatarStyle }}">
                {{ $iniciales }}
            </div>
        </div>
    </section>

    {{-- ── ERRORES ── --}}
    @if($errors->any())
        <div class="flex items-start gap-3 px-4 py-3 rounded-2xl text-sm
                    bg-red-50 text-red-700 border border-red-200
                    dark:bg-red-900/20 dark:text-red-300 dark:border-red-800">
            <svg class="w-4 h-4 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor"
                 stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71
                         c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126z"/>
            </svg>
            Revisa los campos marcados.
        </div>
    @endif

    {{-- ── FORMULARIO ── --}}
    <form method="POST"
          action="{{ route('recepcion.pacientes.update', $paciente) }}"
          class="{{ $card }} overflow-hidden"
          id="form-editar">
        @csrf
        @method('PUT')

        <div class="px-5 sm:px-6 py-5 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-base font-bold text-gray-900 dark:text-white">Información del paciente</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">
                Los cambios se guardarán en la ficha clínica.
            </p>
        </div>

        <div class="px-5 sm:px-6 py-6 space-y-8">

            {{-- ══ IDENTIFICACIÓN ══ --}}
            <section class="space-y-4">
                <h4 class="text-xs font-black uppercase tracking-[0.18em] text-gray-400 dark:text-gray-500">
                    Identificación
                </h4>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">

                    {{-- CI --}}
                    <div>
                        <label class="{{ $labelClass }}">CI <span class="text-red-500">*</span></label>
                        <input type="text" name="ci"
                               value="{{ old('ci', $paciente->ci) }}"
                               class="{{ $inputClass }}"
                               placeholder="Ej. 8465781" required>
                        @error('ci')<p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>@enderror
                    </div>

                    {{-- Estado — select Alpine inline --}}
                    <div>
                        <label class="{{ $labelClass }}">Estado <span class="text-red-500">*</span></label>
                        <div x-data="{
                                open: false,
                                value: '{{ old('estado', $paciente->estado) }}',
                                options: [
                                    { value: 'activo',   label: 'Activo',   dot: '#10b981' },
                                    { value: 'inactivo', label: 'Inactivo', dot: '#f59e0b' },
                                ],
                                get selected() {
                                    return this.options.find(o => o.value === this.value) ?? this.options[0];
                                }
                             }"
                             class="relative">
                            <input type="hidden" name="estado" :value="value">
                            <button type="button"
                                    @click="open = !open"
                                    @keydown.escape="open = false"
                                    class="{{ $inputClass }} flex items-center justify-between gap-2">
                                <span class="flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-full flex-shrink-0"
                                          :style="'background:' + selected.dot"></span>
                                    <span x-text="selected.label"></span>
                                </span>
                                <svg class="w-4 h-4 text-gray-400 flex-shrink-0 transition-transform duration-200"
                                     :class="open ? 'rotate-180' : ''"
                                     fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>
                            <div x-show="open"
                                 x-cloak
                                 @click.outside="open = false"
                                 class="absolute z-30 left-0 right-0 mt-1
                                        bg-white dark:bg-gray-800
                                        border border-gray-200 dark:border-gray-700
                                        rounded-2xl shadow-xl overflow-hidden">
                                <template x-for="opt in options" :key="opt.value">
                                    <button type="button"
                                            @click="value = opt.value; open = false"
                                            class="w-full flex items-center gap-3 px-4 py-2.5 text-sm
                                                   border-b border-gray-100 dark:border-gray-700/60 last:border-0
                                                   hover:bg-gray-50 dark:hover:bg-gray-700/60 transition"
                                            :class="value === opt.value
                                                ? 'font-semibold text-gray-900 dark:text-white'
                                                : 'text-gray-600 dark:text-gray-300'">
                                        <span class="w-2 h-2 rounded-full flex-shrink-0"
                                              :style="'background:' + opt.dot"></span>
                                        <span x-text="opt.label"></span>
                                        <svg x-show="value === opt.value"
                                             class="ml-auto w-4 h-4 flex-shrink-0"
                                             style="color:#52ABB1"
                                             fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    </button>
                                </template>
                            </div>
                        </div>
                        @error('estado')<p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>@enderror
                    </div>

                    {{-- Nombres --}}
                    <div>
                        <label class="{{ $labelClass }}">Nombres <span class="text-red-500">*</span></label>
                        <input type="text" name="nombres"
                               value="{{ old('nombres', $paciente->nombres) }}"
                               class="{{ $inputClass }}"
                               placeholder="Ej. Juan Carlos" required>
                        @error('nombres')<p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>@enderror
                    </div>

                    {{-- Apellido paterno --}}
                    <div>
                        <label class="{{ $labelClass }}">Apellido paterno <span class="text-red-500">*</span></label>
                        <input type="text" name="apellido_paterno"
                               value="{{ old('apellido_paterno', $paciente->apellido_paterno) }}"
                               class="{{ $inputClass }}"
                               placeholder="Ej. Pérez" required>
                        @error('apellido_paterno')<p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>@enderror
                    </div>

                    {{-- Apellido materno --}}
                    <div>
                        <label class="{{ $labelClass }}">Apellido materno</label>
                        <input type="text" name="apellido_materno"
                               value="{{ old('apellido_materno', $paciente->apellido_materno) }}"
                               class="{{ $inputClass }}"
                               placeholder="Ej. López">
                        @error('apellido_materno')<p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>@enderror
                    </div>

                </div>
            </section>

            {{-- ══ DATOS PERSONALES ══ --}}
            <section class="space-y-4">
                <h4 class="text-xs font-black uppercase tracking-[0.18em] text-gray-400 dark:text-gray-500">
                    Datos personales
                </h4>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">

                    {{-- Fecha nacimiento --}}
                    <div>
                        <label class="{{ $labelClass }}">
                            Fecha de nacimiento <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none z-10">
                                <svg class="w-4 h-4 text-gray-400 dark:text-gray-500"
                                     fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M4 10h16m-8-3V4M7 7V4m10 3V4M5 20h14a1 1 0 001-1V7a1 1 0 00-1-1H5a1 1 0 00-1 1v12a1 1 0 001 1z"/>
                                </svg>
                            </div>
                            <input id="fecha-display"
       name="fecha_display"
       datepicker
       datepicker-autohide
       datepicker-format="dd/mm/yyyy"
       datepicker-max-date="{{ $maxDate }}"
       type="text"
       placeholder="dd/mm/aaaa"
       autocomplete="off"
       class="{{ $inputClass }} pl-10"
       value="{{ old('fecha_display', $paciente->fecha_nacimiento?->format('d/m/Y')) }}">
                            <input type="hidden"
                                   id="fecha-hidden"
                                   name="fecha_nacimiento"
                                   value="{{ old('fecha_nacimiento', $paciente->fecha_nacimiento?->format('Y-m-d')) }}">
                        </div>
                        <p class="mt-1 text-xs text-gray-400 dark:text-gray-600">
                            Máximo: {{ now()->format('d/m/Y') }}
                        </p>
                        @error('fecha_nacimiento')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                    </div>

                    {{-- Sexo — select Alpine inline --}}
                    <div>
                        <label class="{{ $labelClass }}">Sexo <span class="text-red-500">*</span></label>
                        <div x-data="{
                                open: false,
                                value: '{{ old('sexo', $paciente->sexo) }}',
                                options: [
                                    { value: 'M',    label: 'Masculino' },
                                    { value: 'F',    label: 'Femenino'  },
                                    { value: 'OTRO', label: 'Otro'      },
                                ],
                                get selected() {
                                    return this.options.find(o => o.value === this.value)
                                        ?? { label: 'Seleccione', value: '' };
                                }
                             }"
                             class="relative">
                            <input type="hidden" name="sexo" :value="value">
                            <button type="button"
                                    @click="open = !open"
                                    @keydown.escape="open = false"
                                    class="{{ $inputClass }} flex items-center justify-between gap-2">
                                <span :class="value ? 'text-gray-900 dark:text-white' : 'text-gray-400 dark:text-gray-500'"
                                      x-text="selected.label">
                                </span>
                                <svg class="w-4 h-4 text-gray-400 flex-shrink-0 transition-transform duration-200"
                                     :class="open ? 'rotate-180' : ''"
                                     fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>
                            <div x-show="open"
                                 x-cloak
                                 @click.outside="open = false"
                                 class="absolute z-30 left-0 right-0 mt-1
                                        bg-white dark:bg-gray-800
                                        border border-gray-200 dark:border-gray-700
                                        rounded-2xl shadow-xl overflow-hidden">
                                <template x-for="opt in options" :key="opt.value">
                                    <button type="button"
                                            @click="value = opt.value; open = false"
                                            class="w-full flex items-center justify-between px-4 py-2.5 text-sm
                                                   border-b border-gray-100 dark:border-gray-700/60 last:border-0
                                                   hover:bg-gray-50 dark:hover:bg-gray-700/60 transition"
                                            :class="value === opt.value
                                                ? 'font-semibold text-gray-900 dark:text-white'
                                                : 'text-gray-600 dark:text-gray-300'">
                                        <span x-text="opt.label"></span>
                                        <svg x-show="value === opt.value"
                                             class="w-4 h-4 flex-shrink-0"
                                             style="color:#52ABB1"
                                             fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    </button>
                                </template>
                            </div>
                        </div>
                        @error('sexo')<p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>@enderror
                    </div>

                    {{-- Teléfono --}}
                    <div>
                        <label class="{{ $labelClass }}">Teléfono <span class="text-red-500">*</span></label>
                        <input type="text" name="telefono"
                               value="{{ old('telefono', $paciente->telefono) }}"
                               class="{{ $inputClass }}"
                               placeholder="Ej. 71234567">
                        @error('telefono')<p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>@enderror
                    </div>

                    {{-- Correo --}}
                    <div>
                        <label class="{{ $labelClass }}">Correo electrónico</label>
                        <input type="email" name="email"
                               value="{{ old('email', $paciente->email) }}"
                               class="{{ $inputClass }}"
                               placeholder="Ej. paciente@gmail.com">
                        @error('email')<p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>@enderror
                    </div>

                    {{-- Dirección --}}
                    <div class="sm:col-span-2 lg:col-span-4">
                        <label class="{{ $labelClass }}">Dirección <span class="text-red-500">*</span></label>
                        <input type="text" name="direccion"
                               value="{{ old('direccion', $paciente->direccion) }}"
                               class="{{ $inputClass }}"
                               placeholder="Ej. Av. Banzer #123, Santa Cruz">
                        @error('direccion')<p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>@enderror
                    </div>

                </div>
            </section>

            {{-- ══ OBSERVACIONES ══ --}}
            <section class="space-y-4">
                <h4 class="text-xs font-black uppercase tracking-[0.18em] text-gray-400 dark:text-gray-500">
                    Observaciones
                </h4>
                <div>
                    <label class="{{ $labelClass }}">Notas adicionales</label>
                    <textarea name="observacion" rows="3"
                              class="{{ $inputClass }} resize-none"
                              placeholder="Alergias, condiciones previas o notas relevantes">{{ old('observacion', $paciente->observacion) }}</textarea>
                    @error('observacion')<p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>
            </section>

        </div>

        {{-- ── FOOTER ── --}}
        <div class="px-5 sm:px-6 py-4 border-t border-gray-200 dark:border-gray-700
                    bg-gray-50 dark:bg-gray-800/50">
            <div class="flex items-center justify-between gap-3">
                <a href="{{ route('recepcion.pacientes.show', $paciente) }}"
                   class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold
                          border border-gray-200 dark:border-gray-700
                          text-gray-600 dark:text-gray-400
                          hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                    Cancelar
                </a>
                <button type="submit"
                        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl
                               text-sm font-bold text-white transition
                               hover:opacity-90 active:scale-[.98]"
                        style="background:#52ABB1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                    </svg>
                    Guardar cambios
                </button>
            </div>
        </div>

    </form>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const displayInput = document.getElementById('fecha-display');
    const hiddenInput  = document.getElementById('fecha-hidden');
    const form         = document.getElementById('form-editar');

    if (!displayInput || !hiddenInput || !form) return;

    displayInput.addEventListener('changeDate', sincronizarFecha);
    displayInput.addEventListener('change', sincronizarFecha);

    form.addEventListener('submit', function (e) {
        sincronizarFecha();

        if (hiddenInput.value) {
            const fecha = new Date(hiddenInput.value + 'T00:00:00');
            const hoy   = new Date();
            hoy.setHours(23, 59, 59, 999);

            if (fecha > hoy) {
                e.preventDefault();
                mostrarError('La fecha de nacimiento no puede ser posterior a hoy.');
            }
        }
    });

    function sincronizarFecha() {
        const raw = displayInput.value?.trim();

        if (!raw) { hiddenInput.value = ''; limpiarError(); return; }

        const parts = raw.split('/');
        if (parts.length !== 3) { hiddenInput.value = ''; return; }

        let [dd, mm, yyyy] = parts;
        dd = String(dd).padStart(2, '0');
        mm = String(mm).padStart(2, '0');

        const day = Number(dd), month = Number(mm), year = Number(yyyy);
        if (!day || !month || !year) { hiddenInput.value = ''; return; }

        const sel = new Date(year, month - 1, day);
        const hoy = new Date();
        hoy.setHours(23, 59, 59, 999);

        const esFechaValida =
            sel.getFullYear() === year &&
            sel.getMonth() === month - 1 &&
            sel.getDate() === day;

        if (!esFechaValida) { hiddenInput.value = ''; mostrarError('La fecha ingresada no es válida.'); return; }
        if (sel > hoy) { hiddenInput.value = ''; displayInput.value = ''; mostrarError('La fecha no puede ser posterior a hoy.'); return; }

        hiddenInput.value = `${year}-${mm}-${dd}`;
        limpiarError();
    }

    function mostrarError(msg) {
        displayInput.classList.add('border-red-400');
        let el = document.getElementById('fecha-err');
        if (!el) {
            el = document.createElement('p');
            el.id = 'fecha-err';
            el.className = 'mt-1.5 text-xs text-red-500';
            displayInput.closest('.relative').insertAdjacentElement('afterend', el);
        }
        el.textContent = msg;
    }

    function limpiarError() {
        displayInput.classList.remove('border-red-400');
        document.getElementById('fecha-err')?.remove();
    }

    sincronizarFecha();
});
</script>
@endpush

</x-app-layout>