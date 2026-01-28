<?php

namespace App\Livewire\Admin;

use App\Models\Exam;
use App\Models\Subject;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Manajemen Ujian')]
class ExamManagement extends Component
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
    public string $title = '';
    public int $duration_minutes = 60;
    public int $total_questions = 10;
    public bool $is_active = false;

    public function rules(): array
    {
        return [
            'subject_id' => ['required', 'exists:subjects,id'],
            'title' => ['required', 'string', 'max:255'],
            'duration_minutes' => ['required', 'integer', 'min:5', 'max:300'],
            'total_questions' => ['required', 'integer', 'min:1', 'max:100'],
            'is_active' => ['boolean'],
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
        $exam = Exam::findOrFail($id);
        $this->editingId = $id;
        $this->subject_id = $exam->subject_id;
        $this->title = $exam->title;
        $this->duration_minutes = $exam->duration_minutes;
        $this->total_questions = $exam->total_questions;
        $this->is_active = $exam->is_active;
        $this->isEditing = true;
        $this->showModal = true;
    }

    public function save(): void
    {
        $this->validate();

        // Validasi jumlah soal tersedia
        $subject = Subject::with('questions')->findOrFail($this->subject_id);
        if ($subject->questions->count() < $this->total_questions) {
            $this->addError('total_questions', "Jumlah soal yang tersedia untuk mapel ini hanya {$subject->questions->count()} soal.");
            return;
        }

        $data = [
            'subject_id' => $this->subject_id,
            'title' => $this->title,
            'duration_minutes' => $this->duration_minutes,
            'total_questions' => $this->total_questions,
            'is_active' => $this->is_active,
        ];

        if ($this->isEditing) {
            Exam::findOrFail($this->editingId)->update($data);
            session()->flash('success', 'Ujian berhasil diperbarui.');
        } else {
            Exam::create($data);
            session()->flash('success', 'Ujian berhasil ditambahkan.');
        }

        $this->closeModal();
    }

    public function toggleActive(int $id): void
    {
        $exam = Exam::findOrFail($id);
        $exam->update(['is_active' => !$exam->is_active]);
        session()->flash('success', 'Status ujian berhasil diubah.');
    }

    public function delete(int $id): void
    {
        Exam::findOrFail($id)->delete();
        session()->flash('success', 'Ujian berhasil dihapus.');
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->resetForm();
    }

    private function resetForm(): void
    {
        $this->editingId = null;
        $this->subject_id = $this->subjectFilter;
        $this->title = '';
        $this->duration_minutes = 60;
        $this->total_questions = 10;
        $this->is_active = false;
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
        $exams = Exam::query()
            ->with(['subject', 'sessions'])
            ->when($this->search, fn($q) => $q->where('title', 'like', "%{$this->search}%"))
            ->when($this->subjectFilter, fn($q) => $q->where('subject_id', $this->subjectFilter))
            ->orderByDesc('created_at')
            ->paginate(10);

        $subjects = Subject::withCount('questions')->orderBy('name')->get();

        return view('livewire.admin.exam-management', [
            'exams' => $exams,
            'subjects' => $subjects,
        ]);
    }
}
