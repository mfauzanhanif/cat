<?php

namespace Database\Seeders;

use App\Models\Exam;
use App\Models\Question;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. BUAT USER (ADMIN, GURU, SISWA)
        // Kita buat akun fix agar mudah login saat testing

        // Admin
        User::create([
            'name' => 'Administrator',
            'email' => 'admin@admin.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // Guru
        User::create([
            'name' => 'Bapak Guru',
            'email' => 'guru@guru.com',
            'password' => Hash::make('password'),
            'role' => 'guru',
        ]);

        // Siswa (Kita buat 2 siswa untuk demo)
        User::create([
            'name' => 'Siswa Teladan',
            'email' => 'siswa@siswa.com',
            'password' => Hash::make('password'),
            'role' => 'siswa',
        ]);

        User::create([
            'name' => 'Siswa Baru',
            'email' => 'siswa2@siswa.com',
            'password' => Hash::make('password'),
            'role' => 'siswa',
        ]);

        // 2. BUAT MATA PELAJARAN (SUBJECTS)
        $subjects = [
            ['name' => 'Matematika', 'code' => 'MTK-10'],
            ['name' => 'Bahasa Indonesia', 'code' => 'IND-10'],
            ['name' => 'Bahasa Inggris', 'code' => 'ING-10'],
        ];

        foreach ($subjects as $sub) {
            Subject::create($sub);
        }

        // 3. BUAT SOAL DUMMY (QUESTIONS)
        // Kita ambil Mapel Matematika (urutan pertama) untuk diisi soal
        $math = Subject::where('code', 'MTK-10')->first();

        // Buat 50 Soal Matematika sederhana
        for ($i = 1; $i <= 50; $i++) {
            Question::create([
                'subject_id' => $math->id,
                'content' => "Pertanyaan Matematika Nomor $i: Berapakah hasil dari $i + $i ?",
                'option_a' => (string) ($i + $i),      // Jawaban Benar (supaya gampang ngecek)
                'option_b' => (string) ($i + $i + 1),
                'option_c' => (string) ($i + $i + 2),
                'option_d' => (string) ($i + $i + 3),
                'correct_answer' => 'A', // Kita set semua kunci jawaban A biar mudah testing
            ]);
        }

        // 4. BUAT JADWAL UJIAN (EXAM)
        // Ujian Matematika, Durasi 60 Menit, Jumlah Soal 10 (diambil acak dari 50 soal diatas)
        Exam::create([
            'subject_id' => $math->id,
            'title' => 'Ujian Tengah Semester (UTS) Ganjil',
            'duration_minutes' => 60,
            'total_questions' => 10,
            'is_active' => true,
        ]);

        // Tambahkan satu ujian lagi tapi tidak aktif
        Exam::create([
            'subject_id' => $math->id,
            'title' => 'Ujian Akhir Semester (UAS) - Coming Soon',
            'duration_minutes' => 90,
            'total_questions' => 40,
            'is_active' => false,
        ]);
    }
}
