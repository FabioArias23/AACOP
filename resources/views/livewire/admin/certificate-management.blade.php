<div class="space-y-6 p-4 sm:p-6 lg:p-8">
    <!-- Encabezado -->
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Gestión de Certificados</h2>
            <p class="text-gray-500">Emisión y control de certificados académicos.</p>
        </div>
    </div>

    <!-- Mensaje de éxito -->
    @if (session('success'))
        <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded-r-lg mb-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-green-700">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif

    <div class="grid lg:grid-cols-3 gap-6">

        <!-- COLUMNA IZQUIERDA: Pendientes de Emisión -->
        <div class="space-y-6">
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="p-4 bg-amber-50 border-b border-amber-100">
                    <h3 class="font-semibold text-amber-800 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v4"/><path d="m16.2 7.8 2.9-2.9"/><path d="M18 12h4"/><path d="m16.2 16.2 2.9 2.9"/><path d="M12 18v4"/><path d="m4.9 19.1 2.9-2.9"/><path d="M2 12h4"/><path d="m4.9 4.9 2.9 2.9"/></svg>
                        Pendientes de Emisión
                    </h3>
                </div>

                <div class="divide-y divide-gray-100">
                    @forelse($pendingEnrollments as $enrollment)
                        <div class="p-4 hover:bg-gray-50 transition-colors">
                            <div class="flex justify-between items-start mb-2">
                                <span class="font-bold text-gray-900">{{ $enrollment->user->name }}</span>
                                <!-- CORRECCIÓN 1: Sin porcentaje en la lista -->
                                <span class="bg-green-100 text-green-800 text-xs font-medium px-2 py-0.5 rounded">Nota: {{ $enrollment->grade }}</span>
                            </div>
                            <p class="text-sm text-gray-600 mb-3">{{ $enrollment->trainingSession->training_title }}</p>

                            <button
                                wire:click="generateCertificate({{ $enrollment->id }})"
                                wire:confirm="¿Generar certificado para {{ $enrollment->user->name }}?"
                                class="w-full inline-flex items-center justify-center gap-2 rounded-lg border border-blue-200 bg-blue-50 px-3 py-1.5 text-sm font-medium text-blue-700 hover:bg-blue-100 transition-colors"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9H4.5a2.5 2.5 0 0 1 0-5H6"/><path d="M18 9h1.5a2.5 2.5 0 0 0 0-5H18"/><path d="M4 22h16"/><path d="M10 14.66V17c0 .55-.47.98-.97 1.21C7.85 18.75 7 20.24 7 22"/><path d="M14 14.66V17c0 .55.47.98.97 1.21C16.15 18.75 17 20.24 17 22"/><path d="M18 2H6v7a6 6 0 0 0 12 0V2Z"/></svg>
                                Generar
                            </button>
                        </div>
                    @empty
                        <div class="p-8 text-center text-gray-500">
                            <p>No hay certificados pendientes.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- COLUMNA DERECHA: Historial de Certificados -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="p-4 border-b border-gray-100 flex flex-col sm:flex-row justify-between items-center gap-4">
                    <h3 class="font-semibold text-gray-900">Historial de Certificados</h3>

                    <div class="relative w-full sm:w-64">
                        <input
                            wire:model.live.debounce.300ms="searchTerm"
                            type="text"
                            placeholder="Buscar certificado..."
                            class="w-full rounded-lg border-gray-300 text-sm pl-9 focus:border-blue-500 focus:ring-blue-500"
                        >
                        <svg xmlns="http://www.w3.org/2000/svg" class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-gray-50 text-gray-700 font-medium">
                            <tr>
                                <th class="px-4 py-3">N° Certificado</th>
                                <th class="px-4 py-3">Estudiante</th>
                                <th class="px-4 py-3">Capacitación</th>
                                <th class="px-4 py-3">Fecha</th>
                                <th class="px-4 py-3 text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($certificates as $cert)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 font-mono text-gray-600">{{ $cert->certificate_number }}</td>
                                    <td class="px-4 py-3 font-medium text-gray-900">{{ $cert->student_name }}</td>
                                    <td class="px-4 py-3 text-gray-600">{{ $cert->training_title }}</td>
                                    <td class="px-4 py-3 text-gray-500">{{ \Carbon\Carbon::parse($cert->completion_date)->format('d/m/Y') }}</td>
                                    <td class="px-4 py-3 text-right">
                                        <button
                                            wire:click="showPreview({{ $cert->id }})"
                                            class="text-blue-600 hover:text-blue-800 font-medium inline-flex items-center gap-1"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                                            Ver
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-8 text-center text-gray-500">
                                        No se encontraron certificados.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="p-4 border-t border-gray-100">
                    {{ $certificates->links() }}
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Vista Previa (Estilo Certificado) -->
    @if($previewDialogOpen && $previewCertificate)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4" wire:click="$set('previewDialogOpen', false)">
            <div class="bg-white w-full max-w-4xl shadow-2xl rounded-lg overflow-hidden" @click.stop>

                <!-- Toolbar -->
                <div class="bg-gray-100 px-4 py-3 border-b flex justify-between items-center">
                    <h3 class="font-semibold text-gray-700">Vista Previa</h3>
                    <div class="flex gap-2">
                        <button onclick="window.print()" class="px-3 py-1.5 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm font-medium flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9V2h12v7"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><path d="M6 14h12v8H6z"/></svg>
                            Imprimir
                        </button>
                        <button wire:click="$set('previewDialogOpen', false)" class="px-3 py-1.5 bg-white border text-gray-700 rounded hover:bg-gray-50 text-sm font-medium">
                            Cerrar
                        </button>
                    </div>
                </div>

                <!-- Diseño del Certificado (Para pantalla e impresión) -->
                <div class="p-10 bg-white text-center border-[20px] border-double border-gray-100 m-4 print:border-0 print:m-0 print:p-0">
                    <div class="py-10 px-6 border-4 border-blue-900/20 h-full flex flex-col items-center justify-center">

                        <!-- Logo o Título -->
                        <div class="mb-8">
                            <h1 class="text-5xl font-serif text-blue-900 font-bold tracking-wider mb-2">CERTIFICADO</h1>
                            <span class="text-blue-600 tracking-[0.3em] text-sm uppercase">De Aprobación</span>
                        </div>

                        <p class="text-gray-500 italic text-lg mb-6">Este documento certifica que</p>

                        <h2 class="text-4xl font-bold text-gray-800 border-b-2 border-gray-300 pb-2 px-10 mb-8 inline-block min-w-[400px]">
                            {{ $previewCertificate->student_name }}
                        </h2>

                        <p class="text-gray-600 text-lg mb-6 max-w-2xl mx-auto">
                            Ha completado satisfactoriamente el curso de capacitación profesional:
                        </p>

                        <h3 class="text-3xl font-bold text-blue-800 mb-10">
                            {{ $previewCertificate->training_title }}
                        </h3>

                        <div class="flex justify-between w-full max-w-3xl mt-8 px-10">
                            <div class="text-center">
                                <!-- CORRECCIÓN 2: Sin porcentaje en la vista previa/impresión -->
                                <p class="font-bold text-gray-900 text-lg">{{ $previewCertificate->grade }}</p>
                                <div class="h-px w-32 bg-gray-400 my-2"></div>
                                <p class="text-xs text-gray-500 uppercase">Calificación Final</p>
                            </div>

                            <div class="text-center">
                                <p class="font-bold text-gray-900 text-lg">{{ \Carbon\Carbon::parse($previewCertificate->completion_date)->translatedFormat('d \d\e F \d\e Y') }}</p>
                                <div class="h-px w-32 bg-gray-400 my-2"></div>
                                <p class="text-xs text-gray-500 uppercase">Fecha de Emisión</p>
                            </div>

                            <div class="text-center">
                                <p class="font-bold text-gray-900 text-lg font-mono">{{ $previewCertificate->certificate_number }}</p>
                                <div class="h-px w-32 bg-gray-400 my-2"></div>
                                <p class="text-xs text-gray-500 uppercase">Código de Verificación</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Estilos para impresión -->
        <style>
            @media print {
                body * { visibility: hidden; }
                .fixed { position: absolute; inset: 0; background: white; padding: 0; }
                .bg-white.w-full.max-w-4xl { box-shadow: none; width: 100%; max-width: none; }
                .border-\[20px\] { border: none !important; margin: 0 !important; }
                .bg-gray-100 { display: none; } /* Ocultar toolbar */
                .p-10 { padding: 0 !important; }
                #certificate-print, .bg-white * { visibility: visible; }
                .bg-white { position: absolute; left: 0; top: 0; width: 100%; height: 100%; }
            }
        </style>
    @endif
</div>
