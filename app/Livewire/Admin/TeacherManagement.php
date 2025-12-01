<?php

namespace App\Livewire\Admin;

use App\Models\User;
use Livewire\Component;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class TeacherManagement extends Component
{
    public bool $openModal = false;

    // Propiedades del formulario
    public $name = '';
    public $email = '';
    public $password = '';
    public $password_confirmation = '';

    protected function rules()
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ];
    }

    public function render()
    {
        // CORRECCIÓN: Usamos 'teacher' para coincidir con tu middleware en web.php
        $docentes = User::where('role', 'teacher')
            ->orderBy('name')
            ->get();

        return view('livewire.admin.teacher-management', [
            'docentes' => $docentes,
        ]);
    }

    public function save()
    {
        $this->validate();

        User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
            'role' => 'teacher', // <--- AQUÍ FORZAMOS EL ROL EN SUPABASE
        ]);

        $this->reset(['name', 'email', 'password', 'password_confirmation', 'openModal']);

        session()->flash('success', 'Docente registrado correctamente.');
    }

    public function delete($id)
    {
        $user = User::findOrFail($id);

        // Evitar borrarse a uno mismo si fuera necesario
        if($user->id === auth()->id()) {
            return;
        }

        $user->delete();
        session()->flash('success', 'Docente eliminado correctamente.');
    }
}
