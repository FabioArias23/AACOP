<div
    class="max-w-7xl mx-auto space-y-6"
    x-data="{ openModal: @entangle('openModal') }"
>
    {{-- Encabezado --}}
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-semibold text-slate-900 dark:text-slate-100">
                Docentes
            </h2>
            <p class="text-sm text-slate-500 dark:text-slate-400">
                Administra los docentes habilitados para dictar capacitaciones.
            </p>
        </div>

        <button
            type="button"
            @click="openModal = true"
            class="inline-flex items-center px-4 py-2 rounded-md bg-blue-600 text-white text-sm font-medium hover:bg-blue-700"
        >
            + Nuevo docente
        </button>
    </div>

    {{-- Mensaje de éxito --}}
    @if (session()->has('success'))
        <div class="rounded-md border border-emerald-400 bg-emerald-50 text-emerald-800 dark:border-emerald-500 dark:bg-emerald-900/40 dark:text-emerald-200 px-4 py-2 text-sm">
            {{ session('success') }}
        </div>
    @endif

    {{-- Listado de docentes --}}
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl overflow-hidden">
        <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">
            <thead class="bg-slate-50 dark:bg-slate-800/80">
                <tr>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase">
                        Nombre
                    </th>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase">
                        Email
                    </th>
                    <th class="px-4 py-2 text-right text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase">
                        Acciones
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                @forelse($docentes as $docente)
                    <tr class="bg-white dark:bg-slate-900 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                        <td class="px-4 py-2 text-sm text-slate-900 dark:text-slate-100">
                            {{ $docente->name }}
                        </td>
                        <td class="px-4 py-2 text-sm text-slate-600 dark:text-slate-300">
                            {{ $docente->email }}
                        </td>
                        <td class="px-4 py-2 text-sm text-right space-x-2">
                            <button
                                wire:click="delete({{ $docente->id }})"
                                wire:confirm="¿Seguro que deseas eliminar este docente?"
                                class="text-red-600 hover:text-red-500 dark:text-red-400 dark:hover:text-red-300 text-xs font-medium transition-colors"
                            >
                                Eliminar
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr class="bg-white dark:bg-slate-900">
                        <td colspan="3" class="px-4 py-8 text-center text-sm text-slate-500 dark:text-slate-400">
                            No hay docentes registrados.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- MODAL: Nuevo docente --}}
    <div
        x-show="openModal"
        style="display: none;"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
    >
        <div
            @click.away="openModal = false"
            class="w-full max-w-lg rounded-xl shadow-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-slate-100 overflow-hidden"
            x-transition:enter="transition ease-out duration-200 transform"
            x-transition:enter-start="scale-95 translate-y-4"
            x-transition:enter-end="scale-100 translate-y-0"
            x-transition:leave="transition ease-in duration-150 transform"
            x-transition:leave-start="scale-100 translate-y-0"
            x-transition:leave-end="scale-95 translate-y-4"
        >
            {{-- Header modal --}}
            <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-700 flex items-center justify-between bg-slate-50 dark:bg-slate-800/50">
                <h3 class="text-lg font-semibold">
                    Nuevo docente
                </h3>
                <button
                    type="button"
                    @click="openModal = false"
                    class="text-slate-400 hover:text-slate-600 dark:text-slate-500 dark:hover:text-slate-300 transition-colors"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                </button>
            </div>

            {{-- Body modal --}}
            <div class="px-6 py-6">
                <!-- AQUÍ ESTÁ EL CAMBIO CLAVE: wire:submit -->
                <form wire:submit="save" class="space-y-5">

                    <div class="grid md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-sm font-medium mb-1.5 text-slate-700 dark:text-slate-300">
                                Nombre completo
                            </label>
                            <input
                                type="text"
                                wire:model="name"
                                class="w-full rounded-lg bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-700 px-3 py-2 text-sm text-slate-900 dark:text-slate-100 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                                placeholder="Ej: Juan Pérez"
                            >
                            @error('name') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-1.5 text-slate-700 dark:text-slate-300">
                                Correo electrónico
                            </label>
                            <input
                                type="email"
                                wire:model="email"
                                class="w-full rounded-lg bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-700 px-3 py-2 text-sm text-slate-900 dark:text-slate-100 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                                placeholder="juan@ejemplo.com"
                            >
                            @error('email') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="grid md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-sm font-medium mb-1.5 text-slate-700 dark:text-slate-300">
                                Contraseña
                            </label>
                            <input
                                type="password"
                                wire:model="password"
                                class="w-full rounded-lg bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-700 px-3 py-2 text-sm text-slate-900 dark:text-slate-100 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                            >
                            @error('password') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-1.5 text-slate-700 dark:text-slate-300">
                                Confirmar contraseña
                            </label>
                            <input
                                type="password"
                                wire:model="password_confirmation"
                                class="w-full rounded-lg bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-700 px-3 py-2 text-sm text-slate-900 dark:text-slate-100 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                            >
                        </div>
                    </div>

                    {{-- Footer modal --}}
                    <div class="flex justify-end gap-3 pt-4 border-t border-slate-100 dark:border-slate-800">
                        <button
                            type="button"
                            @click="openModal = false"
                            class="px-4 py-2 rounded-lg bg-white border border-slate-300 text-slate-700 text-sm font-medium hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-600 dark:hover:bg-slate-700 transition-colors"
                        >
                            Cancelar
                        </button>

                        <button
                            type="submit"
                            class="px-4 py-2 rounded-lg bg-blue-600 text-white text-sm font-medium hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 shadow-sm transition-colors flex items-center gap-2"
                            wire:loading.attr="disabled"
                        >
                            <span wire:loading.remove>Guardar docente</span>
                            <span wire:loading>Guardando...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
