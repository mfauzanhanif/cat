<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
// database/migrations/xxxx_xx_xx_create_exams_table.php
        Schema::create('exams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subject_id')->constrained()->cascadeOnDelete();
            $table->string('title'); // misal: UAS Semester Ganjil
            $table->integer('duration_minutes'); // misal: 90 menit
            $table->integer('total_questions'); // Jumlah soal yang akan di-generate (misal: 40)
            $table->boolean('is_active')->default(false); // Guru bisa on/off ujian
            $table->timestamps();
        });

        // database/migrations/xxxx_xx_xx_create_exam_sessions_table.php
        // Tabel ini memastikan siswa hanya ujian 1x dan menyimpan timer
        Schema::create('exam_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            $table->dateTime('start_time'); // Waktu mulai klik
            $table->dateTime('end_time'); // Batas waktu selesai (start + duration)

            $table->integer('score')->nullable(); // Nilai akhir
            $table->enum('status', ['ongoing', 'completed'])->default('ongoing');
            $table->timestamps();
        });

        // database/migrations/xxxx_xx_xx_create_exam_answers_table.php
        // Tabel ini menyimpan soal random yang didapat siswa & jawaban mereka
        Schema::create('exam_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_session_id')->constrained()->cascadeOnDelete();
            $table->foreignId('question_id')->constrained()->cascadeOnDelete();

            // Jawaban siswa (null jika belum dijawab)
            $table->enum('selected_answer', ['A', 'B', 'C', 'D'])->nullable();
            // Menyimpan status benar/salah per soal untuk analisis
            $table->boolean('is_correct')->default(false);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exams');
        Schema::dropIfExists('exam_sessions');
        Schema::dropIfExists('exam_answers');
    }
};
