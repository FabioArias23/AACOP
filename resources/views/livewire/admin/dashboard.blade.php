<div
    class="min-h-screen bg-gray-50 dark:bg-gray-900 flex transition-colors duration-300"
    x-data="{
        sidebarOpen: true,
        mobileMenuOpen: false,
        darkMode: localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches),
        toggleTheme() {
            this.darkMode = !this.darkMode;
            localStorage.setItem('theme', this.darkMode ? 'dark' : 'light');
            if (this.darkMode) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        },
        init() {
            if (this.darkMode) document.documentElement.classList.add('dark');
            else document.documentElement.classList.remove('dark');
        }
    }"
    x-init="init()"
>
    <!-- 1. SIDEBAR DESKTOP (Barra Lateral Oscura) -->
    <aside class="hidden md:flex flex-col bg-slate-900 border-r border-slate-800 transition-all duration-300"
           :class="sidebarOpen ? 'w-64' : 'w-20'">

        <div class="p-4 border-b border-slate-800 flex items-center justify-center h-16 bg-slate-900">
            <!-- ESTADO ABIERTO -->
            <div x-show="sidebarOpen" class="transition-opacity duration-300">
                <img src="{{ asset('images/logo-formosa.png') }}" alt="AACOP Formosa" class="h-10 w-auto object-contain">
            </div>
            <!-- ESTADO CERRADO -->
            <div x-show="!sidebarOpen" style="display: none;" class="transition-opacity duration-300">
                <img src="{{ asset('images/logo-formosa.png') }}" alt="AACOP" class="h-8 w-8 object-contain">
            </div>
        </div>

        <nav class="flex-1 p-3 space-y-1 overflow-y-auto">
            @foreach ($navItems as $item)
                <button
                    wire:click="changeSection('{{ $item['id'] }}')"
                    class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl transition-colors group
                        {{ $activeSection === $item['id']
                            ? 'bg-blue-600 text-white shadow-md'
                            : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}"
                >
                    <x-dynamic-component :component="'lucide-'.$item['icon']" class="w-5 h-5" />
                    <span x-show="sidebarOpen" class="text-sm font-medium">{{ $item['label'] }}</span>
                </button>
            @endforeach
        </nav>

        <div class="p-3 border-t border-slate-800">
            <button @click="sidebarOpen = !sidebarOpen" class="w-full flex items-center justify-center gap-3 px-3 py-2.5 rounded-xl text-slate-400 hover:text-white hover:bg-slate-800 transition-colors">
                <x-lucide-panel-left-close class="w-5 h-5" x-show="sidebarOpen" />
                <x-lucide-panel-left-open class="w-5 h-5" x-show="!sidebarOpen" />
            </button>
        </div>
    </aside>

    <!-- 🔥 ESTE ES EL BLOQUE QUE FALTABA (MENÚ MÓVIL) 🔥 -->
    <div x-show="mobileMenuOpen"
         @click="mobileMenuOpen = false"
         class="md:hidden fixed inset-0 bg-black/50 z-50 transition-opacity"
         style="display: none;"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
    >
        <aside class="w-64 bg-slate-900 h-full shadow-xl transform transition-transform"
               @click.stop
               x-transition:enter="transition ease-out duration-200"
               x-transition:enter-start="-translate-x-full"
               x-transition:enter-end="translate-x-0"
               x-transition:leave="transition ease-in duration-150"
               x-transition:leave-start="translate-x-0"
               x-transition:leave-end="-translate-x-full"
        >
            <div class="p-4 border-b border-slate-800 flex justify-center items-center h-16">
                 <img src="{{ asset('images/logo-formosa.png') }}" class="h-10 w-auto object-contain">
            </div>

            <nav class="flex-1 p-3 space-y-1 overflow-y-auto">
                @foreach ($navItems as $item)
                    <button
                        wire:click="changeSection('{{ $item['id'] }}'); mobileMenuOpen = false"
                        class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl transition-colors
                            {{ $activeSection === $item['id'] ? 'bg-blue-600 text-white' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}"
                    >
                        <x-dynamic-component :component="'lucide-'.$item['icon']" class="w-5 h-5" />
                        <span class="text-sm font-medium">{{ $item['label'] }}</span>
                    </button>
                @endforeach
            </nav>
        </aside>
    </div>
    <!-- FIN DEL BLOQUE QUE FALTABA -->

    <!-- Contenido Principal -->
    <div class="flex-1 flex flex-col min-h-screen overflow-hidden relative">

        <!-- Header -->
        <header class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 sticky top-0 z-30 h-16 transition-colors duration-300">
            <div class="px-6 h-full flex items-center justify-between">

                <!-- Títulos -->
                <div class="flex items-center gap-4">
                    <button @click="mobileMenuOpen = !mobileMenuOpen" class="md:hidden text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-white p-2 rounded-md focus:bg-gray-100 dark:focus:bg-gray-700">
                        <x-lucide-menu class="w-6 h-6" />
                    </button>

                    <div class="flex flex-col justify-center">
                        <h1 class="text-lg font-bold text-gray-900 dark:text-white leading-tight">
                            Panel de Administración
                        </h1>
                        <p class="text-xs text-gray-500 dark:text-gray-400 hidden sm:block">
                            Sistema de Gestión de Capacitaciones AACOP
                        </p>
                    </div>
                </div>

                <!-- Herramientas Derecha -->
                <div class="flex items-center gap-4 sm:gap-6">
                    <button
                        @click="toggleTheme()"
                        class="p-2 rounded-lg transition-colors focus:outline-none hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-500 dark:text-yellow-400"
                    >
                        <svg x-show="darkMode" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="4"/><path d="M12 2v2"/><path d="M12 20v2"/><path d="m4.93 4.93 1.41 1.41"/><path d="m17.66 17.66 1.41 1.41"/><path d="M2 12h2"/><path d="M20 12h2"/><path d="m6.34 17.66-1.41-1.41"/><path d="m19.07 4.93-1.41 1.41"/></svg>
                        <svg x-show="!darkMode" style="display: none;" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3a6 6 0 0 0 9 9 9 9 0 1 1-9-9Z"/></svg>
                    </button>

                    <div class="h-6 w-px bg-gray-200 dark:bg-gray-700 hidden sm:block"></div>

                    <div class="flex items-center gap-3">
                        <div class="hidden sm:block text-right">
                            <p class="text-sm font-bold text-gray-900 dark:text-white leading-none">{{ Auth::user()->name }}</p>
                            <p class="text-[10px] text-gray-500 dark:text-gray-400 uppercase mt-1">Administrador</p>
                        </div>

                        <div class="h-9 w-9 rounded-full bg-blue-800 flex items-center justify-center text-white font-bold text-sm shadow-sm border-2 border-transparent dark:border-gray-600">
                            {{ substr(Auth::user()->name, 0, 1) }}
                        </div>

                        <button
                            wire:click="logout"
                            class="ml-2 p-2 text-gray-500 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors dark:text-gray-400 dark:hover:bg-red-900/30 dark:hover:text-red-400"
                        >
                            <x-lucide-log-out class="w-5 h-5" />
                        </button>
                    </div>
                </div>
            </div>
        </header>

        <!-- Área de Contenido -->
        <main class="flex-1 overflow-y-auto bg-gray-50 dark:bg-gray-900 p-4 sm:p-6 lg:p-8 text-gray-900 dark:text-gray-100">
            @switch($activeSection)
                @case('dashboard') <livewire:admin.dashboard-overview :key="'dash-'.time()" /> @break
                @case('catalog') <livewire:admin.training-catalog :key="'cat-'.time()" /> @break
                @case('campus') <livewire:admin.campus-management :key="'cam-'.time()" /> @break
                @case('schedule') <livewire:admin.training-schedule :key="'sch-'.time()" /> @break
                @case('participants') <livewire:admin.participant-management :key="'part-'.time()" /> @break
                @case('attendance') <livewire:admin.attendance-management :key="'att-'.time()" /> @break
                @case('certificates') <livewire:admin.certificate-management :key="'cert-'.time()" /> @break
                @case('reports') <livewire:admin.training-reports :key="'rep-'.time()" /> @break
                @default <livewire:admin.dashboard-overview :key="'def-'.time()" />
            @endswitch
        </main>
    </div>
</div>
