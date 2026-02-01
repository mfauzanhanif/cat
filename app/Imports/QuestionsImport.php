<?php

namespace App\Imports;

use App\Models\Question;
use App\Models\Subject;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class QuestionsImport implements ToModel, WithHeadingRow, WithValidation
{
    /**
     * @return \Illuminate\Database\Eloquent\Model|null
     *
     * Note: Kolom tambahan seperti 'no' akan otomatis diabaikan.
     * Hanya kolom yang disebutkan di method ini yang akan diproses.
     */
    public function model(array $row)
    {
        // Kolom 'no' dan kolom lain yang tidak disebutkan akan diabaikan
        // Kita hanya mengambil kolom yang diperlukan untuk membuat Question
        $subject = Subject::where('name', $row['mata_pelajaran'])->first();

        if (! $subject) {
            // Opsional: Bisa throw error atau skip jika mapel tidak ditemukan
            // Untuk sekarang kita skip baris ini jika mapel tidak ada
            return null;
        }

        return new Question([
            'subject_id' => $subject->id,
            'content' => $row['pertanyaan'],
            'option_a' => $row['opsi_a'],
            'option_b' => $row['opsi_b'],
            'option_c' => $row['opsi_c'],
            'option_d' => $row['opsi_d'],
            'correct_answer' => strtoupper($row['kunci_jawaban']), // Pastikan huruf besar
            // 'image_path' dikosongkan untuk bulk import
        ]);
    }

    // Validasi data di dalam file Excel
    public function rules(): array
    {
        return [
            'mata_pelajaran' => ['required', 'string', 'exists:subjects,name'],
            'pertanyaan' => ['required', 'string'],
            'opsi_a' => ['required', 'string'],
            'opsi_b' => ['required', 'string'],
            'opsi_c' => ['required', 'string'],
            'opsi_d' => ['required', 'string'],
            'kunci_jawaban' => ['required', Rule::in(['A', 'B', 'C', 'D', 'a', 'b', 'c', 'd'])],
        ];
    }
}
