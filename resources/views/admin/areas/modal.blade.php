{{--
    Partial: resources/views/admin/areas/modal.blade.php
    Incluir en index.blade.php con: @include('admin.areas.modal')
    Funciona para crear Y editar — JS cambia el modo.
--}}

{{-- Backdrop --}}
<div id="areaModalBackdrop"
     class="fixed inset-0 bg-black/50 z-40 hidden"
     onclick="closeAreaModal()">
</div>

{{-- Modal --}}
<div id="areaModal"
     class="fixed inset-0 z-50 hidden items-center justify-center p-4">
    <div class="w-full max-w-lg rounded-[2rem] bg-white dark:bg-gray-900
                border border-gray-200 dark:border-gray-700
                shadow-2xl overflow-hidden flex flex-col">

        {{-- Header --}}
        <div class="shrink-0 flex items-start justify-between
                    px-6 py-5
                    border-b border-gray-200 dark:border-gray-700">
            <div>
                <h3 id="areaModalTitle"
                    class="text-lg font-bold text-gray-800 dark:text-white">
                    Nueva área
                </h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">
                    Completa la información del área clínica
                </p>
            </div>
            <button type="button"
                    onclick="closeAreaModal()"
                    class="p-2 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-800 transition">
                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Form --}}
        <form id="areaForm"
              method="POST"
              action="{{ route('admin.areas.store') }}"
              class="flex flex-col">
            @csrf
            <input type="hidden" name="_method" id="areaFormMethod" value="POST">

            {{-- Body --}}
            <div class="px-6 py-5 space-y-6">

                {{-- Sección: Información --}}
                <div class="space-y-4">
                    <h4 class="text-xs font-black uppercase tracking-[0.15em] text-gray-400 dark:text-gray-500">
                        Información del área
                    </h4>

                    {{-- Nombre --}}
                    <div>
                        <label for="area_nombre"
                               class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                            Nombre <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="text"
                            id="area_nombre"
                            name="nombre"
                            placeholder="Ej: Traumatología"
                            required
                            maxlength="100"
                            class="block w-full px-4 py-3 rounded-2xl text-sm
                                   bg-white dark:bg-gray-800/40
                                   border border-gray-200 dark:border-gray-700
                                   text-gray-900 dark:text-white
                                   placeholder-gray-400 dark:placeholder-gray-500
                                   focus:border-[#52ABB1] focus:ring-1 focus:ring-[#52ABB1]
                                   outline-none transition-all shadow-sm"
                        />
                        <p id="area_nombre_error" class="mt-1.5 text-xs text-red-500 hidden"></p>
                    </div>

                    {{-- Código + Tipo en dos columnas --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="area_codigo"
                                   class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                Código <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="text"
                                id="area_codigo"
                                name="codigo"
                                placeholder="Ej: TRAUMA"
                                required
                                maxlength="20"
                                style="text-transform:uppercase; font-family:monospace; letter-spacing:.05em;"
                                class="block w-full px-4 py-3 rounded-2xl text-sm
                                       bg-white dark:bg-gray-800/40
                                       border border-gray-200 dark:border-gray-700
                                       text-gray-900 dark:text-white
                                       placeholder-gray-400 dark:placeholder-gray-500
                                       focus:border-[#52ABB1] focus:ring-1 focus:ring-[#52ABB1]
                                       outline-none transition-all shadow-sm"
                            />
                            <p class="mt-1.5 text-xs text-gray-400 dark:text-gray-600">
                                Único · se guarda en mayúsculas
                            </p>
                            <p id="area_codigo_error" class="mt-1 text-xs text-red-500 hidden"></p>
                        </div>

                        <div>
                            <label for="area_tipo"
                                   class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                Tipo <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="text"
                                id="area_tipo"
                                name="tipo"
                                list="area_tipos_sugeridos"
                                placeholder="Ej: consulta"
                                required
                                maxlength="50"
                                class="block w-full px-4 py-3 rounded-2xl text-sm
                                       bg-white dark:bg-gray-800/40
                                       border border-gray-200 dark:border-gray-700
                                       text-gray-900 dark:text-white
                                       placeholder-gray-400 dark:placeholder-gray-500
                                       focus:border-[#52ABB1] focus:ring-1 focus:ring-[#52ABB1]
                                       outline-none transition-all shadow-sm"
                            />
                            <datalist id="area_tipos_sugeridos">
                                @foreach(\App\Models\Area::TIPOS_SUGERIDOS as $t)
                                    <option value="{{ $t }}">
                                @endforeach
                            </datalist>
                            <p class="mt-1.5 text-xs text-gray-400 dark:text-gray-600">
                                Puedes crear tipos nuevos
                            </p>
                            <p id="area_tipo_error" class="mt-1 text-xs text-red-500 hidden"></p>
                        </div>
                    </div>
                </div>

                {{-- Sección: Estado — solo visible en modo edición --}}
                <div id="area_estado_section" class="hidden space-y-3">
                    <h4 class="text-xs font-black uppercase tracking-[0.15em] text-gray-400 dark:text-gray-500">
                        Estado
                    </h4>
                    <div class="flex gap-6">
                        <label class="inline-flex items-center gap-2.5 cursor-pointer group">
                            <input type="radio" name="estado" value="activo"
                                   id="area_estado_activo" checked
                                   class="w-4 h-4 cursor-pointer"
                                   style="accent-color:#52ABB1;"/>
                            <span class="text-sm text-gray-700 dark:text-gray-300
                                         group-hover:text-gray-900 dark:group-hover:text-white transition">
                                Activo
                            </span>
                        </label>
                        <label class="inline-flex items-center gap-2.5 cursor-pointer group">
                            <input type="radio" name="estado" value="inactivo"
                                   id="area_estado_inactivo"
                                   class="w-4 h-4 cursor-pointer"
                                   style="accent-color:#52ABB1;"/>
                            <span class="text-sm text-gray-700 dark:text-gray-300
                                         group-hover:text-gray-900 dark:group-hover:text-white transition">
                                Inactivo
                            </span>
                        </label>
                    </div>

                    {{-- Aviso dependencias — se muestra via JS si el área tiene registros --}}
                    <div id="area_dependencias_warn"
                         class="hidden flex items-start gap-2 text-xs text-amber-700 dark:text-amber-400
                                bg-amber-50 dark:bg-amber-900/20
                                border border-amber-200 dark:border-amber-800
                                rounded-xl px-3 py-2.5">
                        <svg class="w-4 h-4 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126z"/>
                        </svg>
                        <span>
                            Esta área tiene registros asociados. Si la desactivas no estará disponible
                            para nuevos ingresos, pero los datos históricos se conservan.
                        </span>
                    </div>
                </div>

            </div>{{-- /body --}}

            {{-- Footer --}}
            <div class="shrink-0 px-6 py-4
                        border-t border-gray-200 dark:border-gray-700
                        bg-white dark:bg-gray-900
                        flex items-center justify-end gap-3">
                <button type="button"
                        onclick="closeAreaModal()"
                        class="px-5 py-2.5 rounded-2xl text-sm font-medium
                               border border-gray-200 dark:border-gray-700
                               text-gray-700 dark:text-gray-200
                               hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                    Cancelar
                </button>
                <button type="submit"
                        id="areaSubmitBtn"
                        class="px-5 py-2.5 rounded-2xl text-sm font-bold text-white
                               transition hover:opacity-90 active:scale-[.98]"
                        style="background:#52ABB1;">
                    Guardar
                </button>
            </div>
        </form>

    </div>
</div>

{{-- ── JAVASCRIPT ── --}}
<script>
    // ── Abrir modal en modo CREAR ─────────────────────────────
    function openAreaModalCreate() {
        // Reset form
        document.getElementById('areaForm').reset();
        document.getElementById('areaFormMethod').value   = 'POST';
        document.getElementById('areaForm').action        = '{{ route('admin.areas.store') }}';
        document.getElementById('areaModalTitle').textContent = 'Nueva área';
        document.getElementById('areaSubmitBtn').textContent  = 'Crear área';

        // Ocultar sección estado (no aplica al crear)
        document.getElementById('area_estado_section').classList.add('hidden');
        document.getElementById('area_dependencias_warn').classList.add('hidden');

        clearAreaErrors();
        showAreaModal();
    }

    // ── Abrir modal en modo EDITAR ────────────────────────────
    function openAreaModalEdit(area) {
        // area = objeto con {id, nombre, codigo, tipo, estado, tiene_dependencias}
        document.getElementById('areaFormMethod').value = 'PUT';
        document.getElementById('areaForm').action      = `/admin/areas/${area.id}`;
        document.getElementById('areaModalTitle').textContent = `Editar área — ${area.nombre}`;
        document.getElementById('areaSubmitBtn').textContent  = 'Guardar cambios';

        // Rellenar campos
        document.getElementById('area_nombre').value = area.nombre;
        document.getElementById('area_codigo').value = area.codigo;
        document.getElementById('area_tipo').value   = area.tipo;

        // Estado
        document.getElementById('area_estado_section').classList.remove('hidden');
        document.getElementById('area_estado_activo').checked   = area.estado === 'activo';
        document.getElementById('area_estado_inactivo').checked = area.estado === 'inactivo';

        // Aviso dependencias
        if (area.tiene_dependencias) {
            document.getElementById('area_dependencias_warn').classList.remove('hidden');
        } else {
            document.getElementById('area_dependencias_warn').classList.add('hidden');
        }

        clearAreaErrors();
        showAreaModal();
    }

    // ── Mostrar / ocultar ─────────────────────────────────────
    function showAreaModal() {
        document.getElementById('areaModalBackdrop').classList.remove('hidden');
        const modal = document.getElementById('areaModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        // Focus al primer campo
        setTimeout(() => document.getElementById('area_nombre').focus(), 80);
    }

    function closeAreaModal() {
        document.getElementById('areaModalBackdrop').classList.add('hidden');
        const modal = document.getElementById('areaModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    // Cerrar con Escape
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') closeAreaModal();
    });

    // ── Validación básica cliente ─────────────────────────────
    function clearAreaErrors() {
        ['nombre','codigo','tipo'].forEach(f => {
            const el = document.getElementById(`area_${f}_error`);
            if (el) { el.textContent = ''; el.classList.add('hidden'); }
            const input = document.getElementById(`area_${f}`);
            if (input) input.classList.remove('!border-red-400');
        });
    }

    function showAreaError(field, msg) {
        const el = document.getElementById(`area_${field}_error`);
        if (el) { el.textContent = msg; el.classList.remove('hidden'); }
        const input = document.getElementById(`area_${field}`);
        if (input) input.classList.add('!border-red-400');
    }

    document.getElementById('areaForm').addEventListener('submit', function(e) {
        clearAreaErrors();
        let ok = true;

        const nombre = document.getElementById('area_nombre').value.trim();
        const codigo = document.getElementById('area_codigo').value.trim();
        const tipo   = document.getElementById('area_tipo').value.trim();

        if (!nombre) { showAreaError('nombre', 'El nombre es obligatorio.'); ok = false; }
        if (!codigo) { showAreaError('codigo', 'El código es obligatorio.'); ok = false; }
        if (!tipo)   { showAreaError('tipo',   'El tipo es obligatorio.');   ok = false; }

        if (!ok) e.preventDefault();
    });

    // Limpiar error al escribir
    ['nombre','codigo','tipo'].forEach(f => {
        const input = document.getElementById(`area_${f}`);
        if (input) input.addEventListener('input', () => {
            const el = document.getElementById(`area_${f}_error`);
            if (el) { el.textContent = ''; el.classList.add('hidden'); }
            input.classList.remove('!border-red-400');
        });
    });
</script>