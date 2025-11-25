<div class="space-y-6 p-4 sm:p-6 lg:p-8">

    <!-- Encabezado -->
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Calendario de Capacitaciones</h2>
            <p class="text-gray-500">Gestiona la programación de las sesiones académicas.</p>
        </div>
        <button wire:click="create" class="inline-flex items-center justify-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
            <!-- Icono Plus -->
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="M12 5v14"/></svg>
            Programar Sesión
        </button>
    </div>

    <!-- Mensaje de Éxito -->
    @if (session('success'))
        <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded-r-lg">
            <div class="flex">
                <div class="flex-shrink-0">
                    <!-- Icono Check -->
                    <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-green-700">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif

    <div class="grid lg:grid-cols-3 gap-6">

        <!-- COLUMNA IZQUIERDA: Calendario y Lista del día -->
        <div class="lg:col-span-2 space-y-6">

            <!-- Selector de Fecha -->
            <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm flex items-center gap-4">
                <div class="bg-blue-50 p-3 rounded-full text-blue-600">
                    <!-- Icono Calendar -->
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/></svg>
                </div>
                <div class="flex-1">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Ver agenda del día:</label>
                    <input type="date" wire:model.live="selectedDate" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                </div>
            </div>

            <!-- Lista de Sesiones del Día -->
            <div class="space-y-4">
                <h3 class="font-semibold text-gray-800 flex items-center gap-2">
                    Agenda: {{ \Carbon\Carbon::parse($selectedDate)->translatedFormat('l d \d\e F, Y') }}
                </h3>

                @forelse($sessionsOnDate as $session)
                    <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-sm hover:shadow-md transition-shadow flex flex-col sm:flex-row gap-4 items-start sm:items-center">
                        <!-- Hora -->
                        <div class="bg-blue-50 text-blue-700 rounded-lg p-3 flex flex-col items-center justify-center min-w-[90px]">
                            <span class="text-lg font-bold leading-none">{{ \Carbon\Carbon::parse($session->start_time)->format('H:i') }}</span>
                            <span class="text-xs text-blue-400 my-1">a</span>
                            <span class="text-sm font-medium leading-none">{{ \Carbon\Carbon::parse($session->end_time)->format('H:i') }}</span>
                        </div>

                        <!-- Info -->
                        <div class="flex-1">
                            <div class="flex justify-between items-start">
                                <h4 class="text-lg font-bold text-gray-900">{{ $session->training_title }}</h4>
                                <span class="px-2 py-1 text-xs font-semibold rounded-full
                                    {{ $session->status === 'Programada' ? 'bg-blue-100 text-blue-800' :
                                      ($session->status === 'Completada' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800') }}">
                                    {{ $session->status }}
                                </span>
                            </div>

                            <div class="mt-2 flex flex-wrap gap-4 text-sm text-gray-500">
                                <div class="flex items-center gap-1">
                                    <!-- Icono MapPin -->
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
                                    {{ $session->campus_name }}
                                </div>
                                <div class="flex items-center gap-1">
                                    <!-- Icono User -->
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                                    {{ $session->instructor }}
                                </div>
                                <div class="flex items-center gap-1">
                                    <!-- Icono Users -->
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                                    {{ $session->registered }} / {{ $session->capacity }} inscritos
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-10 bg-white border border-dashed border-gray-300 rounded-xl">
                        <p class="text-gray-500">No hay capacitaciones programadas para esta fecha.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- COLUMNA DERECHA: Próximas Sesiones -->
        <div class="space-y-6">
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
                <div class="p-4 border-b border-gray-100 bg-gray-50">
                    <h3 class="font-semibold text-gray-900">Próximas Actividades</h3>
                </div>
                <div class="divide-y divide-gray-100">
                    @forelse($upcomingSessions as $upcoming)
                        <div class="p-4 hover:bg-gray-50 transition-colors">
                            <div class="text-xs font-bold text-blue-600 mb-1 flex items-center gap-1">
                                <!-- Icono Calendar pequeño -->
                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/></svg>
                                {{ \Carbon\Carbon::parse($upcoming->date)->format('d/m/Y') }}
                                •
                                {{ \Carbon\Carbon::parse($upcoming->start_time)->format('H:i') }}
                            </div>
                            <h5 class="font-medium text-gray-900 text-sm">{{ $upcoming->training_title }}</h5>
                            <p class="text-xs text-gray-500 mt-1">{{ $upcoming->campus_name }}</p>
                        </div>
                    @empty
                         <div class="p-4 text-center text-sm text-gray-500">
                            No hay actividades futuras.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL DE CREACIÓN -->
    @if($dialogOpen)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm"
             wire:click="$set('dialogOpen', false)">

            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden"
                 @click.stop>

                <!-- Header del Modal -->
                <div class="bg-gray-50 px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                    <h3 class="text-lg font-bold text-gray-900">Programar Nueva Sesión</h3>
                    <button wire:click="$set('dialogOpen', false)" class="text-gray-400 hover:text-gray-600">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                    </button>
                </div>

                <form wire:submit.prevent="save" class="p-6 space-y-4">
                    <!-- Selección de Capacitación -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Capacitación *</label>
                        <select wire:model.live="training_id" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Seleccionar capacitación...</option>
                            @foreach($trainings as $t)
                                <option value="{{ $t->id }}">{{ $t->title }} ({{ $t->level }})</option>
                            @endforeach
                        </select>
                        @error('training_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <!-- Selección de Sede -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Sede *</label>
                        <select wire:model="campus_id" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Seleccionar sede...</option>
                            @foreach($campuses as $c)
                                <option value="{{ $c->id }}">{{ $c->name }} - {{ $c->city }}</option>
                            @endforeach
                        </select>
                        @error('campus_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <!-- Fecha y Horarios -->
                    <div class="grid grid-cols-2 gap-4">
                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Fecha *</label>
                            <input type="date" wire:model="date" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @error('date') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Hora Inicio *</label>
                            <input type="time" wire:model="start_time" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @error('start_time') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Hora Fin *</label>
                            <input type="time" wire:model="end_time" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @error('end_time') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <!-- Instructor y Capacidad -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Instructor *</label>
                            <input type="text" wire:model="instructor" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @error('instructor') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Capacidad *</label>
                            <input type="number" wire:model="capacity" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @error('capacity') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <!-- Footer del Modal -->
                    <div class="flex justify-end gap-3 pt-4 mt-4 border-t border-gray-100">
                        <button type="button" wire:click="$set('dialogOpen', false)" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Cancelar
                        </button>
                        <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Confirmar Sesión
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
