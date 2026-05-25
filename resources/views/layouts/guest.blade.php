<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Medicalia') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet"/>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,400&family=Playfair+Display:ital,wght@0,400;0,500;0,600;1,500&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Anti-flash: aplica clase dark ANTES de pintar el DOM --}}
    <script>
        (function(){
            const s = localStorage.getItem('theme');
            const os = window.matchMedia('(prefers-color-scheme: dark)').matches;
            if (s === 'dark' || (!s && os)) document.documentElement.classList.add('dark');
        })();
    </script>

    <style>
        body { font-family: 'DM Sans', 'Figtree', sans-serif; }

        /* ══════════════════════════════════════════
           ASIDE — Fondo animado
        ══════════════════════════════════════════ */
        .med-dot-grid {
            position: absolute; inset: 0;
            background-image: radial-gradient(circle, rgba(82,171,177,0.18) 1px, transparent 1px);
            background-size: 34px 34px;
            animation: medGridDrift 20s linear infinite;
            pointer-events: none;
        }
        @keyframes medGridDrift {
            0%   { background-position: 0 0 }
            100% { background-position: 34px 34px }
        }

        .med-ring {
            position: absolute; border-radius: 50%;
            border: 1px solid rgba(82,171,177,0.15);
            pointer-events: none;
            animation: medRingPulse 8s ease-in-out infinite;
        }
        .med-r1 { width:580px; height:580px; top:-190px; right:-190px; animation-delay:0s }
        .med-r2 { width:400px; height:400px; top:-110px; right:-110px; animation-delay:1.5s; border-color:rgba(82,171,177,0.10) }
        .med-r3 { width:240px; height:240px; top:-50px;  right:-50px;  animation-delay:3s;   border-color:rgba(82,171,177,0.22) }
        .med-r4 { width:380px; height:380px; bottom:-160px; left:-130px; animation-delay:2s; border-color:rgba(82,171,177,0.07) }
        .med-r5 { width:220px; height:220px; bottom:-90px;  left:-70px;  animation-delay:4s; border-color:rgba(82,171,177,0.10) }
        @keyframes medRingPulse {
            0%,100% { transform:scale(1);    opacity:1   }
            50%     { transform:scale(1.04); opacity:0.5 }
        }

        .med-cross-spin {
            position: absolute; right:-50px; bottom:550px;
            
            width:420px; height:420px; opacity:0.04;
            pointer-events: none;
            
        }
        

        .med-beam {
            position: absolute; top:-100px; width:1px; height:140%;
            background: linear-gradient(to bottom, transparent, rgba(82,171,177,0.12), transparent);
            transform: rotate(15deg);
            pointer-events: none;
            animation: medBeamMove 12s ease-in-out infinite;
        }
        .med-beam-1 { left:28% }
        .med-beam-2 { left:52%; animation-delay:4s; opacity:.6 }
        @keyframes medBeamMove {
            0%,100% { opacity:.5; transform:rotate(15deg) translateX(0) }
            50%     { opacity:1;  transform:rotate(15deg) translateX(28px) }
        }

        .med-aside-vline {
            position: absolute; top:10%; bottom:10%; right:0; width:1px;
            background: linear-gradient(to bottom, transparent, rgba(82,171,177,0.35) 40%, rgba(82,171,177,0.35) 60%, transparent);
        }

        /* ── Logo icon glow ── */
        .med-logo-icon-wrap {
            position: relative;
        }
        .med-logo-icon-wrap::after {
            content: '';
            position: absolute; inset: -1px;
            border-radius: 14px;
            border: 1px solid rgba(82,171,177,0.5);
            animation: medIconGlow 3s ease-in-out infinite;
        }
        @keyframes medIconGlow {
            0%,100% { opacity:.4; transform:scale(1) }
            50%     { opacity:1;  transform:scale(1.06) }
        }

        /* ── Eyebrow dot blink ── */
        .med-eyebrow-dot {
            width:7px; height:7px; border-radius:50%;
            background:#52ABB1; display:inline-block;
            animation: medDotBlink 2s ease-in-out infinite;
        }
        @keyframes medDotBlink {
            0%,100% { opacity:1;   transform:scale(1)   }
            50%     { opacity:0.4; transform:scale(0.7) }
        }

        /* ── Título em subrayado ── */
        .med-title-em {
            color:#52ABB1; font-style:italic;
            position:relative; display:inline-block;
        }
        .med-title-em::after {
            content:'';
            position:absolute; bottom:-5px; left:0; right:0;
            height:2.5px;
            background: linear-gradient(to right, #52ABB1, transparent);
            animation: medLineGrow 1.2s 0.9s both;
        }
        @keyframes medLineGrow {
            from { width:0; opacity:0 } to { width:100%; opacity:1 }
        }

        /* ── Tarjetas de módulo ── */
        .med-module-card {
            background: rgba(82,171,177,0.08);
            border: 1px solid rgba(82,171,177,0.18);
            border-radius: 14px;
            padding: 16px;
            display: flex; align-items: center; gap: 12px;
            transition: all 0.25s; cursor: default;
        }
        .med-module-card:hover {
            background: rgba(82,171,177,0.16);
            border-color: rgba(82,171,177,0.38);
            transform: translateY(-2px);
        }

        /* ── Stats row ── */
        .med-stat-row {
            display: flex; gap:0;
            border: 1px solid rgba(255,255,255,0.07);
            border-radius: 16px; overflow: hidden;
        }
        .med-stat-item {
            flex:1; padding:20px 24px;
            border-right: 1px solid rgba(255,255,255,0.07);
            position:relative; background:rgba(255,255,255,0.02);
            transition: background 0.2s;
        }
        .med-stat-item:last-child { border-right:none }
        .med-stat-item::before {
            content:''; position:absolute; top:0; left:0; right:0;
            height:2px; background:transparent; transition:background 0.3s;
        }
        .med-stat-item:hover::before {
            background: linear-gradient(to right, #52ABB1, transparent);
        }

        /* ── Animaciones de entrada escalonadas ── */
        .med-anim   { animation: medFadeUp 0.7s ease both; }
        .med-anim-0 { animation-delay:0s    }
        .med-anim-1 { animation-delay:0.12s }
        .med-anim-2 { animation-delay:0.24s }
        .med-anim-3 { animation-delay:0.38s }
        .med-anim-4 { animation-delay:0.52s }
        .med-anim-5 { animation-delay:0.66s }
        @keyframes medFadeUp {
            from { opacity:0; transform:translateY(18px) }
            to   { opacity:1; transform:translateY(0)    }
        }

        /* ══════════════════════════════════════════
           PANEL DERECHO — inputs y componentes
        ══════════════════════════════════════════ */
        .med-input {
            display:block; width:100%;
            padding: 12px 12px 12px 40px;
            font-size:14px; font-family:'DM Sans',sans-serif;
            border-radius:10px;
            border: 1.5px solid #e5e7eb;
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s, background 0.2s;
            background: #f9fafb; color: #111;
        }
        .med-input::placeholder { color:#d1d5db }
        .med-input:focus {
            border-color:#52ABB1;
            box-shadow:0 0 0 3px rgba(82,171,177,0.13);
            background:#fff;
        }
        .dark .med-input {
            background:#111B21;
            border-color:rgba(255,255,255,0.09);
            color:#e0e0e0;
        }
        .dark .med-input::placeholder { color:rgba(255,255,255,0.20) }
        .dark .med-input:focus {
            background:rgba(82,171,177,0.06);
            border-color:#52ABB1;
            box-shadow:0 0 0 3px rgba(82,171,177,0.12);
        }

        .med-field-icon {
            position:absolute; left:12px; top:50%;
            transform:translateY(-50%);
            width:16px; height:16px; pointer-events:none; color:#d1d5db;
        }
        .dark .med-field-icon { color:rgba(255,255,255,0.20) }

        .med-eye-btn {
            position:absolute; right:11px; top:50%;
            transform:translateY(-50%);
            background:none; border:none; cursor:pointer; padding:0;
            width:16px; height:16px; color:#d1d5db; transition:color 0.2s;
        }
        .med-eye-btn:hover { color:#52ABB1 }
        .dark .med-eye-btn { color:rgba(255,255,255,0.22) }

        .med-label {
            display:block; font-size:11px; font-weight:500;
            letter-spacing:0.07em; text-transform:uppercase;
            color:#9ca3af; margin-bottom:7px;
        }
        .dark .med-label { color:rgba(255,255,255,0.30) }

        .med-btn-submit {
            width:100%; background:#52ABB1; color:#fff;
            border:none; border-radius:10px; padding:13px 16px;
            font-size:14px; font-weight:600; font-family:'DM Sans',sans-serif;
            cursor:pointer;
            display:flex; align-items:center; justify-content:center; gap:8px;
            transition: background 0.2s, transform 0.15s, box-shadow 0.2s;
            letter-spacing:0.15px;
        }
        .med-btn-submit:hover {
            background:#3f9aa0; transform:translateY(-1px);
            box-shadow:0 6px 22px rgba(82,171,177,0.38);
        }
        .med-btn-submit:active    { transform:translateY(0) }
        .med-btn-submit:focus-visible {
            outline:none; box-shadow:0 0 0 3px rgba(82,171,177,0.35);
        }

        .med-progress-dot {
            width:6px; height:6px; border-radius:50%;
            background:#e5e7eb; transition:all 0.25s;
        }
        .dark .med-progress-dot { background:rgba(255,255,255,0.12) }
        .med-progress-dot.active { width:20px; border-radius:3px; background:#52ABB1; }

        .med-toggle-pill {
            display:flex; align-items:center; gap:2px;
            background:#f3f4f6; border-radius:100px; padding:3px;
            transition:background 0.3s;
        }
        .dark .med-toggle-pill { background:#2A3942 }

        .med-toggle-btn {
            width:32px; height:32px; border-radius:100px;
            display:flex; align-items:center; justify-content:center;
            border:none; cursor:pointer;
            background:transparent; color:#9ca3af; transition:all 0.2s;
        }
        .med-toggle-btn.active {
            background:#fff; color:#374151;
            box-shadow:0 1px 4px rgba(0,0,0,0.10);
        }
        .dark .med-toggle-btn.active { background:#202C33; color:#e5e7eb; }

        .med-form-scroll::-webkit-scrollbar { width:4px }
        .med-form-scroll::-webkit-scrollbar-track { background:transparent }
        .med-form-scroll::-webkit-scrollbar-thumb { background:rgba(82,171,177,0.3); border-radius:2px; }
        /* ── Fix: autofill del navegador no tape el icono ── */
input:-webkit-autofill,
input:-webkit-autofill:hover,
input:-webkit-autofill:focus {
    -webkit-text-fill-color: #111;
    -webkit-box-shadow: 0 0 0px 1000px #f9fafb inset;
    transition: background-color 5000s ease-in-out 0s;
}
.dark input:-webkit-autofill,
.dark input:-webkit-autofill:hover,
.dark input:-webkit-autofill:focus {
    -webkit-text-fill-color: #e0e0e0;
    -webkit-box-shadow: 0 0 0px 1000px #111B21 inset;
}
/* Oculta el ojito nativo de Chrome/Edge */
input::-ms-reveal,
input::-ms-clear {
    display: none;
}

input[type="password"]::-webkit-credentials-auto-fill-button {
    visibility: hidden;
}
    </style>
</head>

<body class="antialiased">

<div
    x-data="{
        darkMode: document.documentElement.classList.contains('dark'),
        showPass: false,
        toggleTheme() {
            this.darkMode = !this.darkMode;
            document.documentElement.classList.toggle('dark', this.darkMode);
            localStorage.setItem('theme', this.darkMode ? 'dark' : 'light');
        }
    }"
    class="min-h-screen flex"
>

    {{-- ══════════════════════════════════════════════════════════
         ASIDE — Panel izquierdo 65% — Brand / decorativo
         Fondo oscuro fijo, NO sigue el tema del usuario
    ══════════════════════════════════════════════════════════ --}}
    <aside
        class="hidden lg:flex lg:w-[65%] flex-col relative overflow-hidden"
        style="background:#0D1B22; padding:40px 52px; gap:28px;"
    >
        {{-- Fondo animado --}}
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div class="med-dot-grid"></div>
            <div class="med-ring med-r1"></div>
            <div class="med-ring med-r2"></div>
            <div class="med-ring med-r3"></div>
            <div class="med-ring med-r4"></div>
            <div class="med-ring med-r5"></div>
            <svg class="med-cross-spin" viewBox="0 0 100 100" fill="none">
                <rect x="35" y="5" width="30" height="90" rx="8" fill="white"/>
                <rect x="5" y="35" width="90" height="30" rx="8" fill="white"/>
            </svg>
            <div class="med-beam med-beam-1"></div>
            <div class="med-beam med-beam-2"></div>
        </div>
        <div class="med-aside-vline"></div>

        {{-- ══ BARRA SUPERIOR: Logo Medicalia + Logo empresa ══ --}}
        <div class="relative z-10 flex items-center justify-between flex-shrink-0 med-anim med-anim-0">

            {{-- Logo Medicalia --}}
            <div class="flex items-center gap-4">
                <div class="med-logo-icon-wrap w-12 h-12 rounded-[14px] flex items-center justify-center flex-shrink-0"
                     style="background:rgba(82,171,177,0.18); border:1px solid rgba(82,171,177,0.32);">
                    <svg width="23" height="23" fill="none" stroke="#52ABB1" stroke-width="1.7" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108
                                 c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08
                                 m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5
                                 a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664
                                 m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586
                                 m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25
                                 m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25
                                 c0 .621.504 1.125 1.125 1.125h9.75
                                 c.621 0 1.125-.504 1.125-1.125V9.375
                                 c0-.621-.504-1.125-1.125-1.125H8.25z"/>
                    </svg>
                </div>
                <div class="flex items-baseline gap-3">
                    <span style="font-size:24px; font-weight:700; color:#fff; letter-spacing:-0.5px; text-transform:uppercase;">
                        MEDI<span style="color:#52ABB1">CALIA</span>
                    </span>
                    <span style="font-size:9px; text-transform:uppercase; letter-spacing:0.8px; color:rgba(255,255,255,0.28);
                                 border-left:2px solid rgba(82,171,177,0.35); padding-left:10px; line-height:1.4;">
                        Sistema<br>Clínico
                    </span>
                </div>
            </div>

            {{-- ★ LOGO EMPRESA DESARROLLADORA
                 Reemplaza el contenido de .dev-icon con tu logo real (SVG o <img>)
                 y cambia "TuEmpresa" por el nombre de tu empresa.
                 Si tienes un SVG de tu logo, ponlo directamente en el div .dev-icon.
            --}}
            <div class="flex items-center gap-3 rounded-xl px-4 py-[10px]"
                 style="background:rgba(255,255,255,0.04); border:1px solid rgba(255,255,255,0.08);">
                {{-- Contenedor del logo de tu empresa --}}
                <div class="w-8 h-8 rounded-[8px] flex items-center justify-center flex-shrink-0"
                     style="background:rgba(82,171,177,0.15); border:1px solid rgba(82,171,177,0.22);">
                    <img src="{{ asset('images/logos/altecbol_icon.png') }}" class="w-8 h-8"> 
                    
                </div>
                <div style="line-height:1.2;">
                    <p style="font-size:9px; text-transform:uppercase; letter-spacing:0.7px; color:rgba(255,255,255,0.25);">
                        Desarrollado por
                    </p>
                    {{-- REEMPLAZA "TuEmpresa S.A." con el nombre real --}}
                    <p style="font-size:12px; font-weight:600; color:rgb(255, 255, 255);">
                        ALTECBOL
                    </p>
                </div>
            </div>
        </div>

        {{-- ══ HERO ══ --}}
        <div class="relative z-10 flex-1 flex flex-col justify-center">

            {{-- Eyebrow --}}
            <div class="flex items-center gap-2 mb-5 med-anim med-anim-1"
                 style="font-size:12px; font-weight:500; text-transform:uppercase; letter-spacing:1.2px; color:#52ABB1;">
                <span class="med-eyebrow-dot"></span>
                Plataforma médica 
            </div>

            {{-- Título --}}
            <h1 class="med-anim med-anim-2"
                style="font-family:'Playfair Display',serif; font-size:64px; font-weight:500;
                       line-height:1.02; color:#fff; letter-spacing:-2px; margin-bottom:18px;">
                Gestión<br>
                <span class="med-title-em">médica digital</span><br>
                integral
            </h1>

            {{-- Subtítulo --}}
            <p class="med-anim med-anim-3"
               style="font-size:16px; color:rgba(255,255,255,0.42); line-height:1.7; max-width:480px;">
                Plataforma unificada para el manejo completo<br>
                de pacientes y flujos clínicos
            </p>

            {{-- Grid de módulos --}}
            <div class="grid grid-cols-3 gap-3 mt-8 med-anim med-anim-4">
                @php
                    $modules = [
                        ['Recepción',  'M15 9h3.75M15 12h3.75M15 15h3.75M4.5 19.5h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5zm6-10.125a1.875 1.875 0 11-3.75 0 1.875 1.875 0 013.75 0zm1.294 6.336a6.721 6.721 0 01-3.17.789 6.721 6.721 0 01-3.168-.789 3.376 3.376 0 016.338 0z'],
                        ['Enfermeria',     'M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126z'],
                        ['Consultas',  'M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25z'],
                        ['Laboratorio','M9.75 3.104v5.714a2.25 2.25 0 01-.659 1.591L5 14.5M9.75 3.104c-.251.023-.501.05-.75.082m.75-.082a24.301 24.301 0 014.5 0m0 0v5.714c0 .597.237 1.17.659 1.591L19.8 15.3M14.25 3.104c.251.023.501.05.75.082M19.8 15.3l-1.57.393A9.065 9.065 0 0112 15a9.065 9.065 0 00-6.23-.693L5 14.5m14.8.8l1.402 1.402c1 1-.26 2.242-1.9 2.242H4.852c-1.64 0-2.902-1.241-1.9-2.241L4.2 14.95'],
                        ['Recetas',    'M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z'],
                        ['Reportes',   'M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z'],
                    ];
                @endphp
                @foreach($modules as [$name, $path])
                    <div class="med-module-card">
                        <div class="w-9 h-9 rounded-[10px] flex items-center justify-center flex-shrink-0"
                             style="background:rgba(82,171,177,0.18);">
                            <svg width="17" height="17" fill="none" stroke="#52ABB1" stroke-width="1.8" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $path }}"/>
                            </svg>
                        </div>
                        <span style="font-size:13px; font-weight:500; color:rgba(255,255,255,0.68);">
                            {{ $name }}
                        </span>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- ══ STATS ══ --}}
        <div class="med-stat-row relative z-10 flex-shrink-0 med-anim med-anim-5">
            @foreach([['6','Áreas activas'],['4','Roles de usuario'],['24/7','Disponibilidad']] as [$n,$l])
                <div class="med-stat-item">
                    <p style="font-size:32px; font-weight:700; color:#52ABB1; line-height:1;">{{ $n }}</p>
                    <p style="font-size:11px; color:rgba(255,255,255,0.28); margin-top:6px; text-transform:uppercase; letter-spacing:.8px;">
                        {{ $l }}
                    </p>
                </div>
            @endforeach
        </div>
    </aside>

    {{-- ══════════════════════════════════════════════════════════
         MAIN — Panel derecho 35% — Formulario (sigue el tema)
    ══════════════════════════════════════════════════════════ --}}
    <main class="flex-1 flex flex-col
                 bg-white dark:bg-[#202C33]
                 transition-colors duration-300
                 med-form-scroll overflow-y-auto">

        {{-- ── Top bar: Logo Medicalia + toggle dark/light ── --}}
        <div class="flex items-center justify-between px-7 py-5
                    border-b border-gray-100 dark:border-white/[0.05]
                    transition-colors duration-300 flex-shrink-0">

            {{-- Logo Medicalia encima del formulario --}}
            <div class="flex items-center gap-3">
                
            </div>

            {{-- Toggle pastilla sol / luna --}}
            <div class="med-toggle-pill">
                <button type="button"
                    @click="if(darkMode) toggleTheme()"
                    :class="!darkMode ? 'active' : ''"
                    class="med-toggle-btn" aria-label="Modo claro">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M12 3v2.25M12 18.75V21M4.72 4.72l1.59 1.59M17.69 17.69l1.59 1.59
                                 M3 12h2.25M18.75 12H21M4.72 19.28l1.59-1.59M17.69 6.31l1.59-1.59
                                 M15.75 12a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z"/>
                    </svg>
                </button>
                <button type="button"
                    @click="if(!darkMode) toggleTheme()"
                    :class="darkMode ? 'active' : ''"
                    class="med-toggle-btn" aria-label="Modo oscuro">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M21 12.79A9 9 0 1111.21 3c-.13.6-.21 1.22-.21 1.86
                                 a9 9 0 009 8.93c.34 0 .67-.03 1-.1z"/>
                    </svg>
                </button>
            </div>
        </div>

        {{-- ── Formulario centrado ── --}}
        <div class="flex-1 flex items-center justify-center px-8 py-8">
            
            <div class="w-full max-w-sm">
{{-- LOGO CENTRADO --}}
<div class="flex flex-col items-center mb-8 med-anim med-anim-0">

    {{-- Icono --}}
    <div class="w-24 h-24 rounded-[16px] flex items-center justify-center mb-3
                shadow-md"
         style="background:rgba(82,171,177,0.12); border:1px solid rgba(82,171,177,0.25);">
        {{-- Icono claro --}}
    <img src="{{ asset('images/logos/icon_logo.png') }}"
         class="block dark:hidden " alt="">

    {{-- Icono oscuro --}}
    <img src="{{ asset('images/logos/icon_logo_blanco.png') }}"
         class="hidden dark:block " alt="">

    </div>

    {{-- Texto --}}
    {{-- Logo modo claro --}}
<img src="{{ asset('images/logos/medicalia_color.png') }}"
     alt="Logo claro"
     class="w-40 block dark:hidden">

{{-- Logo modo oscuro --}}
<img src="{{ asset('images/logos/medicalia_blanco.png') }}"
     alt="Logo oscuro"
     class="w-40 hidden dark:block">

</div>
                {{-- Progress dots decorativos --}}
                <div class="flex items-center gap-[6px] mb-7">
                    <div class="med-progress-dot active"></div>
                    <div class="med-progress-dot"></div>
                    <div class="med-progress-dot"></div>
                </div>

                {{-- ★ SLOT: login, register, forgot-password, reset-password, etc. --}}
                {{ $slot }}

            </div>
        </div>

        {{-- ── Footer del panel derecho:
             Logo de tu empresa + copyright
             REEMPLAZA "TuEmpresa S.A." con el nombre real.
             Si tienes un logo, agrega <img> dentro del div .dev-icon
        ── --}}
        <div class="flex items-center justify-between px-7 py-4 flex-shrink-0
                    border-t border-gray-100 dark:border-white/[0.05]">

            {{-- Logo empresa desarrolladora --}}
            <div class="flex items-center gap-2">
                
                <span class="text-[11px] text-gray-400 dark:text-gray-600">

    {{-- Logo claro --}}
    <img src="{{ asset('images/logos/altecbol_logo_color.png') }}"
         class="w-32 h-14 block dark:hidden" alt="Logo claro">

    {{-- Logo oscuro --}}
    <img src="{{ asset('images/logos/altecbol_logo_blanco.png') }}"
         class="w-32 h-14 hidden dark:block" alt="Logo oscuro">

</span>
            </div>

            <span class="text-[11px] text-gray-300 dark:text-gray-600">
                Medicalia &copy; {{ date('Y') }}
            </span>
        </div>
    </main>

</div>

</body>
</html>