<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ExamSession extends Model
{
protected $guarded = ['id'];

    // Casting agar start_time dan end_time otomatis jadi Carbon object
    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];

    public function exam(): BelongsTo
    {
        return $this->belongsTo(Exam::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function examAnswers(): HasMany
    {
        return $this->hasMany(ExamAnswer::class);
    }

    // Fungsi helper untuk menghitung nilai total
    public function calculateScore()
    {
        $totalQuestions = $this->exam->total_questions;
        $correctAnswers = $this->examAnswers()->where('is_correct', true)->count();

        if ($totalQuestions > 0) {
            $score = ($correctAnswers / $totalQuestions) * 100;
        } else {
            $score = 0;
        }

        $this->update(['score' => $score]);
    }
}
