<?php

namespace App\Livewire\Siswa;

use App\Models\Exam;
use App\Models\ExamAnswer;
use App\Models\ExamSession;
use App\Models\Question;
use Carbon\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;

#[Layout('layouts.app')]
class TakeExam extends Component
{
    public Exam $exam;
    public ?ExamSession $session = null;
    public $questions = [];
    public $answers = [];
    public int $currentQuestionIndex = 0;
    public int $remainingSeconds = 0;

    public function mount(Exam $exam)
    {
        $this->exam = $exam->load('subject');
        $user = auth()->user();

        // Check if exam is active
        if (!$this->exam->is_active) {
            session()->flash('error', 'Ujian ini tidak tersedia.');
            return redirect()->route('siswa.dashboard');
        }

        // Check if already completed
        $existingSession = ExamSession::where('exam_id', $exam->id)
            ->where('user_id', $user->id)
            ->first();

        if ($existingSession && $existingSession->status === 'completed') {
            session()->flash('error', 'Anda sudah mengerjakan ujian ini.');
            return redirect()->route('siswa.dashboard');
        }

        // Resume or start new session
        if ($existingSession && $existingSession->status === 'ongoing') {
            $this->session = $existingSession;
            $this->loadExistingSession();
        } else {
            $this->startNewSession();
        }

        $this->calculateRemainingTime();
    }

    private function startNewSession(): void
    {
        $user = auth()->user();
        $now = Carbon::now();

        // Create session
        $this->session = ExamSession::create([
            'exam_id' => $this->exam->id,
            'user_id' => $user->id,
            'start_time' => $now,
            'end_time' => $now->copy()->addMinutes($this->exam->duration_minutes),
            'status' => 'ongoing',
        ]);

        // Get random questions
        $randomQuestions = Question::where('subject_id', $this->exam->subject_id)
            ->inRandomOrder()
            ->limit($this->exam->total_questions)
            ->get();

        // Create exam answers for each question
        foreach ($randomQuestions as $question) {
            ExamAnswer::create([
                'exam_session_id' => $this->session->id,
                'question_id' => $question->id,
                'selected_answer' => null,
                'is_correct' => false,
            ]);
        }

        $this->loadExistingSession();
    }

    private function loadExistingSession(): void
    {
        // Load questions from exam answers
        $examAnswers = ExamAnswer::where('exam_session_id', $this->session->id)
            ->with('question')
            ->get();

        $this->questions = [];
        $this->answers = [];

        foreach ($examAnswers as $index => $examAnswer) {
            $this->questions[$index] = [
                'id' => $examAnswer->question->id,
                'content' => $examAnswer->question->content,
                'option_a' => $examAnswer->question->option_a,
                'option_b' => $examAnswer->question->option_b,
                'option_c' => $examAnswer->question->option_c,
                'option_d' => $examAnswer->question->option_d,
                'answer_id' => $examAnswer->id,
            ];
            $this->answers[$examAnswer->id] = $examAnswer->selected_answer;
        }
    }

    private function calculateRemainingTime(): void
    {
        if ($this->session) {
            $now = Carbon::now();
            $endTime = $this->session->end_time;

            if ($now->gte($endTime)) {
                $this->remainingSeconds = 0;
                $this->submitExam();
            } else {
                $this->remainingSeconds = $now->diffInSeconds($endTime);
            }
        }
    }

    public function selectAnswer(int $answerId, string $option): void
    {
        // Check if time is up
        $this->calculateRemainingTime();
        if ($this->remainingSeconds <= 0) {
            return;
        }

        $this->answers[$answerId] = $option;

        // Save to database immediately
        $examAnswer = ExamAnswer::find($answerId);
        if ($examAnswer) {
            $question = Question::find($examAnswer->question_id);
            $examAnswer->update([
                'selected_answer' => $option,
                'is_correct' => $question && $question->correct_answer === $option,
            ]);
        }
    }

    public function goToQuestion(int $index): void
    {
        if ($index >= 0 && $index < count($this->questions)) {
            $this->currentQuestionIndex = $index;
        }
    }

    public function nextQuestion(): void
    {
        if ($this->currentQuestionIndex < count($this->questions) - 1) {
            $this->currentQuestionIndex++;
        }
    }

    public function previousQuestion(): void
    {
        if ($this->currentQuestionIndex > 0) {
            $this->currentQuestionIndex--;
        }
    }

    #[On('timer-expired')]
    public function submitExam()
    {
        if (!$this->session || $this->session->status === 'completed') {
            return $this->redirect(route('siswa.dashboard'), navigate: true);
        }

        // Calculate score
        $this->session->calculateScore();

        // Update session status
        $this->session->update(['status' => 'completed']);

        session()->flash('success', 'Ujian berhasil diselesaikan!');
        return $this->redirect(route('siswa.dashboard'), navigate: true);
    }

    public function render()
    {
        return view('livewire.siswa.take-exam')
            ->layoutData(['title' => $this->exam->title]);
    }
}
