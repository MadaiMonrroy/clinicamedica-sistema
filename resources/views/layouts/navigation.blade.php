<nav
    x-data="{
        open: false,
        darkMode: document.documentElement.classList.contains('dark'),
        toggleTheme() {
            this.darkMode = !this.darkMode;
            document.documentElement.classList.toggle('dark', this.darkMode);
            localStorage.setItem('theme', this.darkMode ? 'dark' : 'light');
            window.dispatchEvent(
                new CustomEvent('theme-changed', { detail: { dark: this.darkMode } })
            );
        }
    }"
    class="fixed top-0 left-0 right-0 z-50 h-16
           bg-white dark:bg-gray-800
           border-b border-gray-200 dark:border-gray-700
           flex items-center px-4 gap-3"
>
    {{-- Logo --}}
    <a href="{{ route('dashboard') }}"
       class="flex items-center gap-2.5 shrink-0">
        <div class="w-9 h-9 rounded-xl overflow-hidden bg-gray-100 dark:bg-gray-700 flex-shrink-0">
            <img src="{{ asset('images/logos/logo_color.png') }}"
                 class="w-full h-full object-cover" alt="Logo">
        </div>
        <img src="{{ asset('images/logos/medicalia_color.png') }}"
             class="h-5 object-contain hidden sm:block" alt="Medicalia">
    </a>

    {{-- Spacer --}}
    <div class="flex-1"></div>

    {{-- Lado derecho --}}
    <div class="flex items-center gap-2">

        {{-- Notificaciones --}}
        <button type="button"
                class="relative w-9 h-9 flex items-center justify-center rounded-lg
                       text-gray-500 dark:text-gray-300
                       hover:bg-gray-100 dark:hover:bg-gray-700 transition"
                aria-label="Notificaciones">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M14.857 17H9.143m9.714 0H19a1 1 0 001-1v-1.382a2 2 0 00-.553-1.382
                         l-1.447-1.566V8a6 6 0 10-12 0v3.67L4.553 13.236A2 2 0 004 14.618V16
                         a1 1 0 001 1h.143m9.714 0a3 3 0 11-5.714 0m5.714 0H9.143"/>
            </svg>
            <span class="absolute top-2 right-2 w-2 h-2 bg-red-500 rounded-full"></span>
        </button>

        {{-- Toggle dark/light --}}
        <button type="button"
                @click="toggleTheme()"
                class="w-9 h-9 flex items-center justify-center rounded-lg
                       text-gray-500 dark:text-gray-300
                       hover:bg-gray-100 dark:hover:bg-gray-700 transition"
                aria-label="Cambiar tema">
            <svg x-show="darkMode" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M12 3v2.25M12 18.75V21M4.72 4.72l1.59 1.59M17.69 17.69l1.59 1.59
                         M3 12h2.25M18.75 12H21M4.72 19.28l1.59-1.59M17.69 6.31l1.59-1.59
                         M15.75 12a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z"/>
            </svg>
            <svg x-show="!darkMode" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M21 12.79A9 9 0 1111.21 3c-.13.6-.21 1.22-.21 1.86
                         a9 9 0 009 8.93c.34 0 .67-.03 1-.1z"/>
            </svg>
        </button>

        {{-- Dropdown usuario --}}
        <x-dropdown align="right" width="48">
            <x-slot name="trigger">
                <button class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm font-medium
                               text-gray-600 dark:text-gray-300
                               hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                    {{ collect(explode(' ', trim(Auth::user()->name ?? '')))->first() }}
{{ Auth::user()->apellido_paterno ?? '' }}
                    <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                              d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414
                                 l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                    </svg>
                </button>
            </x-slot>
            <x-slot name="content">
                <x-dropdown-link :href="route('profile.edit')">
                    {{ __('Perfil') }}
                </x-dropdown-link>
                <div class="border-t border-gray-100 dark:border-gray-700 my-1"></div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                            class="w-full text-left block px-4 py-2 text-sm
                                   text-gray-700 dark:text-gray-300
                                   hover:bg-gray-100 dark:hover:bg-gray-800 transition">
                        Cerrar sesión
                    </button>
                </form>
            </x-slot>
        </x-dropdown>

        {{-- Hamburguesa mobile --}}
        <button type="button"
                onclick="document.getElementById('logo-sidebar').classList.toggle('-translate-x-full')"
                class="sm:hidden w-9 h-9 flex items-center justify-center rounded-lg
                       text-gray-500 dark:text-gray-400
                       hover:bg-gray-100 dark:hover:bg-gray-700 transition"
                aria-label="Menú">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
        </button>

    </div>
</nav>