<div class="space-y-6">
    <!-- Encabezado -->
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
        <div>
            <h2 class="font-heading text-2xl font-semibold text-foreground">Gestión de Sedes</h2>
            <p class="text-muted-foreground">Administra las ubicaciones donde se imparten las capacitaciones</p>
        </div>

        <button wire:click="create" class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-xl text-sm font-medium bg-primary text-primary-foreground hover:bg-primary/90 h-9 px-4 py-2">
            <x-lucide-plus class="w-4 h-4" />
            <span>Nueva Sede</span>
        </button>
    </div>

    <!-- Mensaje de éxito -->
    @if (session()->has('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-md" role="alert">
            <p>{{ session('success') }}</p>
        </div>
    @endif

    <!-- Búsqueda -->
    <div class="relative">
        <x-lucide-search class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-muted-foreground" />
        <input wire:model.live.debounce.300ms="searchTerm" placeholder="Buscar sedes..." class="border-input flex h-9 w-full rounded-xl border bg-transparent px-3 py-1 text-sm pl-10">
    </div>

    <!-- Grid de Sedes -->
    <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
        @forelse ($campuses as $campus)
            <div class="bg-card text-card-foreground flex flex-col rounded-2xl border-2 hover:shadow-lg transition-all">
                <div class="p-6">
                    <div class="flex justify-between items-start">
                        <div class="space-y-1">
                            <h4 class="font-heading text-lg font-semibold flex items-center gap-2">
                                <x-lucide-building-2 class="w-5 h-5 text-primary" /> {{ $campus->name }}
                            </h4>
                            <p class="text-muted-foreground flex items-center gap-1 text-sm">
                                <x-lucide-map-pin class="w-3 h-3" /> {{ $campus->city }}
                            </p>
                        </div>
                        <span class="inline-flex items-center rounded-lg border px-2.5 py-0.5 text-xs font-semibold {{ $campus->status === 'Activo' ? 'bg-primary/10 text-primary' : 'bg-secondary text-secondary-foreground' }}">
                            {{ $campus->status }}
                        </span>
                    </div>
                </div>
                <div class="p-6 pt-0 space-y-4">
                    <div class="space-y-3">
                        <div class="flex items-start gap-2 text-sm text-muted-foreground">
                            <span class="font-medium">Dir:</span> <span>{{ $campus->address }}</span>
                        </div>
                        <div class="flex items-center gap-2 text-sm text-muted-foreground">
                            <x-lucide-users class="w-4 h-4" /><span>Capacidad: {{ $campus->capacity }}</span>
                        </div>
                    </div>
                    <div class="flex gap-2 pt-3 border-t border-border">
                        <!-- Aquí llamamos a edit() pasando el ID -->
                        <button wire:click="edit({{ $campus->id }})" class="inline-flex items-center justify-center gap-2 rounded-xl text-sm font-medium border bg-background h-8 px-3 flex-1 hover:bg-accent">
                            <x-lucide-edit class="w-4 h-4" /> Editar
                        </button>

                        <button wire:click="delete({{ $campus->id }})" wire:confirm="¿Estás seguro de eliminar esta sede?" class="inline-flex items-center justify-center rounded-xl text-sm font-medium border bg-background h-8 px-3 text-destructive hover:bg-destructive/10">
                            <x-lucide-trash-2 class="w-4 h-4" />
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center py-12 col-span-full">
                <x-lucide-building-2 class="w-12 h-12 mx-auto text-muted-foreground mb-4" />
                <p class="text-muted-foreground">No se encontraron sedes.</p>
            </div>
        @endforelse
    </div>

    {{-- Componente Hijo: Se encarga del Modal y el Formulario --}}
    <livewire:admin.campus-form />
</div>
