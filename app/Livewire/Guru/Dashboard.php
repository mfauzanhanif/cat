<?php

namespace App\Livewire\Guru;

use App\Models\Exam;
use App\Models\ExamSession;
use App\Models\Question;
use App\Models\Subject;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Dashboard Guru')]
class Dashboard extends Component
{
    public function render()
    {
        $user = auth()->user();

        // Get subjects taught by this teacher
        $mySubjects = Subject::where('teacher_id', $user->id)->get();
        $subjectIds = $mySubjects->pluck('id');

        return view('livewire.guru.dashboard', [
            'mySubjects' => $mySubjects,
            'totalQuestions' => Question::whereIn('subject_id', $subjectIds)->count(),
            'totalExams' => Exam::whereIn('subject_id', $subjectIds)->count(),
            'activeExams' => Exam::whereIn('subject_id', $subjectIds)->where('is_active', true)->count(),
            'totalParticipants' => ExamSession::whereHas('exam', fn($q) => $q->whereIn('subject_id', $subjectIds))->count(),
            'recentSessions' => ExamSession::with(['user', 'exam.subject'])
                ->whereHas('exam', fn($q) => $q->whereIn('subject_id', $subjectIds))
                ->orderByDesc('created_at')
                ->limit(5)
                ->get(),
        ]);
    }
}
