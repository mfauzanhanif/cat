<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Admin\Dashboard as AdminDashboard;
use App\Livewire\Admin\UserManagement;
use App\Livewire\Admin\SubjectManagement;
use App\Livewire\Admin\QuestionBank as AdminQuestionBank;
use App\Livewire\Admin\ExamManagement as AdminExamManagement;
use App\Livewire\Admin\ResultView as AdminResultView;
use App\Livewire\Guru\Dashboard as GuruDashboard;
use App\Livewire\Guru\QuestionBank as GuruQuestionBank;
use App\Livewire\Guru\ExamManagement as GuruExamManagement;
use App\Livewire\Guru\ResultView as GuruResultView;
use App\Livewire\Siswa\Dashboard as SiswaDashboard;
use App\Livewire\Siswa\TakeExam;
use App\Livewire\Siswa\MyResults;

Route::redirect('/', '/login')->name('home');

// Dashboard redirect based on role
Route::get('dashboard', function () {
    $user = auth()->user();
    return match($user->role) {
        'admin' => redirect()->route('admin.dashboard'),
        'guru' => redirect()->route('guru.dashboard'),
        'siswa' => redirect()->route('siswa.dashboard'),
        default => redirect()->route('login'),
    };
})->middleware(['auth', 'verified'])->name('dashboard');

// ===== ADMIN ROUTES =====
Route::middleware(['auth', 'verified', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', fn() => redirect()->route('admin.dashboard'));
    Route::livewire('dashboard', AdminDashboard::class)->name('dashboard');
    Route::livewire('users', UserManagement::class)->name('users');
    Route::livewire('subjects', SubjectManagement::class)->name('subjects');
    Route::livewire('questions', AdminQuestionBank::class)->name('questions');
    Route::livewire('exams', AdminExamManagement::class)->name('exams');
    Route::livewire('results', AdminResultView::class)->name('results');
});

// ===== GURU ROUTES =====
Route::middleware(['auth', 'verified', 'role:guru'])->prefix('guru')->name('guru.')->group(function () {
    Route::get('/', fn() => redirect()->route('guru.dashboard'));
    Route::livewire('dashboard', GuruDashboard::class)->name('dashboard');
    Route::livewire('questions', GuruQuestionBank::class)->name('questions');
    Route::livewire('exams', GuruExamManagement::class)->name('exams');
    Route::livewire('results', GuruResultView::class)->name('results');
});

// ===== SISWA ROUTES =====
Route::middleware(['auth', 'verified', 'role:siswa'])->prefix('siswa')->name('siswa.')->group(function () {
    Route::get('/', fn() => redirect()->route('siswa.dashboard'));
    Route::livewire('dashboard', SiswaDashboard::class)->name('dashboard');
    Route::livewire('exam/{exam}', TakeExam::class)->name('exam.take');
    Route::livewire('results', MyResults::class)->name('results');
});

require __DIR__.'/settings.php';
