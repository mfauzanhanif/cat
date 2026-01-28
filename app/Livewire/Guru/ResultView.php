<?php

namespace App\Livewire\Guru;

use App\Models\Exam;
use App\Models\ExamSession;
use App\Models\Subject;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Hasil Ujian')]
class ResultView extends Component
{
    use WithPagination;

    #[Url]
    public string $search = '';

    #[Url]
    public ?int $examFilter = null;

    #[Url]
    public string $statusFilter = '';

    public function getMySubjectsProperty()
    {
        return Subject::where('teacher_id', auth()->id())->get();
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingExamFilter(): void
    {
        $this->resetPage();
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $mySubjectIds = $this->mySubjects->pluck('id');

        $sessions = ExamSession::query()
            ->with(['user', 'exam.subject'])
            ->whereHas('exam', fn($q) => $q->whereIn('subject_id', $mySubjectIds))
            ->when($this->search, fn($q) => $q->whereHas('user', fn($q2) => $q2->where('name', 'like', "%{$this->search}%")))
            ->when($this->examFilter, fn($q) => $q->where('exam_id', $this->examFilter))
            ->when($this->statusFilter, fn($q) => $q->where('status', $this->statusFilter))
            ->orderByDesc('created_at')
            ->paginate(15);

        $exams = Exam::with('subject')
            ->whereIn('subject_id', $mySubjectIds)
            ->orderBy('title')
            ->get();

        // Statistics for my subjects only
        $totalSessions = ExamSession::whereHas('exam', fn($q) => $q->whereIn('subject_id', $mySubjectIds))->count();
        $completedSessions = ExamSession::whereHas('exam', fn($q) => $q->whereIn('subject_id', $mySubjectIds))
            ->where('status', 'completed')->count();
        $averageScore = ExamSession::whereHas('exam', fn($q) => $q->whereIn('subject_id', $mySubjectIds))
            ->where('status', 'completed')->avg('score');

        return view('livewire.guru.result-view', [
            'sessions' => $sessions,
            'exams' => $exams,
            'totalSessions' => $totalSessions,
            'completedSessions' => $completedSessions,
            'averageScore' => round($averageScore ?? 0, 1),
        ]);
    }
}
