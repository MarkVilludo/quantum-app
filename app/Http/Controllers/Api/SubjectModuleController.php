<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{SubjectModule, LessonQuizResult};
use Illuminate\Http\Response;
use App\Http\Resources\ModuleResource;

class SubjectModuleController extends Controller
{
    public function show(Request $request, $subjectId)
    {
        $userId = auth()->id();

        $modules = SubjectModule::with(['lessons' => function ($query) {
            $query->orderBy('id'); // You may adjust to created_at if needed
        }])
            ->where('subject_id', $subjectId)
            ->get();

        $previousModuleCompleted = true;

        foreach ($modules as $index => $module) {
            // Unlock if first module or previous module completed
            $module->is_unlocked = $index === 0 || $previousModuleCompleted;

            $lessons = $module->lessons;

            // Reset flag
            $previousModuleCompleted = false;

            if ($lessons->count()) {
                $lastLesson = $lessons->last();

                // Check if user finished last lesson of this module
                $previousModuleCompleted = LessonQuizResult::where('user_id', $userId)
                    ->where('lesson_id', $lastLesson->id)
                    ->exists();
            }

            // You can also add `is_unlocked` on each lesson if needed
            foreach ($lessons as $lessonIndex => $lesson) {
                $lesson->is_unlocked = $module->is_unlocked && ($lessonIndex === 0 || LessonQuizResult::where('user_id', $userId)->where('lesson_id', $lessons[$lessonIndex - 1]->id)->exists());
            }
        }

        return $modules;
    }
}
