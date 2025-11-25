<?php

namespace App\Livewire\Admin;

use App\Models\Enrollment;
use App\Models\Training;
use App\Models\TrainingSession;
use App\Models\User;
use Livewire\Component;
use App\Exports\GeneralReportExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;

class TrainingReports extends Component
{
    // Métricas
    public $totalParticipants;
    public $totalTrainings;
    public $completionRate;
    public $averageGrade;

    // Datos para Gráficos
    public $enrollmentsChartData = [];
    public $categoryChartData = [];
    public $departmentChartData = []; // Nuevo
    public $gradesChartData = [];     // Nuevo

    public function mount()
    {
        $this->calculateMetrics();
        $this->prepareLineChart();      // Inscripciones vs Completadas
        $this->prepareDoughnutChart();  // Categorías
        $this->prepareBarChart();       // Departamentos
        $this->prepareHorizontalBarChart(); // Calificaciones


    }

    public function calculateMetrics()
    {
        $this->totalParticipants = User::where('role', 'student')->count();
        $this->totalTrainings = Training::count();
        $totalEnrollments = Enrollment::count();
        $completed = Enrollment::where('status', 'Aprobado')->count();

        $this->completionRate = $totalEnrollments > 0 ? round(($completed / $totalEnrollments) * 100) : 0;
        $this->averageGrade = round(Enrollment::whereNotNull('grade')->avg('grade') ?? 0);
    }

    public function prepareLineChart()
    {
        // Simulación de datos por mes para que coincida con la imagen
        // En producción, usarías DB::raw queries agrupadas por mes
        $months = ['Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov'];

        $this->enrollmentsChartData = [
            'labels' => $months,
            'datasets' => [
                [
                    'label' => 'Inscripciones',
                    'data' => [45, 52, 48, 61, 70, 55], // Datos azules
                    'borderColor' => '#3b82f6',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'tension' => 0.4,
                    'fill' => false
                ],
                [
                    'label' => 'Completadas',
                    'data' => [38, 45, 42, 55, 62, 28], // Datos verdes
                    'borderColor' => '#10b981',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                    'tension' => 0.4,
                    'fill' => false
                ]
            ]
        ];
    }

    public function prepareDoughnutChart()
{
    // Asegúrate de que esto tenga datos
    $this->categoryChartData = [
        'labels' => ['A', 'B', 'C'],
        'datasets' => [[
            'data' => [10, 20, 30],
            'backgroundColor' => ['#3b82f6', '#10b981', '#f59e0b']
        ]]
    ];
}

    public function prepareBarChart()
    {
        // Datos para "Participación por Departamento"
        $this->departmentChartData = [
            'labels' => ['Ventas', 'IT', 'RRHH', 'Administración', 'Operaciones'],
            'datasets' => [
                [
                    'label' => 'Participantes',
                    'data' => [28, 22, 18, 15, 12],
                    'backgroundColor' => '#3b82f6',
                    'borderRadius' => 4
                ]
            ]
        ];
    }

    public function prepareHorizontalBarChart()
    {
        // Datos para "Distribución de Calificaciones"
        $this->gradesChartData = [
            'labels' => ['Excelente (90-100)', 'Bueno (80-89)', 'Regular (70-79)', 'Bajo (<70)'],
            'datasets' => [
                [
                    'label' => 'Estudiantes',
                    'data' => [45, 35, 15, 5],
                    'backgroundColor' => ['#10b981', '#3b82f6', '#f59e0b', '#ef4444'],
                    'borderRadius' => 4
                ]
            ]
        ];
    }

    public function export()
    {
        return Excel::download(new GeneralReportExport, 'reporte-general.xlsx');
    }

    public function render()
    {
        return view('livewire.admin.training-reports');
    }
}
