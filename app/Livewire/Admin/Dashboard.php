<?php

namespace App\Livewire\Admin;

use App\Models\Exam;
use App\Models\ExamSession;
use App\Models\Question;
use App\Models\Subject;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Admin Dashboard')]
class Dashboard extends Component
{
    public function render()
    {
        return view('livewire.admin.dashboard', [
            'totalSiswa' => User::where('role', 'siswa')->count(),
            'totalGuru' => User::where('role', 'guru')->count(),
            'totalSubjects' => Subject::count(),
            'totalQuestions' => Question::count(),
            'totalExams' => Exam::count(),
            'activeExams' => Exam::where('is_active', true)->count(),
            'recentSessions' => ExamSession::with(['user', 'exam.subject'])
                ->orderByDesc('created_at')
                ->limit(5)
                ->get(),
        ]);
    }
}
