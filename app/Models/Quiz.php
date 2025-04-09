<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quiz extends Model
{
    use HasFactory;

    protected $table = 'lesson_quizzes';

    protected $fillable = [
        'question',
        'answer1',
        'answer2',
        'answer3',
        'answer4',
        'correct',
        'lesson_id',
    ];

    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }
}
