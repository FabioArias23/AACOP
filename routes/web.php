<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Livewire\Admin\CampusManagement;
use App\Livewire\Admin\Dashboard as AdminDashboard;
use App\Livewire\Teacher\Dashboard as TeacherDashboard;
use App\Livewire\Student\Dashboard as StudentDashboard;
use App\Http\Controllers\ExportController;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', function () {
    $user = auth()->user();
    if ($user->role === 'admin') return redirect()->route('admin.dashboard');
    if ($user->role === 'teacher') return redirect()->route('teacher.dashboard');
    if ($user->role === 'student') return redirect()->route('student.dashboard');
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// --- RUTAS ADMIN ---
Route::middleware(['auth', 'verified'])->prefix('admin')->name('admin.')->group(function () {
    // Ruta principal del dashboard que maneja las secciones dinámicas
    Route::get('/dashboard/{section?}', AdminDashboard::class)
        ->name('dashboard')
        ->where('section', 'dashboard|catalog|campus|schedule|participants|attendance|certificates|reports');

    // Ruta individual (si deseas acceder directamente por URL, aunque el dashboard ya lo maneja via section)
    Route::get('/sedes', CampusManagement::class)->name('campuses');
});


Route::get('/export/participants', [ExportController::class, 'exportParticipants'])
    ->name('export.participants')
    ->middleware(['auth', 'verified']); // Asegúrate de proteger la ruta

// --- RUTAS DOCENTE ---
Route::middleware(['auth', 'verified'])->prefix('teacher')->name('teacher.')->group(function () {
    Route::get('/dashboard/{section?}', TeacherDashboard::class)
        ->name('dashboard')
        ->where('section', 'dashboard|classes|attendance|grades');
});

// --- RUTAS ESTUDIANTE ---
Route::middleware(['auth', 'verified'])->prefix('student')->name('student.')->group(function () {
    Route::get('/dashboard/{section?}', StudentDashboard::class)
        ->name('dashboard')
        ->where('section', 'dashboard|courses|enrollments|progress');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
