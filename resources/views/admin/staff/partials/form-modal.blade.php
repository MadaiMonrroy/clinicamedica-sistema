<div id="staffModalBackdrop" class="fixed inset-0 bg-black/50 z-40 hidden"></div>

<div id="staffModal" class="fixed inset-0 z-50 hidden items-center justify-center p-2 sm:p-4">
    <div
        x-data="staffFormComponent()"
        x-init="init()"
        class="w-full max-w-5xl h-[92vh] sm:h-[90vh] rounded-[2rem] bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 shadow-2xl overflow-hidden flex flex-col"
    >
        {{-- Header --}}
        <div class="shrink-0 flex items-start justify-between px-5 sm:px-6 py-4 sm:py-5 border-b border-gray-200 dark:border-gray-700">
            <div class="pr-4">
                <h3 id="staffModalTitle" class="text-lg sm:text-xl font-bold text-gray-800 dark:text-white">
                    Nuevo personal
                </h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Completa la información del personal clínico o administrativo.
                </p>
            </div>

            <button type="button" onclick="closeStaffModal()" class="shrink-0 p-2 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-800">
                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <form id="staffForm" method="POST" action="{{ route('admin.staff.store') }}" class="flex-1 min-h-0 flex flex-col" novalidate>
            @csrf
            <input type="hidden" name="_method" id="staffFormMethod" value="POST">

            {{-- Body --}}
            <div class="flex-1 min-h-0 overflow-y-auto px-5 sm:px-6 py-5 custom-scroll">
                <div class="space-y-8">
                    {{-- INFORMACIÓN PERSONAL --}}
                    <section class="space-y-4">
                        <div>
                            <h4 class="text-sm font-black uppercase tracking-[0.15em] text-gray-400 dark:text-gray-500">
                                Información personal
                            </h4>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div class="md:col-span-2">
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Nombres</label>
                                <input
                                    type="text"
                                    name="name"
                                    id="staff_name"
                                    data-label="Nombres"
                                    placeholder="Ej. Juan Carlos"
                                    class="block w-full px-4 py-3 bg-white dark:bg-gray-800/40 border border-gray-200 dark:border-gray-700 focus:border-[#44B0B3] focus:ring-1 focus:ring-[#44B0B3] rounded-2xl text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 transition-all text-sm shadow-sm"
                                    required
                                >
                            </div>

                            <div class="md:col-span-1">
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Apellido paterno</label>
                                <input
                                    type="text"
                                    name="apellido_paterno"
                                    id="staff_apellido_paterno"
                                    data-label="Apellido paterno"
                                    placeholder="Ej. Pérez"
                                    class="block w-full px-4 py-3 bg-white dark:bg-gray-800/40 border border-gray-200 dark:border-gray-700 focus:border-[#44B0B3] focus:ring-1 focus:ring-[#44B0B3] rounded-2xl text-gray-900 dark:text-white transition-all text-sm shadow-sm placeholder-gray-400 dark:placeholder-gray-500"
                                    required
                                >
                            </div>

                            <div class="md:col-span-1">
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Apellido materno</label>
                                <input
                                    type="text"
                                    name="apellido_materno"
                                    id="staff_apellido_materno"
                                    data-label="Apellido materno"
                                    placeholder="Ej. López"
                                    class="block w-full px-4 py-3 bg-white dark:bg-gray-800/40 border border-gray-200 dark:border-gray-700 focus:border-[#44B0B3] focus:ring-1 focus:ring-[#44B0B3] rounded-2xl text-gray-900 dark:text-white transition-all text-sm shadow-sm placeholder-gray-400 dark:placeholder-gray-500"
                                >
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">CI</label>
                                <input
                                    type="text"
                                    name="ci"
                                    id="staff_ci"
                                    data-label="CI"
                                    placeholder="Ej. 8465781"
                                    class="block w-full px-4 py-3 bg-white dark:bg-gray-800/40 border border-gray-200 dark:border-gray-700 focus:border-[#44B0B3] focus:ring-1 focus:ring-[#44B0B3] rounded-2xl text-gray-900 dark:text-white transition-all text-sm shadow-sm placeholder-gray-400 dark:placeholder-gray-500"
                                    required
                                >
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Teléfono</label>
                                <input
                                    type="text"
                                    name="telefono"
                                    id="staff_telefono"
                                    data-label="Teléfono"
                                    placeholder="Ej. 71234567"
                                    class="block w-full px-4 py-3 bg-white dark:bg-gray-800/40 border border-gray-200 dark:border-gray-700 focus:border-[#44B0B3] focus:ring-1 focus:ring-[#44B0B3] rounded-2xl text-gray-900 dark:text-white transition-all text-sm shadow-sm placeholder-gray-400 dark:placeholder-gray-500"
                                    required
                                    >
                            </div>
                        </div>
                    </section>

                    {{-- ACCESO Y FUNCIÓN --}}
                    <section class="space-y-4">
                        <div>
                            <h4 class="text-sm font-black uppercase tracking-[0.15em] text-gray-400 dark:text-gray-500">
                                Acceso y función
                            </h4>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-6 gap-4">
                            <div class="md:col-span-4">
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Correo</label>
                                <input
                                    type="email"
                                    name="email"
                                    id="staff_email"
                                    data-label="Correo"
                                    placeholder="Ej. juan.carlos@gmail.com"
                                    class="block w-full px-4 py-3 bg-white dark:bg-gray-800/40 border border-gray-200 dark:border-gray-700 focus:border-[#44B0B3] focus:ring-1 focus:ring-[#44B0B3] rounded-2xl text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 transition-all text-sm shadow-sm"
                                    required
                                >
                            </div>

                            {{-- Rol custom --}}
                            <div class="md:col-span-2">
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Rol</label>

                                <div class="relative">
                                    <input type="hidden" name="rol" id="staff_rol" :value="roleValue" data-label="Rol">


                                    <button
                                        type="button"
                                        @click="roleOpen = !roleOpen"
                                        @click.outside="roleOpen = false"
                                        class="inline-flex w-full items-center justify-between px-4 py-3 text-sm leading-5 font-medium rounded-2xl
                                               text-gray-700 dark:text-gray-300
                                               bg-white dark:bg-gray-800
                                               border border-gray-200 dark:border-gray-700
                                               hover:text-gray-900 dark:hover:text-white
                                               focus:outline-none focus:ring-1 focus:ring-[#44B0B3] focus:border-[#44B0B3]
                                               transition ease-in-out duration-150 shadow-sm"
                                    >
                                        <span x-text="roleLabel"></span>

                                        <svg class="fill-current h-4 w-4 text-gray-400 ms-3 shrink-0 transition-transform duration-200" :class="{ 'rotate-180': roleOpen }" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                    </button>

                                    <div
                                        x-show="roleOpen"
                                        x-transition
                                        x-cloak
                                        class="absolute z-50 mt-2 w-full rounded-2xl overflow-hidden border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-xl"
                                    >
                                        <button type="button" @click="setRole('admin'); roleOpen = false" class="block w-full px-4 py-3 text-left text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition">Administrador</button>
                                        <button type="button" @click="setRole('medico'); roleOpen = false" class="block w-full px-4 py-3 text-left text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition">Médico</button>
                                        <button type="button" @click="setRole('enfermera'); roleOpen = false" class="block w-full px-4 py-3 text-left text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition">Enfermera</button>
                                        <button type="button" @click="setRole('recepcionista'); roleOpen = false" class="block w-full px-4 py-3 text-left text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition">Recepcionista</button>
                                    </div>
                                </div>
                            </div>

                            {{-- Especialidad custom autocomplete --}}
                            <div class="md:col-span-3 " x-show="showProfessionalFields" x-transition>
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Especialidad</label>

                                <div class="relative">
                                    <input
                                        type="text"
                                        name="especialidad"
                                        id="staff_especialidad"
                                        data-label="Especialidad"
                                        x-model="especialidadInput"
                                        @input="filterEspecialidades()"
                                        @focus="filterEspecialidades(); especialidadOpen = true"
                                        @click.outside="especialidadOpen = false; normalizeEspecialidad()"
                                        autocomplete="off"
                                        class="block w-full px-4 py-3 pr-10 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 focus:border-[#44B0B3] focus:ring-1 focus:ring-[#44B0B3] rounded-2xl text-gray-900 dark:text-white placeholder-gray-400 transition-all text-sm shadow-sm"
                                        placeholder="Ej. Ginecología"
                                        :required="professionalFieldsRequired"
                                    >

                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-4">
                                        <svg class="fill-current h-4 w-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                    </div>

                                    <div
                                        x-show="especialidadOpen && filteredEspecialidades.length"
                                        x-transition
                                        x-cloak
                                        class="absolute z-50 mt-2 w-full max-h-52 overflow-y-auto rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-xl custom-scroll"
                                    >
                                        <template x-for="item in filteredEspecialidades" :key="item">
                                            <button
                                                type="button"
                                                @click="selectEspecialidad(item)"
                                                class="block w-full px-4 py-3 text-left text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition"
                                                x-text="item"
                                            ></button>
                                        </template>
                                    </div>
                                </div>
                            </div>

                            {{-- Cargo custom autocomplete --}}
                            <div class="md:col-span-3" x-show="showProfessionalFields" x-transition>
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Cargo</label>

                                <div class="relative">
                                    <input
                                        type="text"
                                        name="cargo"
                                        id="staff_cargo"
                                        data-label="Cargo"
                                        x-model="cargoInput"
                                        @input="filterCargos()"
                                        @focus="filterCargos(); cargoOpen = true"
                                        @click.outside="cargoOpen = false; normalizeCargo()"
                                        autocomplete="off"
                                        class="block w-full px-4 py-3 pr-10 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 focus:border-[#44B0B3] focus:ring-1 focus:ring-[#44B0B3] rounded-2xl text-gray-900 dark:text-white placeholder-gray-400 transition-all text-sm shadow-sm"
                                        placeholder="Ej. Médico especialista"
                                        :required="professionalFieldsRequired"
                                    >

                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-4">
                                        <svg class="fill-current h-4 w-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                    </div>

                                    <div
                                        x-show="cargoOpen && filteredCargos.length"
                                        x-transition
                                        x-cloak
                                        class="absolute z-50 mt-2 w-full max-h-52 overflow-y-auto rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-xl custom-scroll"
                                    >
                                        <template x-for="item in filteredCargos" :key="item">
                                            <button
                                                type="button"
                                                @click="selectCargo(item)"
                                                class="block w-full px-4 py-3 text-left text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition"
                                                x-text="item"
                                            ></button>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
{{-- SEGURIDAD --}}
<section id="securitySection" class="space-y-4">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
        <h4 class="text-sm font-black uppercase tracking-[0.15em] text-gray-400 dark:text-gray-500">
            Seguridad
        </h4>

        <label class="inline-flex items-center gap-3 cursor-pointer">
            <input
                type="checkbox"
                name="activo"
                id="staff_activo"
                value="1"
                class="rounded border-gray-300 text-[#44B0B3] focus:ring-[#44B0B3]"
                checked
            >
            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Usuario activo</span>
        </label>
    </div>

    <div id="mailActivationNotice" class="flex items-start gap-3 px-4 py-3 rounded-2xl bg-[#44B0B3]/10 border border-[#44B0B3]/30">

        <svg class="w-5 h-5 text-[#44B0B3] shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M13 16h-1v-4h-1m1-4h.01M12 2a10 10 0 100 20A10 10 0 0012 2z"/>
        </svg>
        <p class="text-sm text-[#2a8a8d] dark:text-[#44B0B3]">
            Al guardar, se enviará automáticamente un correo para que el usuario cree su propia contraseña de forma segura.
        </p>
    </div>
</section>
              
                </div>
            </div>
</form>
 {{-- REENVIAR ACCESO (solo visible al editar) --}}
<section id="passwordEditSection" class="hidden px-5 sm:px-6 py-5 border-t border-gray-200 dark:border-gray-700 mx-0">
    <h4 class="text-sm font-black uppercase tracking-[0.15em] text-gray-400 dark:text-gray-500 mb-2">
        Acceso del usuario
    </h4>

<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 px-4 py-4 rounded-2xl bg-gray-50 dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700">
        <div class="flex items-start gap-3">
            <svg class="w-5 h-5 text-gray-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
            </svg>
            <div>
                <p class="text-sm font-semibold text-gray-700 dark:text-gray-300">Restablecer contraseña</p>
                <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">
                    Se enviará un correo al usuario para que cree una nueva contraseña de forma segura.
                </p>
            </div>
        </div>

        <form id="resendAccessForm" method="POST" class="shrink-0">
            @csrf
            <button
                type="submit"
                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-[#44B0B3] text-[#44B0B3] hover:bg-[#44B0B3] hover:text-white text-sm font-semibold transition-all"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
                Reenviar acceso
            </button>
        </form>
    </div>
</section>
            {{-- Footer --}}
            <div class="shrink-0 px-5 sm:px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900">
                <div class="flex items-center justify-end gap-3">
                    <button
                        type="button"
                        onclick="closeStaffModal()"
                        class="px-5 py-3 rounded-2xl border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-800 transition"
                    >
                        Cancelar
                    </button>

                    <button
    type="submit"
    form="staffForm"
    class="px-5 py-3 rounded-2xl bg-[#44B0B3] hover:bg-[#389a9d] text-white font-bold shadow-lg shadow-[#44B0B3]/25 transition"
>
    Guardar
</button>
                </div>
            </div>
        
    </div>
</div>

<script>
    function staffFormComponent() {
        return {
            roleOpen: false,
            roleValue: 'admin',
            roleLabel: 'Administrador',

            especialidades: @js($especialidades ?? []),
            cargos: @js($cargos ?? []),

            especialidadInput: '',
            cargoInput: '',

            especialidadOpen: false,
            cargoOpen: false,

            filteredEspecialidades: [],
            filteredCargos: [],

            init() {
                window.staffFormBindings = this;
                this.filteredEspecialidades = [...this.especialidades];
                this.filteredCargos = [...this.cargos];
            },

            setRole(role) {
                this.roleValue = role;
                this.roleLabel =
                    role === 'admin' ? 'Administrador' :
                    role === 'medico' ? 'Médico' :
                    role === 'enfermera' ? 'Enfermera' :
                    'Recepcionista';
            },
// DESPUÉS — visible para médico y admin, oculto para enfermera/recepcionista
get showProfessionalFields() {
    return this.roleValue === 'medico' || this.roleValue === 'admin';
},

// Nueva propiedad — obligatorio solo para médico
get professionalFieldsRequired() {
    return this.roleValue === 'medico';
},
            normalizeText(text) {
                return (text || '')
                    .toString()
                    .trim()
                    .replace(/\s+/g, ' ');
            },

            normalizeCompare(text) {
                return this.normalizeText(text)
                    .toLowerCase()
                    .normalize('NFD')
                    .replace(/[\u0300-\u036f]/g, '');
            },

            toTitleCase(text) {
                return this.normalizeText(text)
                    .toLowerCase()
                    .replace(/\b\w/g, c => c.toUpperCase());
            },

            filterEspecialidades() {
                const q = this.normalizeCompare(this.especialidadInput);

                this.filteredEspecialidades = this.especialidades.filter(item =>
                    this.normalizeCompare(item).includes(q)
                ).slice(0, 8);

                this.especialidadOpen = true;
            },

            filterCargos() {
                const q = this.normalizeCompare(this.cargoInput);

                this.filteredCargos = this.cargos.filter(item =>
                    this.normalizeCompare(item).includes(q)
                ).slice(0, 8);

                this.cargoOpen = true;
            },

            selectEspecialidad(item) {
                this.especialidadInput = item;
                this.especialidadOpen = false;
            },

            selectCargo(item) {
                this.cargoInput = item;
                this.cargoOpen = false;
            },

            normalizeEspecialidad() {
                const current = this.normalizeText(this.especialidadInput);
                if (!current) return;

                const found = this.especialidades.find(item =>
                    this.normalizeCompare(item) === this.normalizeCompare(current)
                );

                this.especialidadInput = found ? found : this.toTitleCase(current);
            },

            normalizeCargo() {
                const current = this.normalizeText(this.cargoInput);
                if (!current) return;

                const found = this.cargos.find(item =>
                    this.normalizeCompare(item) === this.normalizeCompare(current)
                );

                this.cargoInput = found ? found : this.toTitleCase(current);
            },

            setEspecialidad(value) {
                this.especialidadInput = value || '';
            },

            setCargo(value) {
                this.cargoInput = value || '';
            }
        }
    }
    function clearFieldError(field) {
        field.classList.remove('field-invalid');

        const wrapper = field.closest('.md\\:col-span-1, .md\\:col-span-2, .md\\:col-span-3, .md\\:col-span-4, .md\\:col-span-6') || field.parentElement;
        if (!wrapper) return;

        const oldError = wrapper.querySelector('.field-error-text');
        if (oldError) oldError.remove();
    }

    function setFieldError(field, message) {
        clearFieldError(field);
        field.classList.add('field-invalid');

        const wrapper = field.closest('.md\\:col-span-1, .md\\:col-span-2, .md\\:col-span-3, .md\\:col-span-4, .md\\:col-span-6') || field.parentElement;
        if (!wrapper) return;

        const error = document.createElement('p');
        error.className = 'field-error-text';
        error.textContent = message;
        wrapper.appendChild(error);
    }

    function getVisibleValidationTarget(field) {
        if (field.id === 'staff_rol') {
            return field.closest('.relative')?.querySelector('button') || field;
        }
        return field;
    }

    function validateRequiredField(field) {
        const label = field.dataset.label || field.name || 'Este campo';
        const value = (field.value || '').trim();

        if (value === '') {
            setFieldError(field, `${label} es obligatorio.`);
            return `${label} es obligatorio.`;
        }

        if (field.type === 'email') {
            const emailOk = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value);
            if (!emailOk) {
                setFieldError(field, `Ingresa un correo válido.`);
                return `Ingresa un correo válido.`;
            }
        }

        return null;
    }



    function initStaffFormValidation() {
    const form = document.getElementById('staffForm');
    if (!form) return;

    const fields = [
        document.getElementById('staff_name'),
        document.getElementById('staff_apellido_paterno'),
        document.getElementById('staff_apellido_materno'),
        document.getElementById('staff_ci'),
        document.getElementById('staff_telefono'),
        document.getElementById('staff_email'),
        document.getElementById('staff_rol'),
        document.getElementById('staff_especialidad'),
        document.getElementById('staff_cargo'),
    ].filter(Boolean);

    fields.forEach(field => {
        field.addEventListener('input', () => clearFieldError(field));
        field.addEventListener('change', () => clearFieldError(field));
    });

    form.addEventListener('submit', async function (e) {
        e.preventDefault();

        // Validación JS primero
        let firstInvalidField = null;
        let firstMessage = null;
        fields.forEach(field => clearFieldError(field));

        for (const field of fields) {
            if (!field.required) continue;
            if (field.offsetParent === null) continue;
            const error = validateRequiredField(field);
            if (error && !firstInvalidField) {
                firstInvalidField = field;
                firstMessage = error;
            }
        }

        if (firstInvalidField) {
            const target = getVisibleValidationTarget(firstInvalidField);
            showToast({ title: 'Campo requerido', message: firstMessage, type: 'error' });
            setTimeout(() => {
                target.focus?.();
                target.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }, 50);
            return;
        }

        // Deshabilitar botón
        const submitBtn = document.querySelector('button[form="staffForm"]');
        const originalText = submitBtn.textContent;
        submitBtn.disabled = true;
        submitBtn.textContent = 'Guardando...';

        try {
            const formData = new FormData(form);

            const response = await fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
                body: formData,
            });

            const data = await response.json();

            if (response.ok) {
                closeStaffModal();
                showToast({ title: 'Éxito', message: data.message, type: 'success' });
                runStaffFilters();
            } else if (data.errors) {
                const fieldMap = {
                    'name': 'staff_name',
                    'apellido_paterno': 'staff_apellido_paterno',
                    'apellido_materno': 'staff_apellido_materno',
                    'ci': 'staff_ci',
                    'telefono': 'staff_telefono',
                    'email': 'staff_email',
                    'rol': 'staff_rol',
                    'especialidad': 'staff_especialidad',
                    'cargo': 'staff_cargo',
                };

                Object.keys(data.errors).forEach(key => {
                    const fieldId = fieldMap[key];
                    if (fieldId) {
                        const field = document.getElementById(fieldId);
                        if (field) setFieldError(field, data.errors[key][0]);
                    }
                });

                const firstKey = Object.keys(data.errors)[0];
                showToast({ title: 'Error', message: data.errors[firstKey][0], type: 'error' });
            }
        } catch (err) {
            showToast({ title: 'Error', message: 'Ocurrió un error inesperado.', type: 'error' });
        } finally {
            submitBtn.disabled = false;
            submitBtn.textContent = originalText;
        }
    });
}

    document.addEventListener('DOMContentLoaded', initStaffFormValidation);
</script>

<style>
    .custom-scroll::-webkit-scrollbar {
        width: 8px;
    }

    .custom-scroll::-webkit-scrollbar-track {
        background: transparent;
    }

    .custom-scroll::-webkit-scrollbar-thumb {
        background: rgba(148, 163, 184, 0.35);
        border-radius: 999px;
    }

    .dark .custom-scroll::-webkit-scrollbar-thumb {
        background: rgba(71, 85, 105, 0.55);
    }

    [x-cloak] {
        display: none !important;
    }

.field-invalid {
    border-color: #ef4444 !important;
    box-shadow: 0 0 0 1px #ef4444 !important;
}

.field-invalid:focus {
    border-color: #ef4444 !important;
    box-shadow: 0 0 0 2px rgba(239, 68, 68, 0.18) !important;
}

.field-error-text {
    margin-top: 0.5rem;
    font-size: 0.75rem;
    line-height: 1rem;
    color: #ef4444;
}
</style>