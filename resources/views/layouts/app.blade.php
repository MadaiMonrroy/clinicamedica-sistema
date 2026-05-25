<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }}</title>
<link rel="icon" type="image/png" href="{{ asset('images/logos/logo_color.png') }}">
<link rel="apple-touch-icon" href="{{ asset('images/logos/logo_color.png') }}">
    {{-- Anti-flash de tema --}}
    <script>
        (function () {
            const s = localStorage.getItem('theme');
            const d = window.matchMedia('(prefers-color-scheme: dark)').matches;
            document.documentElement.classList.toggle('dark', s === 'dark' || (!s && d));
        })();
         document.addEventListener('DOMContentLoaded', () => {
        const details = document.querySelectorAll('#logo-sidebar details');
        details.forEach(detail => {
            detail.addEventListener('toggle', () => {
                if (detail.open) {
                    details.forEach(other => {
                        if (other !== detail) other.removeAttribute('open');
                    });
                }
            });
        });
    });
    </script>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet"/>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>

<body class="font-sans antialiased bg-gray-100 dark:bg-gray-900">

<x-toast />

<div class="min-h-screen">
    {{-- Sin data-turbo-permanent, sin ids especiales --}}
    @include('layouts.navigation')
    <livewire:sidebar />

    <div class="sm:ml-64 pt-16">
        @isset($header)
            <div class="px-4 pt-6 pb-3 sm:px-6 lg:px-8">
                <div class="rounded-2xl border border-gray-200 dark:border-gray-700
                            bg-white dark:bg-gray-800 shadow-sm px-5 py-4">
                    {{ $header }}
                </div>
            </div>
        @endisset

        <main class="px-4 pb-10 pt-7 sm:px-6 lg:px-8">
            {{ $slot }}
        </main>
    </div>
</div>

@if ($errors->any())
<script>
    document.addEventListener('DOMContentLoaded', () => {
        @foreach ($errors->all() as $error)
            showToast({ title: 'Error', message: @js($error), type: 'error' });
        @endforeach

        {{-- Reabrir el modal si había un error de staff --}}
        @if (session('reopen_staff_modal'))
            openStaffCreateModal();
        @endif
    });
</script>
@endif
@stack('scripts')
@livewireScripts
</body>
</html>