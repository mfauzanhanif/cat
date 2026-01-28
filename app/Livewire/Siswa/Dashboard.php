<?php

namespace App\Livewire\Siswa;

use App\Models\Exam;
use App\Models\ExamSession;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Dashboard Siswa')]
class Dashboard extends Component
{
    public function render()
    {
        $user = auth()->user();

        // Get active exams
        $availableExams = Exam::with('subject')
            ->where('is_active', true)
            ->get()
            ->map(function ($exam) use ($user) {
                // Check if student already took this exam
                $session = ExamSession::where('exam_id', $exam->id)
                    ->where('user_id', $user->id)
                    ->first();

                $exam->student_session = $session;
                $exam->can_take = $session === null;
                $exam->is_ongoing = $session && $session->status === 'ongoing';
                $exam->is_completed = $session && $session->status === 'completed';

                return $exam;
            });

        // Get my recent results
        $myResults = ExamSession::with(['exam.subject'])
            ->where('user_id', $user->id)
            ->where('status', 'completed')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        return view('livewire.siswa.dashboard', [
            'availableExams' => $availableExams,
            'myResults' => $myResults,
            'totalExamsTaken' => ExamSession::where('user_id', $user->id)->where('status', 'completed')->count(),
            'averageScore' => round(ExamSession::where('user_id', $user->id)->where('status', 'completed')->avg('score') ?? 0, 1),
        ]);
    }
}
