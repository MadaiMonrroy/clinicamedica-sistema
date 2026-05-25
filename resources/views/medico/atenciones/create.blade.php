<x-app-layout>
    @php
        $pageWrap     = 'space-y-6';
        $card         = 'rounded-[2rem] border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 shadow-sm';
        $inputClass   = 'block w-full px-4 py-3 bg-white dark:bg-gray-800/40 border border-gray-200 dark:border-gray-700 focus:border-[#44B0B3] focus:ring-1 focus:ring-[#44B0B3] rounded-2xl text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 transition-all text-sm shadow-sm';
        $labelClass   = 'block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2';
        $primaryBtn   = 'inline-flex items-center justify-center rounded-2xl bg-[#44B0B3] hover:bg-[#389a9d] text-white font-bold px-5 py-3 shadow-lg shadow-[#44B0B3]/25 transition';
        $secondaryBtn = 'inline-flex items-center justify-center rounded-2xl border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-800 px-5 py-3 transition';
        $badge        = 'inline-flex items-center rounded-full px-2.5 py-1 text-[11px] font-bold uppercase tracking-[0.12em]';
    @endphp

    <div class="mx-auto {{ $pageWrap }}" x-data="dictadoMedico()" x-init="init()">

        {{-- ── Cabecera + signos vitales ── --}}
        <section class="{{ $card }} p-5 sm:p-6">
            <div class="flex flex-col xl:flex-row xl:items-start xl:justify-between gap-5">
                <div>
                    <p class="text-xs font-black uppercase tracking-[0.18em] text-gray-400 dark:text-gray-500">
                        Atención médica / Nueva atención
                    </p>
                    <h1 class="mt-2 text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white">
                        {{ $ticket->paciente?->nombre_completo ?? 'Paciente' }}
                    </h1>
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                        Ticket: {{ $ticket->numero_ticket }} · Área: {{ $ticket->area?->nombre ?? '-' }}
                    </p>
                </div>
                <div class="flex flex-wrap gap-3">
                    <span class="{{ $badge }} bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300">
                        {{ $ticket->estado }}
                    </span>

                    {{-- ── Indicador global de dictado activo ── --}}
                    <div
                        x-show="campoActivo !== null"
                        x-cloak
                        class="flex items-center gap-2 px-3 py-1.5 rounded-full bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800"
                    >
                        <div class="flex items-end gap-0.5 h-4">
                            <span class="w-0.5 bg-red-500 rounded-full" style="height:30%;animation:dictado-bar .8s ease-in-out infinite"></span>
                            <span class="w-0.5 bg-red-500 rounded-full" style="height:70%;animation:dictado-bar .8s ease-in-out .15s infinite"></span>
                            <span class="w-0.5 bg-red-500 rounded-full" style="height:100%;animation:dictado-bar .8s ease-in-out .3s infinite"></span>
                            <span class="w-0.5 bg-red-500 rounded-full" style="height:60%;animation:dictado-bar .8s ease-in-out .45s infinite"></span>
                            <span class="w-0.5 bg-red-500 rounded-full" style="height:40%;animation:dictado-bar .8s ease-in-out .6s infinite"></span>
                        </div>
                        <span class="text-xs font-bold text-red-600 dark:text-red-400">Dictando en
                            <span x-text="{
                                motivo_consulta:   'Motivo',
                                examen_fisico:     'Examen físico',
                                diagnostico_texto: 'Diagnóstico'
                            }[campoActivo]"></span>...
                        </span>
                    </div>

                    {{-- Aviso sin soporte --}}
                    <div x-show="!soportado" x-cloak
                         class="text-xs text-amber-600 dark:text-amber-400 font-medium px-3 py-1.5 rounded-full
                                bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800">
                        ⚠️ Tu navegador no soporta dictado
                    </div>
                </div>
            </div>

            {{-- Signos vitales --}}
            @if($ticket->enfermeria)
                <div class="mt-6">
                    @include('medico.atenciones.partials.signos_vitales', ['enfermeria' => $ticket->enfermeria])
                </div>
            @endif
        </section>

        @if($errors->any())
            <div class="rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700
                        dark:border-red-800 dark:bg-red-900/20 dark:text-red-300">
                Revisa los campos del formulario.
            </div>
        @endif

        {{-- ── Formulario ── --}}
        <form method="POST" action="{{ route('atenciones.store', $ticket) }}" class="{{ $card }} overflow-hidden">
            @csrf

            <div class="px-5 sm:px-6 py-5 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white">Registro clínico inicial</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">Documenta la consulta médica base del paciente.</p>
            </div>

            <div class="px-5 sm:px-6 py-5 space-y-8">

                {{-- ══ CONSULTA ══════════════════════════════════════════════ --}}
                <section class="space-y-4">
                    <h4 class="text-sm font-black uppercase tracking-[0.15em] text-gray-400 dark:text-gray-500">
                        Consulta
                    </h4>

                    {{-- Motivo de consulta --}}
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <label class="text-sm font-semibold text-gray-700 dark:text-gray-300">
                                Motivo de consulta
                            </label>
                            {{-- Botón dictar --}}
                            <button
                                type="button"
                                x-show="soportado"
                                @click="toggleDictado('motivo_consulta')"
                                :title="campoActivo === 'motivo_consulta' ? 'Detener dictado' : 'Dictar por voz'"
                                class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold transition-all select-none"
                                :class="campoActivo === 'motivo_consulta'
                                    ? 'bg-red-500 text-white shadow-md shadow-red-200 dark:shadow-red-900/40 animate-pulse'
                                    : 'bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400 hover:bg-[#44B0B3]/10 hover:text-[#44B0B3]'"
                            >
                                {{-- Icono mic --}}
                                <svg x-show="campoActivo !== 'motivo_consulta'" class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 1a4 4 0 014 4v6a4 4 0 01-8 0V5a4 4 0 014-4zm0 2a2 2 0 00-2 2v6a2 2 0 004 0V5a2 2 0 00-2-2zm-1 14.93V20H9v2h6v-2h-2v-2.07A8.001 8.001 0 0120 11h-2a6 6 0 01-12 0H4a8.001 8.001 0 007 7.93z"/>
                                </svg>
                                {{-- Icono stop --}}
                                <svg x-show="campoActivo === 'motivo_consulta'" class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24">
                                    <rect x="6" y="6" width="12" height="12" rx="2"/>
                                </svg>
                                <span x-text="campoActivo === 'motivo_consulta' ? 'Detener' : 'Dictar'"></span>
                            </button>
                        </div>

                        <div class="relative">
                            <textarea
                                name="motivo_consulta"
                                rows="4"
                                x-ref="motivo_consulta"
                                @input="textoBase.motivo_consulta = $event.target.value"
                                class="{{ $inputClass }} transition-all"
                                :class="campoActivo === 'motivo_consulta'
                                    ? 'border-red-400 ring-1 ring-red-300 dark:ring-red-700'
                                    : ''"
                                placeholder="Describe el motivo principal de consulta"
                            >{{ old('motivo_consulta') }}</textarea>

                            {{-- Texto provisional --}}
                            <div
                                x-show="campoActivo === 'motivo_consulta' && interimText"
                                x-cloak
                                class="absolute bottom-2 left-3 right-3 text-xs text-gray-400 italic truncate pointer-events-none"
                                x-text="'… ' + interimText"
                            ></div>
                        </div>
                        @error('motivo_consulta')
                            <p class="mt-2 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </section>

                {{-- ══ EVALUACIÓN MÉDICA ════════════════════════════════════ --}}
                <section class="space-y-4">
                    <h4 class="text-sm font-black uppercase tracking-[0.15em] text-gray-400 dark:text-gray-500">
                        Evaluación médica
                    </h4>

                    {{-- Examen físico --}}
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <label class="text-sm font-semibold text-gray-700 dark:text-gray-300">
                                Examen físico
                            </label>
                            <button
                                type="button"
                                x-show="soportado"
                                @click="toggleDictado('examen_fisico')"
                                :title="campoActivo === 'examen_fisico' ? 'Detener dictado' : 'Dictar por voz'"
                                class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold transition-all select-none"
                                :class="campoActivo === 'examen_fisico'
                                    ? 'bg-red-500 text-white shadow-md shadow-red-200 dark:shadow-red-900/40 animate-pulse'
                                    : 'bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400 hover:bg-[#44B0B3]/10 hover:text-[#44B0B3]'"
                            >
                                <svg x-show="campoActivo !== 'examen_fisico'" class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 1a4 4 0 014 4v6a4 4 0 01-8 0V5a4 4 0 014-4zm0 2a2 2 0 00-2 2v6a2 2 0 004 0V5a2 2 0 00-2-2zm-1 14.93V20H9v2h6v-2h-2v-2.07A8.001 8.001 0 0120 11h-2a6 6 0 01-12 0H4a8.001 8.001 0 007 7.93z"/>
                                </svg>
                                <svg x-show="campoActivo === 'examen_fisico'" class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24">
                                    <rect x="6" y="6" width="12" height="12" rx="2"/>
                                </svg>
                                <span x-text="campoActivo === 'examen_fisico' ? 'Detener' : 'Dictar'"></span>
                            </button>
                        </div>

                        <div class="relative">
                            <textarea
                                name="examen_fisico"
                                rows="6"
                                x-ref="examen_fisico"
                                @input="textoBase.examen_fisico = $event.target.value"
                                class="{{ $inputClass }} transition-all"
                                :class="campoActivo === 'examen_fisico'
                                    ? 'border-red-400 ring-1 ring-red-300 dark:ring-red-700'
                                    : ''"
                                placeholder="Registra examen físico, hallazgos y apreciación clínica"
                            >{{ old('examen_fisico') }}</textarea>

                            <div
                                x-show="campoActivo === 'examen_fisico' && interimText"
                                x-cloak
                                class="absolute bottom-2 left-3 right-3 text-xs text-gray-400 italic truncate pointer-events-none"
                                x-text="'… ' + interimText"
                            ></div>
                        </div>
                        @error('examen_fisico')
                            <p class="mt-2 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Diagnóstico inicial --}}
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <label class="text-sm font-semibold text-gray-700 dark:text-gray-300">
                                Diagnóstico inicial
                            </label>
                            <button
                                type="button"
                                x-show="soportado"
                                @click="toggleDictado('diagnostico_texto')"
                                :title="campoActivo === 'diagnostico_texto' ? 'Detener dictado' : 'Dictar por voz'"
                                class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold transition-all select-none"
                                :class="campoActivo === 'diagnostico_texto'
                                    ? 'bg-red-500 text-white shadow-md shadow-red-200 dark:shadow-red-900/40 animate-pulse'
                                    : 'bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400 hover:bg-[#44B0B3]/10 hover:text-[#44B0B3]'"
                            >
                                <svg x-show="campoActivo !== 'diagnostico_texto'" class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 1a4 4 0 014 4v6a4 4 0 01-8 0V5a4 4 0 014-4zm0 2a2 2 0 00-2 2v6a2 2 0 004 0V5a2 2 0 00-2-2zm-1 14.93V20H9v2h6v-2h-2v-2.07A8.001 8.001 0 0120 11h-2a6 6 0 01-12 0H4a8.001 8.001 0 007 7.93z"/>
                                </svg>
                                <svg x-show="campoActivo === 'diagnostico_texto'" class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24">
                                    <rect x="6" y="6" width="12" height="12" rx="2"/>
                                </svg>
                                <span x-text="campoActivo === 'diagnostico_texto' ? 'Detener' : 'Dictar'"></span>
                            </button>
                        </div>

                        <div class="relative">
                            <textarea
                                name="diagnostico_texto"
                                rows="4"
                                x-ref="diagnostico_texto"
                                @input="textoBase.diagnostico_texto = $event.target.value"
                                class="{{ $inputClass }} transition-all"
                                :class="campoActivo === 'diagnostico_texto'
                                    ? 'border-red-400 ring-1 ring-red-300 dark:ring-red-700'
                                    : ''"
                                placeholder="Escribe el diagnóstico clínico inicial"
                            >{{ old('diagnostico_texto') }}</textarea>

                            <div
                                x-show="campoActivo === 'diagnostico_texto' && interimText"
                                x-cloak
                                class="absolute bottom-2 left-3 right-3 text-xs text-gray-400 italic truncate pointer-events-none"
                                x-text="'… ' + interimText"
                            ></div>
                        </div>
                        @error('diagnostico_texto')
                            <p class="mt-2 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                </section>
            </div>

            <div class="px-5 sm:px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900">
                <div class="flex items-center justify-end gap-3">
                    <a href="{{ route('tickets.show', $ticket) }}" class="{{ $secondaryBtn }}">Cancelar</a>
                    <button type="submit" class="{{ $primaryBtn }}">Guardar atención</button>
                </div>
            </div>
        </form>
    </div>

    {{-- ══ Script de dictado (idéntica lógica al edit) ══════════════════════ --}}
    <script>
    function dictadoMedico() {
        return {
            soportado:       false,
            campoActivo:     null,
            interimText:     '',
            recognition:     null,
            _detenidoManual: false,
            _reintentos:     0,

            textoBase: {
                motivo_consulta:   '',
                examen_fisico:     '',
                diagnostico_texto: '',
            },

            init() {
                const SR = window.SpeechRecognition || window.webkitSpeechRecognition;
                this.soportado = !!SR;
            },

            _crearRecognition() {
                const SR = window.SpeechRecognition || window.webkitSpeechRecognition;
                if (!SR) return null;

                const r = new SR();
                r.lang           = 'es-BO';
                r.continuous     = false;
                r.interimResults = true;

                r.onresult = (event) => {
                    let finalSegment   = '';
                    let interimSegment = '';

                    for (let i = event.resultIndex; i < event.results.length; i++) {
                        const t = event.results[i][0].transcript;
                        if (event.results[i].isFinal) {
                            finalSegment   += t;
                        } else {
                            interimSegment += t;
                        }
                    }

                    this.interimText = interimSegment;

                    if (finalSegment && this.campoActivo) {
                        const campo     = this.campoActivo;
                        const separador = this.textoBase[campo].trim() ? ' ' : '';
                        this.textoBase[campo] += separador + finalSegment.trim();

                        const el = this.$refs[campo];
                        if (el) {
                            el.value     = this.textoBase[campo];
                            el.scrollTop = el.scrollHeight;
                        }
                    }
                };

                r.onend = () => {
                    this.interimText = '';

                    if (this._detenidoManual || !this.campoActivo) {
                        this.campoActivo     = null;
                        this._detenidoManual = false;
                        this._reintentos     = 0;
                        return;
                    }

                    if (this._reintentos < 50) {
                        this._reintentos++;
                        this.recognition = this._crearRecognition();
                        setTimeout(() => {
                            try { this.recognition.start(); } catch(e) {}
                        }, 80);
                    } else {
                        this.campoActivo = null;
                        this._reintentos = 0;
                    }
                };

                r.onerror = (event) => {
                    this.interimText = '';
                    if (event.error === 'aborted')   return;
                    if (event.error === 'no-speech') return;
                    if (event.error === 'not-allowed') {
                        alert('Permiso de micrófono denegado. Actívalo en la configuración del navegador.');
                        this.campoActivo     = null;
                        this._detenidoManual = true;
                        return;
                    }
                    console.warn('Speech error:', event.error);
                };

                return r;
            },

            toggleDictado(campo) {
                // Mismo campo → detener
                if (this.campoActivo === campo) {
                    this._detenidoManual = true;
                    this.campoActivo     = null;
                    this.interimText     = '';
                    this._reintentos     = 0;
                    try { this.recognition?.stop(); } catch(e) {}
                    return;
                }

                // Otro campo activo → detener primero, luego iniciar
                if (this.campoActivo) {
                    this._detenidoManual = true;
                    try { this.recognition?.stop(); } catch(e) {}
                    setTimeout(() => this._iniciarDictado(campo), 200);
                    return;
                }

                // Nada activo → iniciar directo
                this._iniciarDictado(campo);
            },

            _iniciarDictado(campo) {
                this._detenidoManual = false;
                this._reintentos     = 0;
                this.interimText     = '';

                const el = this.$refs[campo];
                this.textoBase[campo] = el ? el.value : '';

                this.recognition = this._crearRecognition();
                if (!this.recognition) return;

                this.campoActivo = campo;

                try {
                    this.recognition.start();
                } catch(e) {
                    console.error('No se pudo iniciar dictado:', e);
                    this.campoActivo = null;
                }
            },
        };
    }
    </script>

    <style>
        [x-cloak] { display: none !important; }
        @keyframes dictado-bar {
            0%, 100% { transform: scaleY(0.4); }
            50%       { transform: scaleY(1); }
        }
    </style>

</x-app-layout>