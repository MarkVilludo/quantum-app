<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SubjectModule;
use App\Services\LessonProgressService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ModuleProgressController extends Controller
{
    public function __construct(private LessonProgressService $progress)
    {
    }

    public function show(Request $request, int $moduleId)
    {
        $userId = auth()->id();
        $module = SubjectModule::with('subject')->find($moduleId);

        if (!$module) {
            return response()->json([
                'success' => false,
                'message' => 'Module not found.',
            ], Response::HTTP_NOT_FOUND);
        }

        $subjectId = $module->subject_id;
        $subjectProgress = $this->progress->subjectProgress($userId, $module->subject);
        $moduleMeta = collect($subjectProgress['modules'])->firstWhere('id', $moduleId);
        $moduleUnlocked = $moduleMeta['is_unlocked'] ?? false;

        if (!$moduleUnlocked) {
            return response()->json([
                'success' => false,
                'message' => 'This module is locked. Complete the previous module first.',
                'data' => [],
            ], Response::HTTP_FORBIDDEN);
        }

        $lessons = $this->progress->decorateModuleLessons($userId, $moduleId, true);
        $nextIndex = $this->progress->firstIncompleteLessonIndex($lessons);
        $lessonsComplete = $this->progress->areModuleLessonsComplete($userId, $module);
        $quizPassed = $this->progress->hasPassedModuleQuiz($userId, $moduleId);

        return response()->json([
            'success' => true,
            'message' => 'Module progress retrieved.',
            'module' => [
                'id' => $module->id,
                'name' => $module->name,
                'subject_id' => $subjectId,
                'is_completed' => $this->progress->isModuleCompleted($userId, $module),
                'lessons_complete' => $lessonsComplete,
                'module_quiz_ready' => $lessonsComplete && !$quizPassed,
                'module_quiz_passed' => $quizPassed,
            ],
            'current_lesson_index' => $nextIndex,
            'data' => $lessons,
        ], Response::HTTP_OK);
    }
}
