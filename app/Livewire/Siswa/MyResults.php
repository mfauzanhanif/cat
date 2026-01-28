<?php

namespace App\Livewire\Siswa;

use App\Models\ExamSession;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Hasil Ujian Saya')]
class MyResults extends Component
{
    use WithPagination;

    #[Url]
    public string $search = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $user = auth()->user();

        $sessions = ExamSession::query()
            ->with(['exam.subject'])
            ->where('user_id', $user->id)
            ->where('status', 'completed')
            ->when($this->search, fn($q) => $q->whereHas('exam', fn($q2) => $q2->where('title', 'like', "%{$this->search}%")))
            ->orderByDesc('created_at')
            ->paginate(10);

        // Statistics
        $totalExams = ExamSession::where('user_id', $user->id)->where('status', 'completed')->count();
        $averageScore = ExamSession::where('user_id', $user->id)->where('status', 'completed')->avg('score');
        $highestScore = ExamSession::where('user_id', $user->id)->where('status', 'completed')->max('score');
        $lowestScore = ExamSession::where('user_id', $user->id)->where('status', 'completed')->min('score');

        return view('livewire.siswa.my-results', [
            'sessions' => $sessions,
            'totalExams' => $totalExams,
            'averageScore' => round($averageScore ?? 0, 1),
            'highestScore' => $highestScore ?? 0,
            'lowestScore' => $lowestScore ?? 0,
        ]);
    }
}
