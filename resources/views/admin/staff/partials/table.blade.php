<table class="min-w-[1100px] w-full text-left border-collapse">
    <thead>
        <tr class="text-[11px] font-black uppercase tracking-[0.15em] text-gray-400 dark:text-gray-500 border-b border-gray-100 dark:border-gray-700/50 bg-gray-50/50 dark:bg-transparent">
            <th class="px-8 py-5">Personal Clínico</th>
            <th class="px-6 py-5 text-center">Contacto</th>
            <th class="px-6 py-5 text-center">Especialidad / Cargo</th>
            <th class="px-6 py-5 text-center">Rol de Acceso</th>
            <th class="px-6 py-5 text-center">Estado</th>
            <th class="px-8 py-5 text-right">Acciones</th>
        </tr>
    </thead>

    <tbody class="divide-y divide-gray-100 dark:divide-gray-700/30">
        @forelse($staff as $user)
            <tr class="hover:bg-gray-50 dark:hover:bg-[#44B0B3]/5 transition-all group">
                <td class="px-8 py-6">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-2xl bg-[#44B0B3] flex items-center justify-center text-white font-black shadow-lg shadow-[#44B0B3]/20">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                        <div>
                            <div class="font-bold text-gray-800 dark:text-white text-base leading-tight">
                                {{ $user->name }} {{ $user->apellido_paterno }}
                            </div>
                            <div class="text-[11px] text-[#44B0B3] font-bold mt-1 uppercase">
                                CI: {{ $user->ci }}
                            </div>
                        </div>
                    </div>
                </td>

                <td class="px-6 py-6">
                    <div class="text-sm font-semibold text-gray-700 dark:text-gray-200">
                        {{ $user->email ?: 'Sin correo' }}
                    </div>

                    <div class="mt-1 flex items-center gap-2">
                        <span class="text-xs text-gray-500 dark:text-gray-400">
                            {{ $user->telefono ?: 'Sin celular' }}
                        </span>

                        @if($user->telefono)
                            <a
                                href="https://wa.me/{{ preg_replace('/\D+/', '', $user->telefono) }}"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-green-500/10 text-green-600 hover:bg-green-500/20 transition"
                            >
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M20.52 3.48A11.82 11.82 0 0 0 12.06 0C5.53 0 .2 5.31.2 11.84c0 2.08.54 4.1 1.57 5.88L0 24l6.45-1.69a11.8 11.8 0 0 0 5.61 1.43h.01c6.53 0 11.86-5.31 11.86-11.85 0-3.16-1.23-6.13-3.41-8.41ZM12.07 21.7h-.01a9.86 9.86 0 0 1-5.03-1.38l-.36-.22-3.83 1 1.02-3.73-.24-.38a9.83 9.83 0 0 1-1.52-5.22c0-5.45 4.43-9.88 9.88-9.88 2.64 0 5.12 1.03 6.99 2.89a9.81 9.81 0 0 1 2.89 6.99c0 5.45-4.43 9.88-9.88 9.88Zm5.42-7.41c-.3-.15-1.77-.87-2.05-.97-.27-.1-.47-.15-.67.15-.2.3-.77.97-.95 1.17-.17.2-.35.22-.65.07-.3-.15-1.25-.46-2.38-1.46-.88-.78-1.47-1.74-1.64-2.04-.17-.3-.02-.46.13-.61.13-.13.3-.35.45-.52.15-.17.2-.3.3-.5.1-.2.05-.37-.02-.52-.07-.15-.67-1.62-.92-2.22-.24-.58-.49-.5-.67-.51h-.57c-.2 0-.52.08-.79.37-.27.3-1.05 1.03-1.05 2.5 0 1.47 1.08 2.88 1.23 3.08.15.2 2.12 3.24 5.13 4.54.72.31 1.28.5 1.72.64.72.23 1.37.2 1.89.12.58-.09 1.77-.72 2.02-1.42.25-.7.25-1.3.17-1.42-.07-.12-.27-.2-.57-.35Z"/>
                                </svg>
                            </a>
                        @endif
                    </div>
                </td>

                <td class="px-6 py-6 text-center">
                    <div class="text-gray-700 dark:text-gray-100 font-bold text-sm">
                        {{ $user->especialidad ?: 'General' }}
                    </div>
                    <div class="text-[10px] text-gray-400 dark:text-gray-500 uppercase font-black tracking-widest mt-1">
                        {{ $user->cargo ?: 'Staff' }}
                    </div>
                </td>

                <td class="px-6 py-6 text-center">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200">
                        {{ $user->rol }}
                    </span>
                </td>

                <td class="px-6 py-6 text-center">
                    <button
                        type="button"
                        onclick="openToggleStatusModal({{ $user->id }}, '{{ $user->name }} {{ $user->apellido_paterno }}', {{ $user->activo ? 'true' : 'false' }})"
                        class="relative inline-flex h-7 w-12 items-center rounded-full transition {{ $user->activo ? 'bg-[#44B0B3]' : 'bg-gray-300 dark:bg-gray-600' }}"
                    >
                        <span class="inline-block h-5 w-5 transform rounded-full bg-white transition {{ $user->activo ? 'translate-x-6' : 'translate-x-1' }}"></span>
                    </button>
                </td>

                <td class="px-8 py-6 text-right">
                    @php
                        $userData = [
                            'id' => $user->id,
                            'name' => $user->name,
                            'apellido_paterno' => $user->apellido_paterno,
                            'apellido_materno' => $user->apellido_materno,
                            'ci' => $user->ci,
                            'telefono' => $user->telefono,
                            'rol' => $user->rol,
                            'especialidad' => $user->especialidad,
                            'cargo' => $user->cargo,
                            'activo' => (bool) $user->activo,
                            'email' => $user->email,
                        ];
                    @endphp

                    <div class="flex justify-end gap-2">
                        <button
                            type="button"
                            onclick='openStaffEditModal(@json($userData))'
                            class="p-2 text-gray-400 hover:text-[#44B0B3] hover:bg-[#44B0B3]/10 rounded-xl transition-all"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M14 3l7 7M8 16l-1 4 4-1 9-9-3-3-9 9z"/>
                            </svg>
                        </button>
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="6" class="px-8 py-10 text-center text-sm text-gray-500 dark:text-gray-400">
                    No se encontraron resultados.
                </td>
            </tr>
        @endforelse
    </tbody>
</table>