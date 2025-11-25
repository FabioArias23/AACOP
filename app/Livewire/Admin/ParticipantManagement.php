<?php

namespace App\Livewire\Admin;

use App\Models\User;
use App\Models\TrainingSession;
use App\Models\Enrollment;
use Livewire\Component;

class ParticipantManagement extends Component
{
    public string $searchTerm = '';
    public bool $dialogOpen = false;
    public ?User $selectedParticipant = null;
    public $trainingSessionId;

    public function render()
    {
        $participants = User::where('role', 'student')
            ->when($this->searchTerm, function ($query) {
                $query->where('name', 'like', '%' . $this->searchTerm . '%')
                    ->orWhere('email', 'like', '%' . $this->searchTerm . '%');
            })
            ->with(['enrollments.trainingSession'])
            ->get();

        $availableSessions = TrainingSession::where('status', 'Programada')->orderBy('date')->get();

        return view('livewire.admin.participant-management', [
            'participants' => $participants,
            'availableSessions' => $availableSessions,
        ]);
    }

    public function openEnrollDialog(User $participant)
    {
        $this->selectedParticipant = $participant;
        $this->reset('trainingSessionId', 'dialogOpen');
        $this->dialogOpen = true;
    }

    public function enroll()
    {
        $this->validate(['trainingSessionId' => 'required|exists:training_sessions,id']);

        $exists = Enrollment::where('user_id', $this->selectedParticipant->id)
            ->where('training_session_id', $this->trainingSessionId)
            ->exists();

        if ($exists) {
            session()->flash('error', 'El participante ya está inscrito.');
            return;
        }

        Enrollment::create([
            'user_id' => $this->selectedParticipant->id,
            'training_session_id' => $this->trainingSessionId,
            'status' => 'Inscrito',
            'attendance' => 0
        ]);

        session()->flash('success', 'Inscripción exitosa.');
        $this->dialogOpen = false;
    }

    // Configuración exacta de colores e iconos según el diseño React
    public function getStatusConfig(string $status): array
    {
        return match ($status) {
            'Inscrito' => [
                // Celeste
                'class' => 'bg-[#38C0E3]/10 text-[#38C0E3]',
                'icon' => 'lucide-clock'
            ],
            'Completado' => [
                // Verde
                'class' => 'bg-[#00A885]/10 text-[#00A885]',
                'icon' => 'lucide-check-circle-2'
            ],
            'En progreso' => [
                // Dorado/Amarillo
                'class' => 'bg-[#FFD700]/10 text-[#B8860B]',
                'icon' => 'lucide-clock'
            ],
            'Cancelado', 'Reprobado' => [
                // Rojo
                'class' => 'bg-[#ED1C24]/10 text-[#ED1C24]',
                'icon' => 'lucide-x-circle'
            ],
            default => [
                'class' => 'bg-gray-100 text-gray-600',
                'icon' => 'lucide-circle'
            ],
        };
    }
}
