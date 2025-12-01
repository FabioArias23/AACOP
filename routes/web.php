<?php

use Illuminate\Support\Facades\Route;

// Controladores
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ExportController;

// Componentes Livewire (Dashboards)
use App\Livewire\Admin\Dashboard as AdminDashboard;
use App\Livewire\Teacher\Dashboard as TeacherDashboard;
use App\Livewire\Student\Dashboard as StudentDashboard;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Redirección inicial
Route::get('/', function () {
    return redirect()->route('login');
});

// Redirección inteligente basada en el Rol
Route::get('/dashboard', function () {
    $user = auth()->user();

    if (!$user) {
        return redirect()->route('login');
    }

    // Asegúrate de que estos roles coincidan exactamente con lo que hay en tu DB (Supabase)
    return match ($user->role) {
        'admin'   => redirect()->route('admin.dashboard'),
        'teacher' => redirect()->route('teacher.dashboard'), // Antes era 'docente'
        'student' => redirect()->route('student.dashboard'), // Antes era 'participante'
        default   => redirect()->route('login'), // Por seguridad
    };
})->middleware(['auth', 'verified'])->name('dashboard');


// =========================================================================
// 🛡️ RUTAS DE ADMINISTRADOR
// =========================================================================
Route::middleware(['auth', 'verified'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        // Panel Principal (SPA con Livewire)
        // Maneja: catalog, campus, schedule, participants, teachers, attendance, certificates, reports
        Route::get('/dashboard/{section?}', AdminDashboard::class)
            ->name('dashboard')
            ->where('section', 'dashboard|catalog|campus|schedule|participants|teachers|attendance|certificates|reports');

        // Exportación de Excel
        Route::get('/export/participants', [ExportController::class, 'exportParticipants'])
            ->name('export.participants');
    });


// =========================================================================
// 🧑‍🏫 RUTAS DE DOCENTE
// =========================================================================
Route::middleware(['auth', 'verified'])
    ->prefix('teacher')
    ->name('teacher.')
    ->group(function () {

        // Panel Docente (SPA con Livewire)
        Route::get('/dashboard/{section?}', TeacherDashboard::class)
            ->name('dashboard')
            ->where('section', 'dashboard|classes|attendance|grades');
    });


// =========================================================================
// 🎓 RUTAS DE ESTUDIANTE
// =========================================================================
Route::middleware(['auth', 'verified'])
    ->prefix('student')
    ->name('student.')
    ->group(function () {

        // Panel Estudiante (SPA con Livewire)
        Route::get('/dashboard/{section?}', StudentDashboard::class)
            ->name('dashboard')
            ->where('section', 'dashboard|courses|enrollments|progress');
    });


// =========================================================================
// 👤 PERFIL DE USUARIO
// =========================================================================
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
