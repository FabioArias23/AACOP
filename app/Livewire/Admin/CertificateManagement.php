<?php

namespace App\Livewire\Admin;

use App\Models\Certificate;
use App\Models\Enrollment;
use Livewire\Component;
use Livewire\WithPagination;

class CertificateManagement extends Component
{
    use WithPagination;

    public string $searchTerm = '';

    // Variables para el modal de previsualización
    public bool $previewDialogOpen = false;
    public ?Certificate $previewCertificate = null;

    public function render()
    {
        // 1. Certificados ya emitidos
        $certificates = Certificate::query()
            ->when($this->searchTerm, function ($query) {
                $query->where('student_name', 'like', '%' . $this->searchTerm . '%')
                    ->orWhere('training_title', 'like', '%' . $this->searchTerm . '%')
                    ->orWhere('certificate_number', 'like', '%' . $this->searchTerm . '%');
            })
            ->with('user', 'training')
            ->latest()
            ->paginate(10);

        // 2. Estudiantes aprobados pendientes de certificado
        $pendingEnrollments = Enrollment::where('status', 'Aprobado')
            ->whereDoesntHave('certificate') // Asumiendo que tienes la relación en el modelo
            ->with(['user', 'trainingSession'])
            ->get();

        return view('livewire.admin.certificate-management', [
            'certificates' => $certificates,
            'pendingEnrollments' => $pendingEnrollments,
        ]);
    }

    // Generar un nuevo certificado
    public function generateCertificate($enrollmentId)
    {
        $enrollment = Enrollment::with(['user', 'trainingSession'])->find($enrollmentId);

        if (!$enrollment) return;

        // Lógica simple para número de certificado
        $number = 'CERT-' . now()->year . '-' . str_pad(Certificate::count() + 1, 5, '0', STR_PAD_LEFT);

        $certificate = Certificate::create([
            'certificate_number' => $number,
            'user_id' => $enrollment->user_id,
            'training_id' => $enrollment->trainingSession->training_id,
            'enrollment_id' => $enrollment->id,
            'student_name' => $enrollment->user->name,
            'training_title' => $enrollment->trainingSession->training_title,
            'instructor_name' => $enrollment->trainingSession->instructor,
            'completion_date' => $enrollment->trainingSession->date,
            'grade' => $enrollment->grade,
        ]);

        // Vincular certificado a la inscripción (si tienes la columna certificate_id)
        // $enrollment->update(['certificate_id' => $certificate->id]);

        session()->flash('success', 'Certificado generado correctamente.');
    }

    public function showPreview($certificateId)
    {
        $this->previewCertificate = Certificate::find($certificateId);
        $this->previewDialogOpen = true;
    }
}
