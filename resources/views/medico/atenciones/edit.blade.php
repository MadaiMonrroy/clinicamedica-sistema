<x-app-layout>
    @php
        $card = 'rounded-[2rem] border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 shadow-sm overflow-hidden';
        $inputClass = 'block w-full px-4 py-3 bg-white dark:bg-gray-800/40 border border-gray-200 dark:border-gray-700 focus:border-[#44B0B3] focus:ring-1 focus:ring-[#44B0B3] rounded-2xl text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 transition-all text-sm shadow-sm';
        $labelClass = 'block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2 ml-1';
        $primaryBtn = 'inline-flex items-center justify-center rounded-2xl bg-[#44B0B3] hover:bg-[#389a9d] text-white font-bold px-8 py-3 shadow-lg shadow-[#44B0B3]/25 transition-all active:scale-95';
        $secondaryBtn = 'inline-flex items-center justify-center rounded-2xl border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-800 px-6 py-3 transition-all';
    @endphp

    <div class="max-w-[1600px] mx-auto p-4 lg:p-6 space-y-6">

        {{-- Cabecera --}}
        <header class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <nav class="flex items-center gap-2 text-xs font-black uppercase tracking-widest text-[#44B0B3] mb-1">
                    <span>Atención Médica</span>
                    <span class="text-gray-300">/</span>
                    <span class="text-gray-500">Edición</span>
                </nav>
                <h1 class="text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight">
                    Consulta en Curso
                </h1>
                <div class="flex items-center gap-3 mt-2 text-sm text-gray-500 bg-white dark:bg-gray-800 w-fit px-3 py-1 rounded-full border border-gray-100 dark:border-gray-700 shadow-sm">
                    <span class="font-bold text-gray-700 dark:text-gray-300 italic">{{ $atencion->ticket?->paciente?->nombre_completo }}</span>
                    <span class="text-gray-300">|</span>
                    <span>Ticket #{{ $atencion->ticket?->numero_ticket }}</span>
                </div>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('atenciones.show', $atencion) }}" class="{{ $secondaryBtn }}">Ver Historial</a>
            </div>
        </header>

        <div class="grid grid-cols-12 gap-6">

            {{-- ════════════════════════════════════════════════════════
                 COLUMNA IZQUIERDA — Formulario con dictado
            ════════════════════════════════════════════════════════ --}}
            <div class="col-span-12 lg:col-span-8 space-y-6">
                <form
                    method="POST"
                    action="{{ route('atenciones.update', $atencion) }}"
                    class="{{ $card }}"
                    x-data="dictadoMedico()"
                    x-init="init()"
                >
                    @csrf
                    @method('PUT')

                    {{-- Header del form --}}
                    <div class="p-6 border-b border-gray-100 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-800/50">
                        <div class="flex items-center justify-between gap-4">
                            <h2 class="text-lg font-bold flex items-center gap-2">
                                <svg class="w-5 h-5 text-[#44B0B3]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                Evolución Médica
                            </h2>

                            {{-- Indicador global de dictado activo --}}
                            <div
                                x-show="campoActivo !== null"
                                x-cloak
                                class="flex items-center gap-2 px-3 py-1.5 rounded-full bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800"
                            >
                                {{-- Onda animada --}}
                                <div class="flex items-end gap-0.5 h-4">
                                    <span class="w-0.5 bg-red-500 rounded-full animate-[dictado-bar_0.8s_ease-in-out_infinite]"        style="height:30%"></span>
                                    <span class="w-0.5 bg-red-500 rounded-full animate-[dictado-bar_0.8s_ease-in-out_0.15s_infinite]"  style="height:70%"></span>
                                    <span class="w-0.5 bg-red-500 rounded-full animate-[dictado-bar_0.8s_ease-in-out_0.3s_infinite]"   style="height:100%"></span>
                                    <span class="w-0.5 bg-red-500 rounded-full animate-[dictado-bar_0.8s_ease-in-out_0.45s_infinite]"  style="height:60%"></span>
                                    <span class="w-0.5 bg-red-500 rounded-full animate-[dictado-bar_0.8s_ease-in-out_0.6s_infinite]"   style="height:40%"></span>
                                </div>
                                <span class="text-xs font-bold text-red-600 dark:text-red-400">Dictando en
                                    <span x-text="{
                                        motivo_consulta: 'Motivo',
                                        examen_fisico: 'Examen físico',
                                        diagnostico_texto: 'Diagnóstico'
                                    }[campoActivo] ?? campoActivo"></span>...
                                </span>
                            </div>

                            {{-- Aviso si el navegador no soporta dictado --}}
                            <div x-show="!soportado" x-cloak
                                 class="text-xs text-amber-600 dark:text-amber-400 font-medium px-3 py-1.5 rounded-full bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800">
                                ⚠️ Tu navegador no soporta dictado
                            </div>
                        </div>
                    </div>

                    <div class="p-6 space-y-8">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                            {{-- ── MOTIVO DE CONSULTA ──────────────────────────── --}}
                            <div class="md:col-span-2">
                                <div class="flex items-center justify-between mb-2">
                                    <label class="text-sm font-bold text-gray-700 dark:text-gray-300 ml-1">
                                        Motivo de consulta
                                    </label>
                                    {{-- Botón de micrófono --}}
                                    <button
                                        type="button"
                                        x-show="soportado"
                                        @click="toggleDictado('motivo_consulta')"
                                        :title="campoActivo === 'motivo_consulta' ? 'Detener dictado' : 'Iniciar dictado por voz'"
                                        class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold transition-all"
                                        :class="campoActivo === 'motivo_consulta'
                                            ? 'bg-red-500 text-white shadow-md shadow-red-200 dark:shadow-red-900/40 animate-pulse'
                                            : 'bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400 hover:bg-[#44B0B3]/10 hover:text-[#44B0B3] dark:hover:bg-[#44B0B3]/20'"
                                    >
                                        {{-- Icono micrófono --}}
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

                                {{-- Textarea con borde resaltado al dictar --}}
                                <div class="relative">
                                    <textarea
                                        name="motivo_consulta"
                                        rows="3"
                                        x-ref="motivo_consulta"
                                        @input="textos.motivo_consulta = $event.target.value"
                                        class="{{ $inputClass }} shadow-inner focus:ring-opacity-50 transition-all"
                                        :class="campoActivo === 'motivo_consulta'
                                            ? 'border-red-400 ring-1 ring-red-300 dark:ring-red-700'
                                            : ''"
                                        placeholder="Describa el motivo..."
                                    >{{ old('motivo_consulta', $atencion->motivo_consulta) }}</textarea>

                                    {{-- Texto provisional (interim) mientras dicta --}}
                                    <div
                                        x-show="campoActivo === 'motivo_consulta' && interimText"
                                        x-cloak
                                        class="absolute bottom-2 left-3 right-3 text-xs text-gray-400 dark:text-gray-500 italic truncate pointer-events-none"
                                        x-text="'… ' + interimText"
                                    ></div>
                                </div>
                            </div>

                            {{-- ── EXAMEN FÍSICO ────────────────────────────────── --}}
                            <div class="md:col-span-2">
                                <div class="flex items-center justify-between mb-2">
                                    <label class="text-sm font-bold text-gray-700 dark:text-gray-300 ml-1">
                                        Examen físico
                                    </label>
                                    <button
                                        type="button"
                                        x-show="soportado"
                                        @click="toggleDictado('examen_fisico')"
                                        :title="campoActivo === 'examen_fisico' ? 'Detener dictado' : 'Iniciar dictado por voz'"
                                        class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold transition-all"
                                        :class="campoActivo === 'examen_fisico'
                                            ? 'bg-red-500 text-white shadow-md shadow-red-200 dark:shadow-red-900/40 animate-pulse'
                                            : 'bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400 hover:bg-[#44B0B3]/10 hover:text-[#44B0B3] dark:hover:bg-[#44B0B3]/20'"
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
                                        rows="4"
                                        x-ref="examen_fisico"
                                        @input="textos.examen_fisico = $event.target.value"
                                        class="{{ $inputClass }} transition-all"
                                        :class="campoActivo === 'examen_fisico'
                                            ? 'border-red-400 ring-1 ring-red-300 dark:ring-red-700'
                                            : ''"
                                        placeholder="Hallazgos del examen físico..."
                                    >{{ old('examen_fisico', $atencion->examen_fisico) }}</textarea>

                                    <div
                                        x-show="campoActivo === 'examen_fisico' && interimText"
                                        x-cloak
                                        class="absolute bottom-2 left-3 right-3 text-xs text-gray-400 dark:text-gray-500 italic truncate pointer-events-none"
                                        x-text="'… ' + interimText"
                                    ></div>
                                </div>
                            </div>

                            {{-- ── DIAGNÓSTICO CLÍNICO ──────────────────────────── --}}
                            <div class="md:col-span-2">
                                <div class="flex items-center justify-between mb-2">
                                    <label class="text-sm font-bold text-gray-700 dark:text-gray-300 ml-1">
                                        Diagnóstico clínico
                                    </label>
                                    <button
                                        type="button"
                                        x-show="soportado"
                                        @click="toggleDictado('diagnostico_texto')"
                                        :title="campoActivo === 'diagnostico_texto' ? 'Detener dictado' : 'Iniciar dictado por voz'"
                                        class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold transition-all"
                                        :class="campoActivo === 'diagnostico_texto'
                                            ? 'bg-red-500 text-white shadow-md shadow-red-200 dark:shadow-red-900/40 animate-pulse'
                                            : 'bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400 hover:bg-[#44B0B3]/10 hover:text-[#44B0B3] dark:hover:bg-[#44B0B3]/20'"
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
                                        rows="3"
                                        x-ref="diagnostico_texto"
                                        @input="textos.diagnostico_texto = $event.target.value"
                                        class="{{ $inputClass }} border-l-4 border-l-[#44B0B3] transition-all"
                                        :class="campoActivo === 'diagnostico_texto'
                                            ? 'border-red-400 border-l-red-500 ring-1 ring-red-300 dark:ring-red-700'
                                            : ''"
                                        placeholder="Impresión diagnóstica..."
                                    >{{ old('diagnostico_texto', $atencion->diagnostico_texto) }}</textarea>

                                    <div
                                        x-show="campoActivo === 'diagnostico_texto' && interimText"
                                        x-cloak
                                        class="absolute bottom-2 left-3 right-3 text-xs text-gray-400 dark:text-gray-500 italic truncate pointer-events-none"
                                        x-text="'… ' + interimText"
                                    ></div>
                                </div>
                            </div>

                            {{-- ── ESTADO ───────────────────────────────────────── --}}
                            <div>
                                <label class="{{ $labelClass }}">Estado de Atención</label>
                                <select name="estado" class="{{ $inputClass }}">
                                    <option value="en_curso"    @selected($atencion->estado == 'en_curso')>🟢 En curso</option>
                                    <option value="finalizada"  @selected($atencion->estado == 'finalizada')>🏁 Finalizada</option>
                                    <option value="derivada"    @selected($atencion->estado == 'derivada')>🚑 Derivada</option>
                                    <option value="observacion" @selected($atencion->estado == 'observacion')>🏥 Observación</option>
                                </select>
                            </div>

                        </div>
                    </div>

                    <div class="p-6 bg-gray-50 dark:bg-gray-800/30 flex justify-end gap-4 border-t border-gray-100 dark:border-gray-800">
                        <button type="submit" class="{{ $primaryBtn }}">
                            Guardar y Continuar
                        </button>
                    </div>
                </form>
            </div>

            {{-- ════════════════════════════════════════════════════════
                 COLUMNA DERECHA — sin cambios
            ════════════════════════════════════════════════════════ --}}
            <aside class="col-span-12 lg:col-span-4 space-y-6">

                @if($atencion->ticket?->enfermeria)
                    <div class="{{ $card }}">
                        <div class="p-4 border-b border-gray-100 dark:border-gray-800 flex justify-between items-center">
                            <h3 class="font-bold text-sm text-gray-700 dark:text-gray-300 uppercase italic">Triaje Actual</h3>
                            <span class="text-[10px] bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full font-bold uppercase">
                                {{ $atencion->ticket->enfermeria->fecha_enfermeria->diffForHumans() }}
                            </span>
                        </div>
                        <div class="p-5">
                            @include('medico.atenciones.partials.signos_vitales', [
                                'enfermeria' => $atencion->ticket->enfermeria
                            ])
                        </div>
                    </div>
                @endif

                <div class="{{ $card }} border-red-100 dark:border-red-900/30">
                    <div class="bg-red-50 dark:bg-red-900/20 p-4 border-b border-red-100 dark:border-red-900/30">
                        <h3 class="text-red-700 dark:text-red-400 font-black text-sm uppercase tracking-tighter flex items-center gap-2">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            Alertas de Alergias
                        </h3>
                    </div>
                    <div class="p-5">
                        @include('medico.atenciones.partials.alergias', [
                            'paciente' => $paciente,
                            'medicamentos' => $medicamentos
                        ])
                    </div>
                </div>

            </aside>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════════════
         Alpine.js — Componente de dictado médico
    ══════════════════════════════════════════════════════════════════════ --}}
    {{--
    Reemplaza SOLO el bloque <script> y <style> al final de tu vista edit.blade.php
    Todo lo demás (HTML, form, textareas) queda igual.
--}}
<script>
function dictadoMedico() {
    return {
        // ── Estado ────────────────────────────────────────────────────────
        soportado:       false,
        campoActivo:     null,
        interimText:     '',
        recognition:     null,
        _detenidoManual: false,  // flag para distinguir stop manual vs stop del browser
        _reintentos:     0,

        textoBase: {
            motivo_consulta:   '',
            examen_fisico:     '',
            diagnostico_texto: '',
        },

        // ── Init: solo detecta soporte, NO crea recognition todavía ──────
        init() {
            const SR = window.SpeechRecognition || window.webkitSpeechRecognition;
            this.soportado = !!SR;
        },

        // ── Crea una instancia fresca de SpeechRecognition ───────────────
        // Se hace aquí y no en init() porque algunos navegadores (Chrome/Android)
        // requieren que el objeto se cree justo antes de llamar .start()
        _crearRecognition() {
            const SR = window.SpeechRecognition || window.webkitSpeechRecognition;
            if (!SR) return null;

            const r = new SR();
            r.lang           = 'es-BO'; // cambia a es-ES, es-MX, etc.
            r.continuous     = false;   // ← false es más estable en Chrome móvil/desktop
            r.interimResults = true;

            // ── Resultado de voz ─────────────────────────────────────────
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

            // ── onend: el browser SIEMPRE llama esto al terminar ─────────
            // Con continuous=false termina después de cada frase.
            // Si no fue stop manual → reiniciamos para simular dictado continuo.
            r.onend = () => {
                this.interimText = '';

                if (this._detenidoManual || !this.campoActivo) {
                    // El médico presionó "Detener" → no reiniciamos
                    this.campoActivo     = null;
                    this._detenidoManual = false;
                    this._reintentos     = 0;
                    return;
                }

                // El browser paró solo (silencio, fin de frase) → reiniciar
                if (this._reintentos < 50) { // máximo ~50 ciclos seguidos
                    this._reintentos++;
                    // Creamos una instancia nueva para evitar errores de estado
                    this.recognition = this._crearRecognition();
                    setTimeout(() => {
                        try { this.recognition.start(); } catch(e) {
                            console.warn('restart error:', e);
                        }
                    }, 80);
                } else {
                    // Demasiados reintentos → detener
                    this.campoActivo = null;
                    this._reintentos = 0;
                }
            };

            // ── onerror ──────────────────────────────────────────────────
            r.onerror = (event) => {
                this.interimText = '';

                // 'aborted' = nosotros llamamos .stop(), no es un error real
                if (event.error === 'aborted') return;

                // 'no-speech' = silencio largo, onend se encargará de reiniciar
                if (event.error === 'no-speech') return;

                // 'not-allowed' = el usuario negó el permiso del micrófono
                if (event.error === 'not-allowed') {
                    alert('Permiso de micrófono denegado. Actívalo en la configuración del navegador.');
                    this.campoActivo     = null;
                    this._detenidoManual = true;
                    return;
                }

                console.warn('Speech error:', event.error);
                // Para cualquier otro error, dejamos que onend maneje el reinicio
            };

            return r;
        },

        // ── Toggle dictado por campo ──────────────────────────────────────
        toggleDictado(campo) {

            // ── Caso 1: mismo campo activo → DETENER ─────────────────────
            if (this.campoActivo === campo) {
                this._detenidoManual = true;
                this.campoActivo     = null;
                this.interimText     = '';
                this._reintentos     = 0;
                try { this.recognition?.stop(); } catch(e) {}
                return;
            }

            // ── Caso 2: otro campo activo → detener el anterior ──────────
            if (this.campoActivo) {
                this._detenidoManual = true;
                try { this.recognition?.stop(); } catch(e) {}
                // Pequeño delay antes de arrancar el nuevo
                setTimeout(() => this._iniciarDictado(campo), 200);
                return;
            }

            // ── Caso 3: nada activo → iniciar directo ────────────────────
            this._iniciarDictado(campo);
        },

        // ── Inicia el dictado en un campo específico ──────────────────────
        _iniciarDictado(campo) {
            this._detenidoManual = false;
            this._reintentos     = 0;
            this.interimText     = '';

            // Guardar texto previo del textarea
            const el = this.$refs[campo];
            this.textoBase[campo] = el ? el.value : '';

            // Crear instancia fresca
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



    {{-- ── Animación de las barras de audio ─────────────────────────── --}}
    <style>
        [x-cloak] { display: none !important; }

        @keyframes dictado-bar {
            0%, 100% { transform: scaleY(0.4); }
            50%       { transform: scaleY(1); }
        }
    </style>

</x-app-layout>