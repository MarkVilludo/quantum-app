<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SubjectModule extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['name', 'subject_id'];


    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }

    public function lessons()
    {
        return $this->hasMany(Lesson::class, 'module_id');
    }
}
