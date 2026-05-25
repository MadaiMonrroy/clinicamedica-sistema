<x-guest-layout>

    {{-- Encabezado --}}
    <div class="mb-7">
        <h2 class="text-[22px] font-semibold tracking-tight text-gray-900 dark:text-white">
            Bienvenido
        </h2>
        <p class="mt-1 text-sm text-gray-400 dark:text-gray-500">
            Ingresa con tus credenciales asignadas
        </p>
    </div>

    <x-auth-session-status class="mb-5" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        {{-- ── Email ── --}}
        <div>
            <label for="email" class="med-label">Correo electrónico</label>
            <div class="relative">
                <svg class="med-field-icon" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75
                             m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243
                             a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0
                             L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/>
                </svg>
                <input
                    id="email" type="email" name="email"
                    value="{{ old('email') }}"
                    class="med-input"
                    placeholder="usuario@clinica.com"
                    required autofocus autocomplete="username"
                />
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        {{-- ── Password — SIN x-data propio, usa el scope del layout ── --}}
        <div>
            <label for="password" class="med-label">Contraseña</label>
            <div class="relative">
                <svg class="med-field-icon" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75
                             m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75
                             a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75
                             a2.25 2.25 0 002.25 2.25z"/>
                </svg>
                <input
                    id="password"
                    :type="showPass ? 'text' : 'password'"
                    name="password"
                    class="med-input"
                    placeholder="••••••••"
                    style="padding-right:40px;"
                    required autocomplete="current-password"
                />
                {{-- UN SOLO botón ver/ocultar, usa el showPass del layout --}}
                <button type="button" @click="showPass = !showPass"
                    class="med-eye-btn"
                    :aria-label="showPass ? 'Ocultar' : 'Ver contraseña'">
                    <svg x-show="!showPass" fill="none" stroke="currentColor" stroke-width="1.8"
                         viewBox="0 0 24 24" style="width:16px;height:16px;">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5
                                 c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639
                                 C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.964-7.178z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <svg x-show="showPass" x-cloak fill="none" stroke="currentColor" stroke-width="1.8"
                         viewBox="0 0 24 24" style="width:16px;height:16px;">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5
                                 c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5
                                 c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774
                                 M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21
                                 m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88"/>
                    </svg>
                </button>
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        {{-- ── Remember + Forgot ── --}}
        <div class="flex items-center justify-between">
            <label for="remember_me" class="inline-flex items-center gap-2 cursor-pointer group">
                <input id="remember_me" type="checkbox" name="remember"
                    class="w-4 h-4 rounded border-gray-300 dark:border-gray-600
                           bg-white dark:bg-[#111B21]
                           focus:ring-[#52ABB1] focus:ring-offset-0
                           transition-colors cursor-pointer"
                    style="accent-color:#52ABB1;"
                />
                <span class="text-sm text-gray-500 dark:text-gray-400
                             group-hover:text-gray-700 dark:group-hover:text-gray-300 transition-colors">
                    Recordarme
                </span>
            </label>
            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}"
                   class="text-sm font-medium transition-opacity hover:opacity-70"
                   style="color:#52ABB1;">
                    ¿Olvidaste tu contraseña?
                </a>
            @endif
        </div>

        {{-- ── Submit ── --}}
        <button type="submit" class="med-btn-submit">
            Iniciar sesión
            <svg fill="none" stroke="currentColor" stroke-width="2"
                 viewBox="0 0 24 24" style="width:15px;height:15px;">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/>
            </svg>
        </button>

    </form>

</x-guest-layout>