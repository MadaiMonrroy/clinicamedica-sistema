<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div>
                <h2 class="text-3xl font-black text-gray-800 dark:text-white tracking-tight">
                    Gestión de Personal
                </h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Administra usuarios, roles, estados y datos de contacto del personal.
                </p>
            </div>
    
            <button
                type="button"
                onclick="openStaffCreateModal()"
                class="w-full sm:w-auto px-6 py-3 bg-[#44B0B3] hover:bg-[#389a9d] text-white font-bold rounded-2xl transition-all shadow-lg shadow-[#44B0B3]/25 flex items-center justify-center gap-2 text-sm"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Nuevo Miembro
            </button>
        </div>
    </x-slot>

    <div class="py-6 px-4 sm:px-6 lg:px-8">
        <div class="mx-auto">

            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div class="md:col-span-3 relative group">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <svg class="w-5 h-5 text-gray-400 group-focus-within:text-[#44B0B3] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
            
                    <input
                        id="staff-search"
                        type="text"
                        value="{{ $search ?? '' }}"
                        class="block w-full pl-12 pr-4 py-3 bg-white dark:bg-gray-800/40 border border-gray-200 dark:border-gray-700 focus:border-[#44B0B3] focus:ring-1 focus:ring-[#44B0B3] rounded-2xl text-gray-900 dark:text-white placeholder-gray-400 transition-all text-sm shadow-sm"
                        placeholder="Buscar por nombre, CI, correo, celular, especialidad..."
                    >
                </div>
            
                <div class="relative" x-data="{ open: false, selected: '{{ $rol ?: 'Todos los roles' }}', value: '{{ $rol }}' }">
                    <input type="hidden" id="staff-role" value="{{ $rol }}">
            
                    <button
                        type="button"
                        @click="open = !open"
                        @click.outside="open = false"
                        class="inline-flex w-full items-center justify-between px-4 py-3 text-sm leading-5 font-medium rounded-2xl
                               text-gray-700 dark:text-gray-300
                               bg-white dark:bg-gray-800
                               border border-gray-200 dark:border-gray-700
                               hover:text-gray-900 dark:hover:text-white
                               focus:outline-none focus:ring-1 focus:ring-[#44B0B3] focus:border-[#44B0B3]
                               transition ease-in-out duration-150 shadow-sm"
                    >
                        <span x-text="selected === '' ? 'Todos los roles' : selected"></span>
            
                        <svg class="fill-current h-4 w-4 text-gray-400 ms-3 shrink-0 transition-transform duration-200" :class="{ 'rotate-180': open }" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>
            
                    <div
                        x-show="open"
                        x-transition
                        x-cloak
                        class="absolute z-50 mt-2 w-full rounded-2xl overflow-hidden border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-xl"
                    >
                        <button type="button" @click="selected='Todos los roles'; value=''; document.getElementById('staff-role').value=''; open=false; runStaffFilters()" class="block w-full px-4 py-3 text-left text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition">Todos los roles</button>
                        <button type="button" @click="selected='Administrador'; value='admin'; document.getElementById('staff-role').value='admin'; open=false; runStaffFilters()" class="block w-full px-4 py-3 text-left text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition">Administrador</button>
                        <button type="button" @click="selected='Médico'; value='medico'; document.getElementById('staff-role').value='medico'; open=false; runStaffFilters()" class="block w-full px-4 py-3 text-left text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition">Médico</button>
                        <button type="button" @click="selected='Enfermera'; value='enfermera'; document.getElementById('staff-role').value='enfermera'; open=false; runStaffFilters()" class="block w-full px-4 py-3 text-left text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition">Enfermera</button>
                        <button type="button" @click="selected='Recepcionista'; value='recepcionista'; document.getElementById('staff-role').value='recepcionista'; open=false; runStaffFilters()" class="block w-full px-4 py-3 text-left text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition">Recepcionista</button>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800/40 rounded-[2rem] border border-gray-100 dark:border-gray-700/50 overflow-hidden shadow-xl dark:shadow-2xl">
                <div class="overflow-x-auto" id="staff-table-wrapper">
                    @include('admin.staff.partials.table', ['staff' => $staff])
                </div>
            
                <div id="staff-pagination-wrapper" class="px-4 sm:px-6 py-4 border-t border-gray-100 dark:border-gray-700/50">
                    @include('admin.staff.partials.pagination', ['staff' => $staff])
                </div>
            </div>

        </div>
    </div>

    @include('admin.staff.partials.form-modal')
    @include('admin.staff.partials.confirm-toggle-modal')
    @include('admin.staff.partials.scripts')
</x-app-layout>