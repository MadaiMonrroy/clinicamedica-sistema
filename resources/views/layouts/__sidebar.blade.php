@php
    use Illuminate\Support\Facades\Auth;
    $user = Auth::user();
    $role = strtolower($user->rol ?? '');

    $isAdmin  = $role === 'admin';
    $isRecep  = in_array($role, ['admin', 'recepcionista']);
    $isNurse  = in_array($role, ['admin', 'enfermera']);
    $isDoctor = in_array($role, ['admin', 'medico', 'medico especialista', 'médico', 'médico especialista']);
    $isLab    = in_array($role, ['admin', 'laboratorio']);

    // Helpers PHP — simples y sin bugs
    function navItem(bool $active): string {
        return $active
            ? 'group flex items-center p-2.5 text-gray-900 rounded-xl dark:text-gray-100 bg-gray-100 dark:bg-gray-700/80 border border-gray-200 dark:border-gray-700 shadow-sm transition-all'
            : 'group flex items-center p-2.5 text-gray-900 rounded-xl dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700/70 transition-all';
    }
    function navSub(bool $active): string {
        return $active
            ? 'group flex items-center w-full rounded-lg px-3 py-2 text-sm font-medium text-gray-900 dark:text-gray-100 bg-gray-100 dark:bg-gray-700/70 border border-gray-200 dark:border-gray-700 transition-all'
            : 'group flex items-center w-full rounded-lg px-3 py-2 text-sm text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/50 hover:text-gray-900 dark:hover:text-white transition-all';
    }
    function navIcon(bool $active): string {
        return $active
            ? 'w-5 h-5 text-[#52ABB1]'
            : 'w-5 h-5 text-gray-500 dark:text-gray-400 group-hover:text-[#52ABB1] transition duration-200';
    }
    function navSection(): string {
        return 'px-3 mb-2 mt-5 text-[11px] font-semibold text-gray-400 uppercase tracking-[0.16em]';
    }
    function isActive(string|array $patterns): bool {
        foreach ((array) $patterns as $p) {
            if (request()->routeIs($p)) return true;
        }
        return false;
    }
@endphp

<aside
    id="logo-sidebar"
    class="fixed top-0 left-0 z-40 w-64 h-screen pt-16
           transition-transform -translate-x-full sm:translate-x-0
           bg-white border-r border-gray-200
           dark:bg-gray-800 dark:border-gray-700"
    aria-label="Sidebar"
>
    <div class="flex h-full flex-col">

        {{-- Badge de rol --}}
        <div class="border-b border-gray-200 dark:border-gray-700 px-4 py-3">
            <div class="rounded-2xl border border-gray-200 dark:border-gray-700
                        bg-gray-50 dark:bg-gray-700/40 px-3 py-2.5">
                <div class="flex items-center justify-between gap-2">
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Acceso actual</p>
                        <p class="text-sm font-semibold text-gray-800 dark:text-gray-100">
                            {{ ucfirst($user->rol ?? 'Usuario') }}
                        </p>
                    </div>
                    <span class="inline-flex h-2.5 w-2.5 rounded-full bg-[#52ABB1]"></span>
                </div>
            </div>
        </div>

        {{-- Menú scrollable --}}
        <div class="flex-1 overflow-y-auto px-3 py-4 custom-scroll">

            <div class="{{ navSection() }}">General</div>
            <ul class="space-y-1.5">
                <li>
                    <a href="{{ route('dashboard') }}" class="{{ navItem(isActive('dashboard')) }}">
                        <svg class="{{ navIcon(isActive('dashboard')) }}" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10 3a1 1 0 0 1 .707.293l5 5A1 1 0 0 1 16 9v6a2 2 0 0 1-2 2h-3a1 1 0 0 1-1-1v-3H9v3a1 1 0 0 1-1 1H5a2 2 0 0 1-2-2V9a1 1 0 0 1 .293-.707l5-5A1 1 0 0 1 10 3Z"/>
                        </svg>
                        <div class="ms-3 min-w-0">
                            <p class="text-sm font-semibold">Panel principal</p>
                            <p class="text-[11px] text-gray-400 dark:text-gray-500">Resumen operativo</p>
                        </div>
                    </a>
                </li>
            </ul>

            @if($isRecep || $isAdmin)
                <div class="{{ navSection() }}">Recepción</div>

                <details class="group mb-2 rounded-2xl border border-gray-200 dark:border-gray-700
                                bg-white dark:bg-gray-800 overflow-hidden"
                         {{ isActive('recepcion.pacientes.*') ? 'open' : '' }}>
                    <summary class="flex cursor-pointer list-none items-center justify-between
                                    gap-3 px-3 py-2.5 hover:bg-gray-50 dark:hover:bg-gray-700/40 transition">
                        <div class="flex items-center gap-3 min-w-0">
                            <svg class="{{ navIcon(isActive('recepcion.pacientes.*')) }}"
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M15 19a6 6 0 10-12 0m12 0h6m-3-3v6M12 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                            <div class="min-w-0">
                                <p class="text-sm font-semibold text-gray-800 dark:text-gray-100">Pacientes</p>
                                <p class="text-[11px] text-gray-400 dark:text-gray-500">Registro y búsqueda</p>
                            </div>
                        </div>
                        <svg class="w-4 h-4 text-gray-400 transition-transform duration-200 group-open:rotate-180"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </summary>
                    <div class="px-2 pb-2 space-y-1">
                        <a href="{{ route('recepcion.pacientes.index') }}"
                           class="{{ navSub(isActive('recepcion.pacientes.index')) }}">
                            <span class="mr-2 h-1.5 w-1.5 rounded-full bg-[#52ABB1]"></span>
                            Listado de pacientes
                        </a>
                        <a href="{{ route('recepcion.pacientes.create') }}"
                           class="{{ navSub(isActive('recepcion.pacientes.create')) }}">
                            <span class="mr-2 h-1.5 w-1.5 rounded-full bg-[#52ABB1]"></span>
                            Nuevo paciente
                        </a>
                    </div>
                </details>

                <details class="group mb-2 rounded-2xl border border-gray-200 dark:border-gray-700
                                bg-white dark:bg-gray-800 overflow-hidden"
                         {{ isActive('recepcion.ingresos.*') ? 'open' : '' }}>
                    <summary class="flex cursor-pointer list-none items-center justify-between
                                    gap-3 px-3 py-2.5 hover:bg-gray-50 dark:hover:bg-gray-700/40 transition">
                        <div class="flex items-center gap-3 min-w-0">
                            <svg class="{{ navIcon(isActive('recepcion.ingresos.*')) }}"
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M8 6h13M8 12h13M8 18h13M3 6h.01M3 12h.01M3 18h.01"/>
                            </svg>
                            <div class="min-w-0">
                                <p class="text-sm font-semibold text-gray-800 dark:text-gray-100">Ingresos</p>
                                <p class="text-[11px] text-gray-400 dark:text-gray-500">Admisión y prioridad</p>
                            </div>
                        </div>
                        <svg class="w-4 h-4 text-gray-400 transition-transform duration-200 group-open:rotate-180"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </summary>
                    <div class="px-2 pb-2 space-y-1">
                        <a href="{{ route('recepcion.ingresos.index') }}"
                           class="{{ navSub(isActive('recepcion.ingresos.index')) }}">
                            <span class="mr-2 h-1.5 w-1.5 rounded-full bg-[#52ABB1]"></span>
                            Historial de ingresos
                        </a>
                        <a href="{{ route('recepcion.ingresos.create') }}"
                           class="{{ navSub(isActive('recepcion.ingresos.create')) }}">
                            <span class="mr-2 h-1.5 w-1.5 rounded-full bg-[#52ABB1]"></span>
                            Nuevo ingreso
                        </a>
                    </div>
                </details>
            @endif

            @if($isNurse || $isAdmin)
                <div class="{{ navSection() }}">Enfermería</div>
                <details class="group mb-2 rounded-2xl border border-gray-200 dark:border-gray-700
                                bg-white dark:bg-gray-800 overflow-hidden"
                         {{ isActive('enfermeria.*') ? 'open' : '' }}>
                    <summary class="flex cursor-pointer list-none items-center justify-between
                                    gap-3 px-3 py-2.5 hover:bg-gray-50 dark:hover:bg-gray-700/40 transition">
                        <div class="flex items-center gap-3 min-w-0">
                            <svg class="{{ navIcon(isActive('enfermeria.*')) }}"
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M9 12h6m-3-3v6m8-3A9 9 0 113 12a9 9 0 0118 0z"/>
                            </svg>
                            <div class="min-w-0">
                                <p class="text-sm font-semibold text-gray-800 dark:text-gray-100">Gestión de enfermería</p>
                                <p class="text-[11px] text-gray-400 dark:text-gray-500">Clasificación y destino</p>
                            </div>
                        </div>
                        <svg class="w-4 h-4 text-gray-400 transition-transform duration-200 group-open:rotate-180"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </summary>
                    <div class="px-2 pb-2 space-y-1">
                        <a href="{{ route('enfermeria.index') }}"
                           class="{{ navSub(isActive('enfermeria.index')) }}">
                            <span class="mr-2 h-1.5 w-1.5 rounded-full bg-[#52ABB1]"></span>
                            Cola de enfermería
                        </a>
                        <a href="{{ route('enfermeria.pending') }}"
                           class="{{ navSub(isActive('enfermeria.pending')) }}">
                            <span class="mr-2 h-1.5 w-1.5 rounded-full bg-[#52ABB1]"></span>
                            Pendientes
                        </a>
                    </div>
                </details>
            @endif

            @if($isDoctor || $isAdmin)
                <div class="{{ navSection() }}">Atención médica</div>

                <details class="group mb-2 rounded-2xl border border-gray-200 dark:border-gray-700
                                bg-white dark:bg-gray-800 overflow-hidden"
                         {{ isActive('tickets.*') ? 'open' : '' }}>
                    <summary class="flex cursor-pointer list-none items-center justify-between
                                    gap-3 px-3 py-2.5 hover:bg-gray-50 dark:hover:bg-gray-700/40 transition">
                        <div class="flex items-center gap-3 min-w-0">
                            <svg class="{{ navIcon(isActive('tickets.*')) }}"
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M8 7h8M8 12h8M8 17h5M5 4h14a2 2 0 012 2v12a2 2 0 01-2 2H5a2 2 0 01-2-2V6a2 2 0 012-2z"/>
                            </svg>
                            <div class="min-w-0">
                                <p class="text-sm font-semibold text-gray-800 dark:text-gray-100">Turnos</p>
                                <p class="text-[11px] text-gray-400 dark:text-gray-500">Colas por área</p>
                            </div>
                        </div>
                        <svg class="w-4 h-4 text-gray-400 transition-transform duration-200 group-open:rotate-180"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </summary>
                    <div class="px-2 pb-2 space-y-1">
                        <a href="{{ route('tickets.index') }}"
                           class="{{ navSub(isActive('tickets.index')) }}">
                            <span class="mr-2 h-1.5 w-1.5 rounded-full bg-[#52ABB1]"></span>
                            Todos los tickets
                        </a>
                    </div>
                </details>

                <details class="group mb-2 rounded-2xl border border-gray-200 dark:border-gray-700
                                bg-white dark:bg-gray-800 overflow-hidden"
                         {{ isActive(['atenciones.*','recetas.*','ordenes-medicas.*','interconsultas.*']) ? 'open' : '' }}>
                    <summary class="flex cursor-pointer list-none items-center justify-between
                                    gap-3 px-3 py-2.5 hover:bg-gray-50 dark:hover:bg-gray-700/40 transition">
                        <div class="flex items-center gap-3 min-w-0">
                            <svg class="{{ navIcon(isActive(['atenciones.*','recetas.*','ordenes-medicas.*','interconsultas.*'])) }}"
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25z"/>
                            </svg>
                            <div class="min-w-0">
                                <p class="text-sm font-semibold text-gray-800 dark:text-gray-100">Atención clínica</p>
                                <p class="text-[11px] text-gray-400 dark:text-gray-500">Consulta y seguimiento</p>
                            </div>
                        </div>
                        <svg class="w-4 h-4 text-gray-400 transition-transform duration-200 group-open:rotate-180"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </summary>
                    <div class="px-2 pb-2 space-y-1">
                        <a href="{{ route('atenciones.index') }}"
                           class="{{ navSub(isActive('atenciones.*')) }}">
                            <span class="mr-2 h-1.5 w-1.5 rounded-full bg-[#52ABB1]"></span>Atenciones
                        </a>
                        <a href="{{ route('recetas.index') }}"
                           class="{{ navSub(isActive('recetas.*')) }}">
                            <span class="mr-2 h-1.5 w-1.5 rounded-full bg-[#52ABB1]"></span>Recetas médicas
                        </a>
                        <a href="{{ route('ordenes-medicas.index') }}"
                           class="{{ navSub(isActive('ordenes-medicas.*')) }}">
                            <span class="mr-2 h-1.5 w-1.5 rounded-full bg-[#52ABB1]"></span>Órdenes médicas
                        </a>
                        <a href="{{ route('interconsultas.index') }}"
                           class="{{ navSub(isActive('interconsultas.*')) }}">
                            <span class="mr-2 h-1.5 w-1.5 rounded-full bg-[#52ABB1]"></span>Interconsultas
                        </a>
                    </div>
                </details>
            @endif

            @if($isLab || $isAdmin)
                <div class="{{ navSection() }}">Laboratorio</div>
                <details class="group mb-2 rounded-2xl border border-gray-200 dark:border-gray-700
                                bg-white dark:bg-gray-800 overflow-hidden"
                         {{ isActive('laboratorio.*') ? 'open' : '' }}>
                    <summary class="flex cursor-pointer list-none items-center justify-between
                                    gap-3 px-3 py-2.5 hover:bg-gray-50 dark:hover:bg-gray-700/40 transition">
                        <div class="flex items-center gap-3 min-w-0">
                            <svg class="{{ navIcon(isActive('laboratorio.*')) }}"
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M9 3h6m-1 0v6.586l4.95 7.425A2 2 0 0117.286 20H6.714a2 2 0 01-1.664-2.989L10 9.586V3"/>
                            </svg>
                            <div class="min-w-0">
                                <p class="text-sm font-semibold text-gray-800 dark:text-gray-100">Resultados</p>
                                <p class="text-[11px] text-gray-400 dark:text-gray-500">Adjuntos PDF</p>
                            </div>
                        </div>
                        <svg class="w-4 h-4 text-gray-400 transition-transform duration-200 group-open:rotate-180"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </summary>
                    <div class="px-2 pb-2 space-y-1">
                        <a href="{{ route('laboratorio.index') }}"
                           class="{{ navSub(isActive('laboratorio.index')) }}">
                            <span class="mr-2 h-1.5 w-1.5 rounded-full bg-[#52ABB1]"></span>Panel de laboratorio
                        </a>
                        
                    </div>
                </details>
            @endif

            @if($isAdmin)
    <div class="{{ navSection() }}">Administración</div>

    <details class="group mb-2 rounded-2xl border border-gray-200 dark:border-gray-700
                    bg-white dark:bg-gray-800 overflow-hidden"
             {{ isActive(['admin.staff.*','admin.areas.*']) ? 'open' : '' }}>
        <summary class="flex cursor-pointer list-none items-center justify-between
                        gap-3 px-3 py-2.5 hover:bg-gray-50 dark:hover:bg-gray-700/40 transition">
            <div class="flex items-center gap-3 min-w-0">
                <svg class="{{ navIcon(isActive(['admin.staff.*','admin.areas.*'])) }}"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M10.343 3.94c.09-.542.56-.94 1.11-.94h1.093c.55 0 1.02.398 1.11.94l.149.894c.07.424.384.764.78.93.398.164.855.142 1.205-.108l.737-.527a1.125 1.125 0 011.45.12l.773.774c.39.389.44 1.002.12 1.45l-.527.737c-.25.35-.272.806-.107 1.204.165.397.505.71.93.78l.893.15c.543.09.94.56.94 1.109v1.094c0 .55-.397 1.02-.94 1.11l-.893.149c-.425.07-.765.383-.93.78-.165.398-.143.854.107 1.204l.527.738c.32.447.269 1.06-.12 1.45l-.774.773a1.125 1.125 0 01-1.449.12l-.738-.527c-.35-.25-.806-.272-1.203-.107-.397.165-.71.505-.781.929l-.149.894c-.09.542-.56.94-1.11.94h-1.094c-.55 0-1.019-.398-1.11-.94l-.148-.894c-.071-.424-.384-.764-.781-.93-.398-.164-.854-.142-1.204.108l-.738.527c-.447.32-1.06.269-1.45-.12l-.773-.774a1.125 1.125 0 01-.12-1.45l.527-.737c.25-.35.273-.806.108-1.204-.165-.397-.506-.71-.93-.78l-.894-.15c-.542-.09-.94-.56-.94-1.109v-1.094c0-.55.398-1.02.94-1.11l.894-.149c.424-.07.765-.383.93-.78.165-.398.143-.854-.108-1.204l-.526-.738a1.125 1.125 0 01.12-1.45l.773-.773a1.125 1.125 0 011.45-.12l.737.527c.35.25.807.272 1.204.107.397-.165.71-.505.78-.929l.15-.894z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                <div class="min-w-0">
                    <p class="text-sm font-semibold text-gray-800 dark:text-gray-100">Gestión interna</p>
                    <p class="text-[11px] text-gray-400 dark:text-gray-500">Configuración base</p>
                </div>
            </div>
            <svg class="w-4 h-4 text-gray-400 transition-transform duration-200 group-open:rotate-180"
                 fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
            </svg>
        </summary>
        <div class="px-2 pb-2 space-y-1">
            <a href="{{ route('admin.staff.index') }}"
               class="{{ navSub(isActive('admin.staff.*')) }}">
                <span class="mr-2 h-1.5 w-1.5 rounded-full bg-[#52ABB1]"></span>Personal
            </a>
            <a href="{{ route('admin.areas.index') }}"
               class="{{ navSub(isActive('admin.areas.*')) }}">
                <span class="mr-2 h-1.5 w-1.5 rounded-full bg-[#52ABB1]"></span>Áreas
            </a>

        </div>
    </details>
@endif

        </div>

        {{-- Footer --}}
        <div class="border-t border-gray-200 dark:border-gray-700 px-4 py-3">
            <div class="rounded-2xl bg-gray-50 dark:bg-gray-700/40
                        border border-gray-200 dark:border-gray-700 px-3 py-3">
                <p class="text-xs text-gray-500 dark:text-gray-400">Sesión iniciada como</p>
                <p class="truncate text-sm font-semibold text-gray-800 dark:text-gray-100">
                    {{ $user->name ?? 'Usuario' }}
                </p>
            </div>
        </div>

    </div>
</aside>
