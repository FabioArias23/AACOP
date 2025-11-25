<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ParticipantsExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $search;

    // Recibimos el término de búsqueda para filtrar igual que en la tabla
    public function __construct($search = null)
    {
        $this->search = $search;
    }

    public function query()
    {
        // Filtramos solo estudiantes y aplicamos búsqueda si existe
        return User::query()
            ->where('role', 'student')
            ->when($this->search, function ($query) {
                $query->where(function($q) {
                    $q->where('name', 'like', "%{$this->search}%")
                      ->orWhere('email', 'like', "%{$this->search}%")
                      ->orWhere('department', 'like', "%{$this->search}%");
                });
            })
            ->with('enrollments.session.training'); // Cargar relaciones para evitar N+1
    }

    // Definimos qué datos van en cada celda
    public function map($student): array
    {
        // Obtenemos los cursos activos (ejemplo de lógica)
        $activeCourses = $student->enrollments
            ->where('status', 'En progreso')
            ->map(fn($e) => $e->session->training->title)
            ->implode(', ');

        return [
            $student->id,
            $student->name,
            $student->email,
            $student->department ?? 'N/A',
            $activeCourses ?: 'Ninguno',
            $student->created_at->format('d/m/Y'),
        ];
    }

    // Encabezados de la tabla Excel
    public function headings(): array
    {
        return [
            'ID',
            'Nombre Completo',
            'Correo Electrónico',
            'Departamento',
            'Cursos en Progreso',
            'Fecha de Registro',
        ];
    }

    // Estilos básicos (Negrita en encabezado)
    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 12]],
        ];
    }
}
