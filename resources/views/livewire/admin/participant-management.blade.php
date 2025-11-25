<div class="space-y-6 font-sans text-slate-900">

    <!-- Header y Botón Exportar -->
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
        <div>
            <h2 class="text-2xl font-bold tracking-tight">Gestión de Participantes</h2>
            <p class="text-slate-500 mt-1">Administra los estudiantes inscritos en las capacitaciones</p>
        </div>

    </div>

    <!-- Buscador -->
    <div class="relative">
        <x-lucide-search class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" />
        <input
            wire:model.live.debounce.300ms="searchTerm"
            type="text"
            placeholder="Buscar participantes..."
            class="flex h-11 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 pl-10 text-sm ring-offset-white file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-slate-500 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-600 focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
        >
    </div>

    <!-- Mensajes Flash -->
    @if (session('success'))
        <div class="bg-[#00A885]/10 border border-[#00A885]/20 text-[#00A885] p-4 rounded-xl flex items-center gap-2">
            <x-lucide-check-circle-2 class="w-4 h-4" />
            <span class="text-sm font-medium">{{ session('success') }}</span>
        </div>
    @endif

    <!-- Lista de Tarjetas -->
    <div class="grid gap-6">
        @forelse ($participants as $participant)
            <!-- Card Container -->
            <div class="rounded-2xl border-2 border-slate-100 bg-white text-slate-950 shadow-sm hover:shadow-lg transition-all duration-200 hover:border-blue-100">

                <!-- Card Header -->
                <div class="p-6 flex flex-col sm:flex-row sm:justify-between sm:items-start gap-4">
                    <div>
                        <h3 class="font-semibold leading-none tracking-tight text-lg flex items-center gap-2">
                            {{ $participant->name }}
                        </h3>
                        <p class="text-sm text-slate-500 mt-1.5">
                            {{ $participant->email }} • {{ $participant->department ?? 'General' }}
                        </p>
                    </div>

                    <button
                        wire:click="openEnrollDialog({{ $participant->id }})"
                        class="inline-flex items-center justify-center whitespace-nowrap rounded-xl text-sm font-medium ring-offset-white transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-slate-950 focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 bg-[#213D8F] text-white hover:bg-[#213D8F]/90 h-9 px-4 py-2"
                    >
                        <x-lucide-user-plus class="w-4 h-4 mr-2" />
                        Inscribir
                    </button>
                </div>

                <!-- Card Content (Enrollments) -->
                <div class="p-6 pt-0">
                    @if ($participant->enrollments->isNotEmpty())
                        <div class="space-y-3">
                            <h4 class="text-sm font-medium text-slate-900">Inscripciones ({{ $participant->enrollments->count() }})</h4>
                            <div class="grid gap-3">
                                @foreach ($participant->enrollments as $enrollment)
                                    @php
                                        $config = $this->getStatusConfig($enrollment->status);
                                    @endphp

                                    <!-- Enrollment Item -->
                                    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 p-4 bg-slate-50/50 rounded-xl border border-slate-100">
                                        <div class="flex-1">
                                            <p class="font-medium text-slate-900">{{ $enrollment->trainingSession->training_title }}</p>
                                            <p class="text-sm text-slate-500">
                                                {{ \Carbon\Carbon::parse($enrollment->trainingSession->date)->translatedFormat('d \\de F, Y') }}
                                            </p>
                                        </div>

                                        <div class="flex items-center gap-3 flex-wrap">
                                            <!-- Badge Estado Dinámico -->
                                            <span class="inline-flex items-center rounded-lg px-2.5 py-0.5 text-xs font-medium {{ $config['class'] }}">
                                                <x-dynamic-component :component="$config['icon']" class="w-3 h-3 mr-1.5" />
                                                {{ $enrollment->status }}
                                            </span>

                                            <!-- Nota (si existe) -->
                                            @if($enrollment->grade)
                                                <span class="inline-flex items-center rounded-lg bg-slate-100 px-2.5 py-0.5 text-xs font-medium text-slate-600">
                                                    Nota: {{ $enrollment->grade }}%
                                                </span>
                                            @endif

                                            <!-- Asistencia -->
                                            <span class="inline-flex items-center rounded-lg border border-slate-200 bg-white px-2.5 py-0.5 text-xs font-medium text-slate-600">
                                                Asistencia: {{ $enrollment->attendance }}%
                                            </span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <div class="text-center py-6 bg-slate-50 rounded-xl border border-dashed border-slate-200">
                            <p class="text-sm text-slate-500">Sin inscripciones activas</p>
                        </div>
                    @endif
                </div>
            </div>
        @empty
            <div class="text-center py-12">
                <x-lucide-users class="w-12 h-12 mx-auto text-slate-300 mb-4" />
                <h3 class="text-lg font-medium text-slate-900">No hay participantes</h3>
                <p class="text-slate-500">No se encontraron resultados para tu búsqueda.</p>
            </div>
        @endforelse
    </div>

    <!-- Modal de Inscripción -->
    @if($dialogOpen)
    <div
        class="fixed inset-0 z-50 bg-black/40 backdrop-blur-sm flex items-center justify-center p-4"
        x-data
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
    >
        <!-- Overlay click close -->
        <div class="absolute inset-0" @click="$wire.set('dialogOpen', false)"></div>

        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md border border-slate-200 p-6" @click.stop>
            <div class="flex flex-col space-y-1.5 mb-6">
                <h3 class="font-semibold text-xl leading-none tracking-tight">Nueva Inscripción</h3>
                <p class="text-sm text-slate-500">
                    Inscribe a <span class="font-bold text-slate-800">{{ $selectedParticipant?->name }}</span> en una sesión.
                </p>
            </div>

            <form wire:submit="enroll" class="space-y-4">
                <div class="space-y-2">
                    <label class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70">
                        Sesión de Capacitación
                    </label>
                    <select
                        wire:model="trainingSessionId"
                        class="flex h-10 w-full items-center justify-between rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm placeholder:text-slate-500 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                    >
                        <option value="">Selecciona una sesión...</option>
                        @foreach($availableSessions as $session)
                            <option value="{{ $session->id }}">
                                {{ $session->training_title }} - {{ \Carbon\Carbon::parse($session->date)->format('d/m/Y') }}
                            </option>
                        @endforeach
                    </select>
                    @error('trainingSessionId') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <div class="flex justify-end gap-3 pt-4">
                    <button
                        type="button"
                        @click="$wire.set('dialogOpen', false)"
                        class="inline-flex items-center justify-center rounded-xl text-sm font-medium transition-colors border border-slate-200 bg-transparent hover:bg-slate-100 h-10 px-4 py-2"
                    >
                        Cancelar
                    </button>
                    <button
                        type="submit"
                        class="inline-flex items-center justify-center rounded-xl text-sm font-medium transition-colors bg-[#213D8F] text-white hover:bg-[#213D8F]/90 h-10 px-4 py-2"
                    >
                        Inscribir
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>
