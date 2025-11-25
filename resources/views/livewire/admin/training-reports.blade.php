<div class="space-y-6 p-4 sm:p-6 lg:p-8 bg-gray-50 min-h-screen">

    <!-- 1. Encabezado -->
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Reportes y Análisis</h2>
            <p class="text-gray-500">Visualiza métricas y estadísticas de capacitaciones</p>
        </div>

        <button
            wire:click="export"
            wire:loading.attr="disabled"
            class="inline-flex items-center justify-center gap-2 rounded-lg bg-blue-900 px-4 py-2 text-sm font-medium text-white hover:bg-blue-800 transition-colors shadow-sm disabled:opacity-50"
        >
            <span wire:loading.remove wire:target="export" class="flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" x2="12" y1="15" y2="3"/></svg>
                Exportar Reporte
            </span>
            <span wire:loading wire:target="export" class="flex items-center gap-2">
                <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                Generando...
            </span>
        </button>
    </div>

    <!-- 2. Tarjetas de Métricas -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm">
            <p class="text-sm font-medium text-gray-500">Total Participantes</p>
            <h3 class="text-3xl font-bold text-gray-900 mt-2">{{ $totalParticipants }}</h3>
            <div class="mt-4 flex items-center text-xs font-medium text-green-600">
                <span>12% vs mes anterior</span>
            </div>
        </div>
        <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm">
            <p class="text-sm font-medium text-gray-500">Capacitaciones</p>
            <h3 class="text-3xl font-bold text-blue-600 mt-2">{{ $totalTrainings }}</h3>
            <p class="mt-4 text-xs text-gray-500">8 programadas este mes</p>
        </div>
        <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm">
            <p class="text-sm font-medium text-gray-500">Tasa Finalización</p>
            <h3 class="text-3xl font-bold text-green-600 mt-2">{{ $completionRate }}%</h3>
            <div class="mt-4 flex items-center text-xs font-medium text-green-600">
                <span>5% vs mes anterior</span>
            </div>
        </div>
        <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm">
            <p class="text-sm font-medium text-gray-500">Nota Promedio</p>
            <h3 class="text-3xl font-bold text-gray-900 mt-2">4.6</h3>
            <p class="mt-4 text-xs text-gray-500">de 5.0 estrellas</p>
        </div>
    </div>

    <!-- 3. Fila Central: Gráficos -->
    <div class="grid lg:grid-cols-2 gap-6">

        <!-- Gráfico 1 -->
        <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm flex flex-col">
            <h3 class="font-bold text-gray-900 text-sm mb-6">Inscripciones vs Completadas</h3>
            <div class="relative w-full h-64" wire:ignore>
                <canvas id="chartEnrollments"></canvas>
            </div>
        </div>

        <!-- Gráfico 2 -->
        <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm flex flex-col">
            <h3 class="font-bold text-gray-900 text-sm mb-6">Capacitaciones por Categoría</h3>
            <div class="relative w-full h-64" wire:ignore>
                <canvas id="chartCategories"></canvas>
            </div>
        </div>
    </div>

    <!-- 4. Fila Inferior: Gráficos de Barras -->
    <div class="grid lg:grid-cols-2 gap-6">

        <!-- Gráfico 3 -->
        <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm flex flex-col">
            <h3 class="font-bold text-gray-900 text-sm mb-6">Participación por Departamento</h3>
            <div class="relative w-full h-64" wire:ignore>
                <canvas id="chartDepartments"></canvas>
            </div>
        </div>

        <!-- Gráfico 4 -->
        <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm flex flex-col">
            <h3 class="font-bold text-gray-900 text-sm mb-6">Distribución de Calificaciones</h3>
            <div class="relative w-full h-64" wire:ignore>
                <canvas id="chartGrades"></canvas>
            </div>
        </div>
    </div>

    <!-- 5. Resumen Ejecutivo -->
    <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm">
        <h3 class="font-bold text-gray-900 text-sm mb-1">Resumen Ejecutivo</h3>
        <p class="text-xs text-gray-500 mb-4">Análisis del periodo actual</p>
        <div class="space-y-4">
            <div>
                <h4 class="text-xs font-bold text-blue-700 border-l-4 border-blue-500 pl-2 mb-2">Tendencias Positivas</h4>
                <ul class="list-disc list-inside text-xs text-gray-600 space-y-1 ml-1">
                    <li>Incremento del 12% en participación respecto al mes anterior</li>
                    <li>Tasa de finalización de 89%, superando el objetivo de 85%</li>
                </ul>
            </div>
            <div>
                <h4 class="text-xs font-bold text-amber-600 border-l-4 border-amber-500 pl-2 mb-2">Áreas de Oportunidad</h4>
                <ul class="list-disc list-inside text-xs text-gray-600 space-y-1 ml-1">
                    <li>Aumentar la participación en el departamento de Operaciones</li>
                    <li>Diversificar la oferta de capacitaciones en categoría "Otros"</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- SCRIPTS DE CHART.JS (Separados para evitar errores de sintaxis) -->
    <script>
        document.addEventListener('livewire:navigated', function () {
            initCharts();
        });

        // También ejecutamos al cargar por si no es una navegación SPA
        document.addEventListener('DOMContentLoaded', function () {
            initCharts();
        });

        function initCharts() {
            if (typeof Chart === 'undefined') return;

            // 1. Inscripciones (Line)
            const ctxEnrollments = document.getElementById('chartEnrollments');
            if (ctxEnrollments) {
                new Chart(ctxEnrollments, {
                    type: 'line',
                    data: @json($enrollmentsChartData),
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: { mode: 'index', intersect: false },
                        plugins: { legend: { position: 'bottom', labels: { usePointStyle: true, padding: 20 } } },
                        scales: { y: { beginAtZero: true, grid: { borderDash: [2, 4] } }, x: { grid: { display: false } } }
                    }
                });
            }

            // 2. Categorías (Doughnut)
            const ctxCategories = document.getElementById('chartCategories');
            if (ctxCategories) {
                new Chart(ctxCategories, {
                    type: 'doughnut',
                    data: @json($categoryChartData),
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '60%',
                        plugins: { legend: { position: 'right', labels: { usePointStyle: true, boxWidth: 8 } } },
                        layout: { padding: 10 }
                    }
                });
            }

            // 3. Departamentos (Bar)
            const ctxDepartments = document.getElementById('chartDepartments');
            if (ctxDepartments) {
                new Chart(ctxDepartments, {
                    type: 'bar',
                    data: @json($departmentChartData),
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: { y: { beginAtZero: true, grid: { borderDash: [2, 4] } }, x: { grid: { display: false } } }
                    }
                });
            }

            // 4. Calificaciones (Horizontal Bar)
            const ctxGrades = document.getElementById('chartGrades');
            if (ctxGrades) {
                new Chart(ctxGrades, {
                    type: 'bar',
                    data: @json($gradesChartData),
                    options: {
                        indexAxis: 'y',
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: { x: { beginAtZero: true, grid: { borderDash: [2, 4] } }, y: { grid: { display: false } } }
                    }
                });
            }
        }
    </script>
</div>
