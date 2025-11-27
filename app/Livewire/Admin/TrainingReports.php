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
    public $totalParticipants = 0;
    public $totalTrainings = 0;
    public $completionRate = 0;
    public $averageGrade = 0;

    // Datos para Gráficos
    public $enrollmentsChartData = [];
    public $categoryChartData = [];
    public $departmentChartData = [];
    public $gradesChartData = [];

    public function mount()
    {
        $this->calculateMetrics();
        $this->prepareCharts();
    }

    public function calculateMetrics()
    {
        $this->totalParticipants = User::where('role', 'student')->count();
        $this->totalTrainings = Training::count();

        $totalEnrollments = Enrollment::count();
        $completed = Enrollment::whereIn('status', ['Aprobado', 'Completado'])->count();

        $this->completionRate = $totalEnrollments > 0 ? round(($completed / $totalEnrollments) * 100) : 0;
        $this->averageGrade = round(Enrollment::whereNotNull('grade')->avg('grade') ?? 0, 1);
    }

    public function prepareCharts()
    {
        // 1. Gráfico de Línea: Inscripciones por Mes (Últimos 6 meses)
        // 🔥 CORRECCIÓN: Usamos TO_CHAR para PostgreSQL en lugar de DATE_FORMAT
        $monthlyStats = Enrollment::select(
            DB::raw("TO_CHAR(created_at, 'Mon') as month"),
            DB::raw('count(*) as total')
        )
        ->where('created_at', '>=', now()->subMonths(6))
        ->groupBy('month')
        // En Postgres necesitamos agrupar también por la fecha para ordenar, o usar min/max
        // Para simplificar el ordenamiento en el gráfico sin complicar la query SQL:
        ->orderByRaw("MIN(created_at)")
        ->get();

        // Si no hay datos, ponemos datos dummy
        if ($monthlyStats->isEmpty()) {
            $months = ['Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov'];
            $data = [0, 0, 0, 0, 0, 0];
        } else {
            $months = $monthlyStats->pluck('month')->toArray();
            $data = $monthlyStats->pluck('total')->toArray();
        }

        $this->enrollmentsChartData = [
            'labels' => $months,
            'datasets' => [
                [
                    'label' => 'Inscripciones',
                    'data' => $data,
                    'borderColor' => '#3b82f6',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'tension' => 0.4,
                    'fill' => true
                ]
            ]
        ];

        // 2. Gráfico de Torta: Categorías
        $categories = Training::select('category', DB::raw('count(*) as total'))
            ->groupBy('category')
            ->get();

        $this->categoryChartData = [
            'labels' => $categories->isEmpty() ? ['Sin datos'] : $categories->pluck('category')->toArray(),
            'datasets' => [[
                'data' => $categories->isEmpty() ? [1] : $categories->pluck('total')->toArray(),
                'backgroundColor' => ['#3b82f6', '#10b981', '#f59e0b', '#8b5cf6', '#ef4444'],
            ]]
        ];

        // 3. Gráfico de Barras: Por Sede
        $campuses = TrainingSession::select('campus_name', DB::raw('count(*) as total'))
            ->join('enrollments', 'training_sessions.id', '=', 'enrollments.training_session_id')
            ->groupBy('campus_name')
            ->limit(5)
            ->get();

        $this->departmentChartData = [
            'labels' => $campuses->isEmpty() ? ['Sede Central'] : $campuses->pluck('campus_name')->toArray(),
            'datasets' => [[
                'label' => 'Participantes',
                'data' => $campuses->isEmpty() ? [0] : $campuses->pluck('total')->toArray(),
                'backgroundColor' => '#3b82f6',
                'borderRadius' => 4
            ]]
        ];

        // 4. Gráfico Horizontal: Notas
        // Postgres usa comillas dobles para alias o texto literal en CASE a veces requiere cuidado
        // Usamos sintaxis estándar SQL compatible
        $grades = Enrollment::select(
            DB::raw("CASE
                WHEN grade >= 9 THEN 'Excelente (9-10)'
                WHEN grade >= 7 THEN 'Bueno (7-8.9)'
                WHEN grade >= 6 THEN 'Regular (6-6.9)'
                ELSE 'Bajo (<6)'
            END as range_grade"),
            DB::raw('count(*) as total')
        )
        ->whereNotNull('grade')
        ->groupBy('range_grade')
        ->get();

        $this->gradesChartData = [
            'labels' => $grades->isEmpty() ? ['Sin notas'] : $grades->pluck('range_grade')->toArray(),
            'datasets' => [[
                'label' => 'Estudiantes',
                'data' => $grades->isEmpty() ? [0] : $grades->pluck('total')->toArray(),
                'backgroundColor' => ['#10b981', '#3b82f6', '#f59e0b', '#ef4444'],
                'borderRadius' => 4
            ]]
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
