<div>
    <x-modal wire:model="trainingDialog">

        <!-- ENCABEZADO (Reemplazo de x-modal.header) -->
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">
                {{ $trainingForm['id'] ? 'Editar Capacitación' : 'Nueva Capacitación' }}
            </h3>
        </div>

        <!-- CUERPO (Reemplazo de x-modal.body) -->
        <div class="px-6 py-4 space-y-4">

            <div>
                <x-input-label value="Título" />
                <!-- Nota: Cambié 'titulo' por 'title' para coincidir con tu backend -->
                <x-text-input wire:model="trainingForm.title" type="text" class="w-full" />
                @error('trainingForm.title') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <div>
                <x-input-label value="Descripción" />
                <!-- Nota: Cambié 'descripcion' por 'description' para coincidir con tu backend -->
                <textarea wire:model="trainingForm.description" rows="3" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                @error('trainingForm.description') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

        </div>

        <!-- PIE (Reemplazo de x-modal.footer) -->
        <div class="px-6 py-4 bg-gray-50 flex justify-end gap-3">
            <x-secondary-button wire:click="$set('trainingDialog', false)">
                Cancelar
            </x-secondary-button>

            <x-primary-button wire:click="saveTraining">
                Guardar
            </x-primary-button>
        </div>

    </x-modal>
</div>
