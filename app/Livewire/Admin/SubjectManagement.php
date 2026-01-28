<?php

namespace App\Livewire\Admin;

use App\Models\Subject;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Validation\Rule;

#[Layout('layouts.app')]
#[Title('Manajemen Mata Pelajaran')]
class SubjectManagement extends Component
{
    use WithPagination;

    #[Url]
    public string $search = '';

    public bool $showModal = false;
    public bool $isEditing = false;
    public ?int $editingId = null;

    // Form fields
    public string $name = '';
    public string $code = '';
    public ?int $teacher_id = null;

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'code' => [
                'required',
                'string',
                'max:20',
                Rule::unique('subjects', 'code')->ignore($this->editingId)
            ],
            'teacher_id' => ['nullable', 'exists:users,id'],
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
        $subject = Subject::findOrFail($id);
        $this->editingId = $id;
        $this->name = $subject->name;
        $this->code = $subject->code;
        $this->teacher_id = $subject->teacher_id;
        $this->isEditing = true;
        $this->showModal = true;
    }

    public function save(): void
    {
        $this->validate();

        $data = [
            'name' => $this->name,
            'code' => $this->code,
            'teacher_id' => $this->teacher_id ?: null,
        ];

        if ($this->isEditing) {
            Subject::findOrFail($this->editingId)->update($data);
            session()->flash('success', 'Mata pelajaran berhasil diperbarui.');
        } else {
            Subject::create($data);
            session()->flash('success', 'Mata pelajaran berhasil ditambahkan.');
        }

        $this->closeModal();
    }

    public function delete(int $id): void
    {
        Subject::findOrFail($id)->delete();
        session()->flash('success', 'Mata pelajaran berhasil dihapus.');
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->resetForm();
    }

    private function resetForm(): void
    {
        $this->editingId = null;
        $this->name = '';
        $this->code = '';
        $this->teacher_id = null;
        $this->resetValidation();
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $subjects = Subject::query()
            ->with(['teacher', 'questions', 'exams'])
            ->when($this->search, fn($q) => $q->where('name', 'like', "%{$this->search}%")
                ->orWhere('code', 'like', "%{$this->search}%"))
            ->orderByDesc('created_at')
            ->paginate(10);

        $teachers = User::where('role', 'guru')->orderBy('name')->get();

        return view('livewire.admin.subject-management', [
            'subjects' => $subjects,
            'teachers' => $teachers,
        ]);
    }
}
