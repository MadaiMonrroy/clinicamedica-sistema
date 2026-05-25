<x-app-layout>
    @php
        $pageWrap    = 'space-y-6';
        $pageHeaderCard = 'rounded-[2rem] border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 shadow-sm';
        $sectionCard = 'rounded-[2rem] border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 shadow-sm';
        $inputClass  = 'block w-full px-4 py-3 bg-white dark:bg-gray-800/40 border border-gray-200 dark:border-gray-700 focus:border-[#44B0B3] focus:ring-1 focus:ring-[#44B0B3] rounded-2xl text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 transition-all text-sm shadow-sm';
        $labelClass  = 'block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2';
        $primaryBtn  = 'inline-flex items-center justify-center rounded-2xl bg-[#44B0B3] hover:bg-[#389a9d] text-white font-bold px-5 py-3 shadow-lg shadow-[#44B0B3]/25 transition';
        $secondaryBtn = 'inline-flex items-center justify-center rounded-2xl border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-800 px-5 py-3 transition';
        $required = '<span class="text-red-500">*</span>';
    @endphp
<style>
/* ── Flowbite Datepicker: dark/light ────────────────────────── */
.datepicker-picker {
    background-color: #ffffff !important;
    border: 1px solid #e5e7eb !important;
    border-radius: 1rem !important;
    padding: 0.75rem !important;
    box-shadow: 0 10px 30px -8px rgba(0,0,0,.15) !important;
}
.dark .datepicker-picker {
    background-color: #1f2937 !important;
    border-color: #374151 !important;
}
.datepicker-controls .prev-btn,
.datepicker-controls .next-btn,
.datepicker-controls .view-switch {
    background: transparent !important;
    color: #374151 !important;
    font-weight: 600 !important;
    font-size: 0.875rem !important;
    border-radius: 0.5rem !important;
}
.dark .datepicker-controls .prev-btn,
.dark .datepicker-controls .next-btn,
.dark .datepicker-controls .view-switch {
    color: #f9fafb !important;
}
.datepicker-controls .prev-btn:hover,
.datepicker-controls .next-btn:hover,
.datepicker-controls .view-switch:hover {
    background-color: #f3f4f6 !important;
}
.dark .datepicker-controls .prev-btn:hover,
.dark .datepicker-controls .next-btn:hover,
.dark .datepicker-controls .view-switch:hover {
    background-color: #374151 !important;
}
.datepicker-grid .dow {
    color: #9ca3af !important;
    font-size: 0.7rem !important;
    font-weight: 700 !important;
    text-transform: uppercase !important;
    letter-spacing: 0.05em !important;
}
.dark .datepicker-grid .dow {
    color: #6b7280 !important;
}
.datepicker-grid .datepicker-cell {
    color: #374151 !important;
    font-size: 0.8rem !important;
    border-radius: 0.5rem !important;
}
.dark .datepicker-grid .datepicker-cell {
    color: #e5e7eb !important;
}
.datepicker-grid .datepicker-cell:not(.disabled):not(.selected):hover {
    background-color: rgba(82,171,177,.18) !important;
    color: #52ABB1 !important;
}
.datepicker-grid .datepicker-cell.selected,
.datepicker-grid .datepicker-cell.selected:hover {
    background-color: #52ABB1 !important;
    color: #ffffff !important;
    font-weight: 700 !important;
}
.datepicker-grid .datepicker-cell.today:not(.selected) {
    font-weight: 700 !important;
    color: #52ABB1 !important;
    background-color: rgba(82,171,177,.12) !important;
}
.datepicker-grid .datepicker-cell.disabled,
.datepicker-grid .datepicker-cell.disabled:hover {
    color: #d1d5db !important;
    background-color: transparent !important;
    cursor: not-allowed !important;
    opacity: 0.4 !important;
    pointer-events: none !important;
}
.dark .datepicker-grid .datepicker-cell.disabled,
.dark .datepicker-grid .datepicker-cell.disabled:hover {
    color: #4b5563 !important;
    opacity: 0.4 !important;
}
.datepicker-grid .datepicker-cell.prev-month,
.datepicker-grid .datepicker-cell.next-month {
    opacity: 0.35 !important;
}
</style>
    <div class=" mx-auto {{ $pageWrap }}">

        {{-- HEADER --}}
        <section class="{{ $pageHeaderCard }} p-5 sm:p-6">
            <p class="text-xs font-black uppercase tracking-[0.18em] text-gray-400 dark:text-gray-500">
                Recepción / Pacientes
            </p>
            <h1 class="mt-2 text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white">
                Nuevo paciente
            </h1>
            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400 max-w-2xl">
                Primero verifica si el paciente ya existe ingresando su CI. Si no está registrado, completa el formulario.
            </p>
        </section>

        @if($errors->any())
            <div class="rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700 dark:border-red-800 dark:bg-red-900/20 dark:text-red-300">
                Revisa los campos marcados en el formulario.
            </div>
        @endif

        <form method="POST" action="{{ route('recepcion.pacientes.store') }}" class="{{ $sectionCard }} overflow-hidden">
            @csrf

            <div class="px-5 sm:px-6 py-5 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white">Información del paciente</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">Completa la ficha base de identificación y contacto.</p>
            </div>

            <div class="px-5 sm:px-6 py-5 space-y-8">

                {{-- SECCIÓN IDENTIFICACIÓN --}}
                <section class="space-y-4">
                    <h4 class="text-sm font-black uppercase tracking-[0.15em] text-gray-400 dark:text-gray-500">
                        Identificación
                    </h4>

                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">

                        {{-- CAMPO CI con búsqueda en tiempo real --}}
                        <div class="xl:col-span-1">
                            <label class="{{ $labelClass }}">
                                CI {!! $required !!}
                                <span id="ci-estado" class="ml-2 text-xs font-normal"></span>
                            </label>
                            <div class="relative">
                                <input
                                    type="text"
                                    name="ci"
                                    id="ci"
                                    value="{{ old('ci') }}"
                                    class="{{ $inputClass }} pr-10"
                                    placeholder="Ej. 8465781"
                                    autocomplete="off"
                                >
                                {{-- Spinner --}}
                                <span id="ci-spinner" class="hidden absolute right-3 top-1/2 -translate-y-1/2">
                                    <svg class="animate-spin h-4 w-4 text-[#44B0B3]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
                                    </svg>
                                </span>
                            </div>
                            @error('ci') <p class="mt-2 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>

                        {{-- Nombres (oculto hasta confirmar que el CI no existe) --}}
                        <div class="xl:col-span-3" id="campo-nombres" style="display:none">
                            <label class="{{ $labelClass }}">Nombres {!! $required !!}</label>
                            <input type="text" name="nombres" value="{{ old('nombres') }}" class="{{ $inputClass }}" placeholder="Ej. Juan Carlos">
                            @error('nombres') <p class="mt-2 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>

                        <div class="xl:col-span-2" id="campo-ap" style="display:none">
                            <label class="{{ $labelClass }}">Apellido paterno {!! $required !!}</label>
                            <input type="text" name="apellido_paterno" value="{{ old('apellido_paterno') }}" class="{{ $inputClass }}" placeholder="Ej. Pérez">
                            @error('apellido_paterno') <p class="mt-2 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>

                        <div class="xl:col-span-2" id="campo-am" style="display:none">
                            <label class="{{ $labelClass }}">Apellido materno</label>
                            <input type="text" name="apellido_materno" value="{{ old('apellido_materno') }}" class="{{ $inputClass }}" placeholder="Ej. López">
                            @error('apellido_materno') <p class="mt-2 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>

                    </div>

                    {{-- TARJETA: paciente encontrado --}}
                    <div id="paciente-encontrado" class="hidden">
                        <div class="rounded-2xl border border-[#44B0B3]/40 bg-[#44B0B3]/5 dark:bg-[#44B0B3]/10 px-5 py-4">
                            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                                <div class="space-y-1">
                                    <p class="text-xs font-black uppercase tracking-[0.15em] text-[#44B0B3]">
                                        Paciente ya registrado
                                    </p>
                                    <p id="pf-nombre" class="text-base font-bold text-gray-900 dark:text-white"></p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        CI: <span id="pf-ci" class="font-medium text-gray-700 dark:text-gray-200"></span>
                                        &nbsp;·&nbsp;
                                        Tel: <span id="pf-tel" class="font-medium text-gray-700 dark:text-gray-200"></span>
                                        &nbsp;·&nbsp;
                                        <span id="pf-sexo"></span>, <span id="pf-edad"></span> años
                                    </p>
                                </div>
                                <div class="flex flex-wrap gap-2 shrink-0">
                                    <a id="btn-historial" href="#"
                                       class="inline-flex items-center rounded-2xl border border-[#44B0B3] text-[#44B0B3] hover:bg-[#44B0B3]/10 font-bold px-4 py-2 text-sm transition">
                                        Ver historial
                                    </a>
                                    <a id="btn-ingreso" href="#"
                                       class="inline-flex items-center rounded-2xl bg-[#44B0B3] hover:bg-[#389a9d] text-white font-bold px-4 py-2 text-sm shadow-lg shadow-[#44B0B3]/25 transition">
                                        Nuevo ingreso
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                </section>

                {{-- RESTO DEL FORMULARIO (oculto hasta que CI no exista) --}}
                <div id="resto-formulario" style="display:none" class="space-y-8">

                    <section class="space-y-4">
                        <h4 class="text-sm font-black uppercase tracking-[0.15em] text-gray-400 dark:text-gray-500">
                            Datos personales
                        </h4>

                        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
                            <div>
    <label class="{{ $labelClass }}">Fecha de nacimiento {!! $required !!}</label>
    <div class="relative">
        <div class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none z-10">
            <svg class="w-4 h-4 text-gray-400 dark:text-gray-500"
                 fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M4 10h16m-8-3V4M7 7V4m10 3V4M5 20h14a1 1 0 001-1V7a1 1 0 00-1-1H5a1 1 0 00-1 1v12a1 1 0 001 1z"/>
            </svg>
        </div>
        <input id="fecha-display-create"
               datepicker
               datepicker-autohide
               datepicker-language="es"
               datepicker-format="dd/mm/yyyy"
               datepicker-max-date="{{ now()->format('d/m/Y') }}"
               type="text"
               placeholder="dd/mm/aaaa"
               autocomplete="off"
               class="{{ $inputClass }} pl-10"
               value="{{ old('fecha_display') }}">
        <input type="hidden"
               id="fecha-hidden-create"
               name="fecha_nacimiento"
               value="{{ old('fecha_nacimiento') }}">
    </div>
    <p class="mt-1 text-xs text-gray-400 dark:text-gray-600">Máximo: {{ now()->format('d/m/Y') }}</p>
    @error('fecha_nacimiento') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
</div>

                            <div>
    <label class="{{ $labelClass }}">Sexo {!! $required !!}</label>
    <div x-data="{
            open: false,
            value: '{{ old('sexo') }}',
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
                         class="w-4 h-4 flex-shrink-0" style="color:#52ABB1"
                         fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                    </svg>
                </button>
            </template>
        </div>
    </div>
    @error('sexo') <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p> @enderror
</div>

                            <div class="xl:col-span-2">
                                <label class="{{ $labelClass }}">Teléfono {!! $required !!}</label>
                                <input type="text" name="telefono" value="{{ old('telefono') }}" class="{{ $inputClass }}" placeholder="Ej. 71234567">
                                @error('telefono') <p class="mt-2 text-xs text-red-500">{{ $message }}</p> @enderror
                            </div>

                            <div class="xl:col-span-2">
                                <label class="{{ $labelClass }}">Correo </label>
                                <input type="email" name="email" value="{{ old('email') }}" class="{{ $inputClass }}" placeholder="Ej. paciente@gmail.com">
                                
                            </div>

                            <div class="xl:col-span-2">
                                <label class="{{ $labelClass }}">Dirección {!! $required !!}</label>
                                <input type="text" name="direccion" value="{{ old('direccion') }}" class="{{ $inputClass }}" placeholder="Ej. Av. Banzer, Santa Cruz">
                                @error('direccion') <p class="mt-2 text-xs text-red-500">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </section>

                    <section class="space-y-4">
                        <h4 class="text-sm font-black uppercase tracking-[0.15em] text-gray-400 dark:text-gray-500">
                            Observaciones
                        </h4>
                        <div>
                            <label class="{{ $labelClass }}">Notas adicionales</label>
                            <textarea name="observacion" rows="4" class="{{ $inputClass }}" placeholder="Alergias, observaciones rápidas o datos relevantes">{{ old('observacion') }}</textarea>
                            @error('observacion') <p class="mt-2 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>
                    </section>

                </div>
            </div>

            {{-- FOOTER DEL FORM (oculto hasta que CI no exista) --}}
            <div id="form-footer" style="display:none"
                 class="px-5 sm:px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900">
                <div class="flex items-center justify-end gap-3">
                    <a href="{{ route('recepcion.pacientes.index') }}" class="{{ $secondaryBtn }}">
                        Cancelar
                    </a>
                    <button type="submit" class="{{ $primaryBtn }}">
                        Guardar paciente
                    </button>
                </div>
            </div>

        </form>
    </div>

    {{-- ── JAVASCRIPT ── --}}
    @push('scripts')
    <script>
    (function () {
        const ciInput        = document.getElementById('ci');
        const spinner        = document.getElementById('ci-spinner');
        const estadoLabel    = document.getElementById('ci-estado');
        const cardEncontrado = document.getElementById('paciente-encontrado');
        const restoForm      = document.getElementById('resto-formulario');
        const formFooter     = document.getElementById('form-footer');

        // Campos de identificación que se muestran solo si el CI es nuevo
        const camposIdent = [
            document.getElementById('campo-nombres'),
            document.getElementById('campo-ap'),
            document.getElementById('campo-am'),
        ];

        // Elementos de la tarjeta de paciente encontrado
        const pfNombre = document.getElementById('pf-nombre');
        const pfCi     = document.getElementById('pf-ci');
        const pfTel    = document.getElementById('pf-tel');
        const pfSexo   = document.getElementById('pf-sexo');
        const pfEdad   = document.getElementById('pf-edad');
        const btnHistorial = document.getElementById('btn-historial');
        const btnIngreso   = document.getElementById('btn-ingreso');

        const urlBuscar = "{{ route('recepcion.pacientes.buscar-ci') }}";
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content ?? '';

        let debounceTimer = null;

        function mostrarFormularioNuevo() {
            camposIdent.forEach(el => el.style.display = '');
            restoForm.style.display   = '';
            formFooter.style.display  = '';
            cardEncontrado.classList.add('hidden');
            estadoLabel.textContent   = '✓ CI disponible';
            estadoLabel.className     = 'ml-2 text-xs font-normal text-emerald-500';
        }

        function mostrarPacienteExistente(p) {
            pfNombre.textContent       = p.nombre_completo;
            pfCi.textContent           = p.ci;
            pfTel.textContent          = p.telefono;
            pfSexo.textContent         = p.sexo === 'M' ? 'Masculino' : p.sexo === 'F' ? 'Femenino' : 'Otro';
            pfEdad.textContent         = p.edad ?? '—';
            btnHistorial.href          = p.url_historial;
            btnIngreso.href            = p.url_ingreso;

            camposIdent.forEach(el => el.style.display = 'none');
            restoForm.style.display    = 'none';
            formFooter.style.display   = 'none';
            cardEncontrado.classList.remove('hidden');
            estadoLabel.textContent    = '⚠ Ya registrado';
            estadoLabel.className      = 'ml-2 text-xs font-normal text-amber-500';
        }

        function resetear() {
            camposIdent.forEach(el => el.style.display = 'none');
            restoForm.style.display    = 'none';
            formFooter.style.display   = 'none';
            cardEncontrado.classList.add('hidden');
            estadoLabel.textContent    = '';
        }

        ciInput.addEventListener('input', function () {
            clearTimeout(debounceTimer);
            const ci = this.value.trim();

            if (ci.length < 4) {
                resetear();
                return;
            }

            spinner.classList.remove('hidden');

            debounceTimer = setTimeout(async () => {
                try {
                    const res  = await fetch(`${urlBuscar}?ci=${encodeURIComponent(ci)}`, {
                        headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }
                    });
                    const data = await res.json();

                    if (data.encontrado) {
                        mostrarPacienteExistente(data.paciente);
                    } else {
                        mostrarFormularioNuevo();
                    }
                } catch (e) {
                    mostrarFormularioNuevo(); // si falla la red, permite seguir
                } finally {
                    spinner.classList.add('hidden');
                }
            }, 400); // espera 400ms después de que deja de escribir
        });

        // Si hay un error de validación y ya venía CI, mostrar el formulario completo
        @if(old('ci'))
            mostrarFormularioNuevo();
        @endif

    })();
    // Sincroniza el datepicker de fecha de nacimiento con el input hidden
document.addEventListener('DOMContentLoaded', () => {
    const display = document.getElementById('fecha-display-create');
    const hidden  = document.getElementById('fecha-hidden-create');

    if (display && hidden) {
        display.addEventListener('changeDate', (e) => {
            // Convierte dd/mm/yyyy → yyyy-mm-dd para el servidor
            const val = e.detail.date;
            if (val) {
                const d = new Date(val);
                const yyyy = d.getFullYear();
                const mm   = String(d.getMonth() + 1).padStart(2, '0');
                const dd   = String(d.getDate()).padStart(2, '0');
                hidden.value = `${yyyy}-${mm}-${dd}`;
            } else {
                hidden.value = '';
            }
        });
    }
});
    </script>
    @endpush

</x-app-layout>