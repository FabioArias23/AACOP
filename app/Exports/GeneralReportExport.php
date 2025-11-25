<?php

namespace App\Exports;

use App\Models\Enrollment;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class GeneralReportExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    public function collection()
    {
        // Obtenemos todas las inscripciones con sus relaciones
        return Enrollment::with(['user', 'trainingSession'])->get();
    }

    public function headings(): array
    {
        return [
            'ID Inscripción',
            'Estudiante',
            'Email',
            'Capacitación',
            'Sede',
            'Fecha',
            'Estado',
            'Asistencia (%)',
            'Nota Final'
        ];
    }

    public function map($enrollment): array
    {
        return [
            $enrollment->id,
            $enrollment->user->name,
            $enrollment->user->email,
            $enrollment->trainingSession->training_title,
            $enrollment->trainingSession->campus_name,
            $enrollment->trainingSession->date,
            $enrollment->status,
            $enrollment->attendance,
            $enrollment->grade ?? 'N/A',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Pone la primera fila en negrita
            1 => ['font' => ['bold' => true]],
        ];
    }
}
