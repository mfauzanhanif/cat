<?php

namespace App\Livewire\Guru;

use App\Models\Question;
use App\Models\Subject;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Validation\Rule;

#[Layout('layouts.app')]
#[Title('Bank Soal')]
class QuestionBank extends Component
{
    use WithPagination;

    #[Url]
    public string $search = '';

    #[Url]
    public ?int $subjectFilter = null;

    public bool $showModal = false;
    public bool $isEditing = false;
    public ?int $editingId = null;

    // Form fields
    public ?int $subject_id = null;
    public string $content = '';
    public string $option_a = '';
    public string $option_b = '';
    public string $option_c = '';
    public string $option_d = '';
    public string $correct_answer = 'A';

    public function getMySubjectsProperty()
    {
        return Subject::where('teacher_id', auth()->id())->get();
    }

    public function rules(): array
    {
        $mySubjectIds = $this->mySubjects->pluck('id')->toArray();

        return [
            'subject_id' => ['required', Rule::in($mySubjectIds)],
            'content' => ['required', 'string'],
            'option_a' => ['required', 'string', 'max:500'],
            'option_b' => ['required', 'string', 'max:500'],
            'option_c' => ['required', 'string', 'max:500'],
            'option_d' => ['required', 'string', 'max:500'],
            'correct_answer' => ['required', Rule::in(['A', 'B', 'C', 'D'])],
        ];
    }

    public function openCreateModal(): void
    {
        $this->resetForm();
        $this->isEditing = false;
        $this->showModal = true;
    }

    public function openEditModal(int $id): void
    {
        $question = Question::findOrFail($id);

        // Verify ownership
        if (!$this->mySubjects->pluck('id')->contains($question->subject_id)) {
            session()->flash('error', 'Anda tidak memiliki akses ke soal ini.');
            return;
        }

        $this->editingId = $id;
        $this->subject_id = $question->subject_id;
        $this->content = $question->content;
        $this->option_a = $question->option_a;
        $this->option_b = $question->option_b;
        $this->option_c = $question->option_c;
        $this->option_d = $question->option_d;
        $this->correct_answer = $question->correct_answer;
        $this->isEditing = true;
        $this->showModal = true;
    }

    public function save(): void
    {
        $this->validate();

        $data = [
            'subject_id' => $this->subject_id,
            'content' => $this->content,
            'option_a' => $this->option_a,
            'option_b' => $this->option_b,
            'option_c' => $this->option_c,
            'option_d' => $this->option_d,
            'correct_answer' => $this->correct_answer,
        ];

        if ($this->isEditing) {
            Question::findOrFail($this->editingId)->update($data);
            session()->flash('success', 'Soal berhasil diperbarui.');
        } else {
            Question::create($data);
            session()->flash('success', 'Soal berhasil ditambahkan.');
        }

        $this->closeModal();
    }

    public function delete(int $id): void
    {
        $question = Question::findOrFail($id);

        // Verify ownership
        if (!$this->mySubjects->pluck('id')->contains($question->subject_id)) {
            session()->flash('error', 'Anda tidak memiliki akses ke soal ini.');
            return;
        }

        $question->delete();
        session()->flash('success', 'Soal berhasil dihapus.');
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->resetForm();
    }

    private function resetForm(): void
    {
        $this->editingId = null;
        $this->subject_id = $this->subjectFilter ?? $this->mySubjects->first()?->id;
        $this->content = '';
        $this->option_a = '';
        $this->option_b = '';
        $this->option_c = '';
        $this->option_d = '';
        $this->correct_answer = 'A';
        $this->resetValidation();
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingSubjectFilter(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $mySubjectIds = $this->mySubjects->pluck('id');

        $questions = Question::query()
            ->with('subject')
            ->whereIn('subject_id', $mySubjectIds)
            ->when($this->search, fn($q) => $q->where('content', 'like', "%{$this->search}%"))
            ->when($this->subjectFilter, fn($q) => $q->where('subject_id', $this->subjectFilter))
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('livewire.guru.question-bank', [
            'questions' => $questions,
            'subjects' => $this->mySubjects,
        ]);
    }
}
