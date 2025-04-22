<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lesson extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'module_lessons';

    protected $fillable = [
        'year_id',
        'subject_id',
        'module_id',
        'name',
        'data',
        'content',
        'activity',
    ];

    protected $casts = [
        'data' => 'array',
        'activity' => 'array',
    ];

    public function quizzes()
    {
        return $this->hasMany(Quiz::class, 'lesson_id', 'id');
    }

    // Define the belongsTo relationship for the module
    public function module()
    {
        return $this->belongsTo(SubjectModule::class, 'module_id');
    }

    // Define the belongsTo relationship for the subject
    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }

    // Define the belongsTo relationship for the year
    public function year()
    {
        return $this->belongsTo(Year::class, 'year_id');
    }
}
