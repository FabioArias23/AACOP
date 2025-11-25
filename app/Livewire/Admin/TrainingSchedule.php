<?php

namespace App\Livewire\Admin;

use App\Models\Campus;
use App\Models\Training;
use App\Models\TrainingSession;
use Carbon\Carbon;
use Livewire\Component;

class TrainingSchedule extends Component
{
    public $selectedDate;
    public bool $dialogOpen = false;

    // Propiedades del formulario
    public $training_id = '';
    public $campus_id = '';
    public $date = '';
    public $start_time = '09:00';
    public $end_time = '13:00';
    public $capacity = 0;
    public $instructor = '';

    public function mount()
    {
        $this->selectedDate = now()->format('Y-m-d');
        $this->date = $this->selectedDate;
    }

    // Cuando se selecciona una capacitación, autocompletamos datos sugeridos
    public function updatedTrainingId($value)
    {
        $training = Training::find($value);
        if ($training) {
            $this->instructor = $training->instructor;
            $this->capacity = $training->capacity;
        }
    }

    // Cuando cambia la fecha del calendario, actualizamos el formulario también
    public function updatedSelectedDate($value)
    {
        $this->date = $value;
    }

    public function create()
    {
        $this->reset(['training_id', 'campus_id', 'start_time', 'end_time', 'instructor']);
        $this->date = $this->selectedDate;
        // Valores por defecto
        $this->start_time = '09:00';
        $this->end_time = '13:00';
        $this->dialogOpen = true;
    }

    public function save()
    {
        $this->validate([
            'training_id' => 'required|exists:trainings,id',
            'campus_id' => 'required|exists:campuses,id',
            'date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
            'capacity' => 'required|integer|min:1',
            'instructor' => 'required|string',
        ]);

        // Obtenemos los modelos para llenar los campos denormalizados (título, nombre sede, etc.)
        $training = Training::find($this->training_id);
        $campus = Campus::find($this->campus_id);

        TrainingSession::create([
            'training_id' => $this->training_id,
            'campus_id' => $this->campus_id,
            'training_title' => $training->title,
            'campus_name' => $campus->name,
            'instructor' => $this->instructor,
            'date' => $this->date,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'capacity' => $this->capacity,
            'status' => 'Programada',
            'registered' => 0
        ]);

        session()->flash('success', 'Sesión programada exitosamente.');
        $this->dialogOpen = false;

        // Reseteamos el formulario
        $this->reset(['training_id', 'campus_id', 'instructor']);
    }

    public function render()
    {
        // 1. Obtener sesiones de la fecha seleccionada en el input date
        $sessionsOnDate = TrainingSession::whereDate('date', $this->selectedDate)
            ->orderBy('start_time')
            ->get();

        // 2. Obtener próximas sesiones (independiente de la fecha seleccionada)
        $upcomingSessions = TrainingSession::where('date', '>=', now())
            ->orderBy('date')
            ->orderBy('start_time')
            ->take(5)
            ->get();

        // 3. Listas para los select del formulario
        $trainings = Training::where('status', 'Activo')->get();
        $campuses = Campus::where('status', 'Activo')->get();

        return view('livewire.admin.training-schedule', [
            'sessionsOnDate' => $sessionsOnDate,
            'upcomingSessions' => $upcomingSessions,
            'trainings' => $trainings,
            'campuses' => $campuses,
        ]);
    }
}
