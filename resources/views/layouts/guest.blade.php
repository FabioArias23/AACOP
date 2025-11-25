<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased bg-gray-50 dark:bg-gray-950 relative">

        <!-- Fondo decorativo (Patrón sutil) -->
        <div class="absolute inset-0 -z-10 h-full w-full bg-white dark:bg-gray-950 bg-[linear-gradient(to_right,#8080800a_1px,transparent_1px),linear-gradient(to_bottom,#8080800a_1px,transparent_1px)] bg-[size:14px_24px]">
            <div class="absolute left-0 right-0 top-0 -z-10 m-auto h-[310px] w-[310px] rounded-full bg-blue-400 opacity-20 blur-[100px]"></div>
        </div>

        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 px-4">
            <!-- Logo con animación y título -->
            <div class="mb-8 text-center transition-transform hover:scale-105 duration-300">
                <a href="/" class="flex flex-col items-center justify-center gap-3">
                    <!-- Contenedor del logo -->
                    <div class="bg-white dark:bg-gray-800 p-3 rounded-xl shadow-md ring-1 ring-gray-100 dark:ring-gray-700">
                        <img
                            src="{{ asset('images/logo-formosa.png') }}"
                            alt="AACOP Aniversario"
                            class="w-20 h-auto object-contain"
                        >
                    </div>
                    <span class="text-2xl font-bold tracking-tight text-gray-800 dark:text-gray-100">Sistema de Gestión de Capacitaciones</span>
                </a>
            </div>

            <!-- Tarjeta del Formulario -->
            <div class="w-full sm:max-w-md bg-white dark:bg-gray-900 shadow-2xl rounded-2xl overflow-hidden border border-gray-100 dark:border-gray-800 relative">

                <!-- Línea de color superior -->
                <div class="absolute top-0 left-0 w-full h-1.5 bg-gradient-to-r from-blue-600 to-teal-400"></div>

                <div class="px-8 py-10">
                    {{ $slot }}
                </div>
            </div>

            <!-- Footer simple -->
            <div class="mt-8 text-center text-xs text-gray-400 dark:text-gray-600">
                &copy; {{ date('Y') }} AACOP. Sistema de Gestión.
            </div>
        </div>
    </body>
</html>
