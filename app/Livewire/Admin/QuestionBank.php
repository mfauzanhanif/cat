<?php

namespace App\Livewire\Admin;

use App\Exports\QuestionsTemplateExport;
use App\Imports\QuestionsImport;
use App\Models\Question;
use App\Models\Subject;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;

#[Layout('layouts.app')]
#[Title('Bank Soal')]
class QuestionBank extends Component
{
    use WithFileUploads;
    use WithPagination;

    #[Url]
    public string $search = '';

    #[Url]
    public ?int $subjectFilter = null;

    public bool $showModal = false;

    public bool $showImportModal = false;

    public bool $isEditing = false;

    public ?int $editingId = null;

    // Form fields
    public ?int $subject_id = null;

    public string $content = '';

    public $image;

    public ?string $existingImage = null;

    public $importFile;

    public string $option_a = '';

    public string $option_b = '';

    public string $option_c = '';

    public string $option_d = '';

    public string $correct_answer = 'A';

    public function rules(): array
    {
        return [
            'subject_id' => ['required', 'exists:subjects,id'],
            'content' => ['required', 'string'],
            'image' => ['nullable', 'image', 'max:2048'],
            'option_a' => ['required', 'string', 'max:500'],
            'option_b' => ['required', 'string', 'max:500'],
            'option_c' => ['required', 'string', 'max:500'],
            'option_d' => ['required', 'string', 'max:500'],
            'correct_answer' => ['required', Rule::in(['A', 'B', 'C', 'D'])],
        ];
    }

    protected function importRules(): array
    {
        return [
            'importFile' => ['required', 'mimes:xlsx,xls,csv', 'max:10240'],
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
        $this->editingId = $id;
        $this->subject_id = $question->subject_id;
        $this->content = $question->content;
        $this->existingImage = $question->image_path;
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

        // Logika Upload Gambar
        if ($this->image) {
            // Jika sedang edit dan ada gambar baru, hapus gambar lama
            if ($this->isEditing && $this->existingImage) {
                Storage::disk('public')->delete($this->existingImage);
            }
            // Simpan gambar baru ke folder 'question-images' di storage public
            $data['image_path'] = $this->image->store('question-images', 'public');
        }

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
        // Hapus gambar dari storage saat soal dihapus
        if ($question->image_path) {
            Storage::disk('public')->delete($question->image_path);
        }
        $question->delete();
        session()->flash('success', 'Soal berhasil dihapus.');
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->resetForm();
    }

    public function openImportModal(): void
    {
        $this->resetImportForm();
        $this->showImportModal = true;
    }

    public function closeImportModal(): void
    {
        $this->showImportModal = false;
        $this->resetImportForm();
    }

    private function resetImportForm(): void
    {
        $this->importFile = null;
        $this->resetValidation();
    }

    public function importExcel(): void
    {
        $this->validate($this->importRules());

        try {
            Excel::import(new QuestionsImport, $this->importFile);
            session()->flash('success', 'Import soal berhasil!');
            $this->closeImportModal();
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            $errorMessages = [];
            foreach ($failures as $failure) {
                $errorMessages[] = "Baris {$failure->row()}: ".implode(', ', $failure->errors());
            }
            $this->addError('importFile', 'Gagal import. Periksa data Excel anda: '.implode(' | ', $errorMessages));
        } catch (\Exception $e) {
            $this->addError('importFile', 'Terjadi kesalahan saat import: '.$e->getMessage());
        }
    }

    public function downloadTemplate()
    {
        return Excel::download(new QuestionsTemplateExport, 'template-soal.xlsx');
    }

    private function resetForm(): void
    {
        $this->editingId = null;
        $this->subject_id = $this->subjectFilter;
        $this->content = '';
        $this->image = null;
        $this->existingImage = null;
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
        $questions = Question::query()
            ->with('subject')
            ->when($this->search, fn ($q) => $q->where('content', 'like', "%{$this->search}%"))
            ->when($this->subjectFilter, fn ($q) => $q->where('subject_id', $this->subjectFilter))
            ->orderByDesc('created_at')
            ->paginate(10);

        $subjects = Subject::orderBy('name')->get();

        return view('livewire.admin.question-bank', [
            'questions' => $questions,
            'subjects' => $subjects,
        ]);
    }
}
