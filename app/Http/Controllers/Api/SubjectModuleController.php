<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use App\Models\SubjectModule;
use App\Services\LessonProgressService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class SubjectModuleController extends Controller
{
    public function __construct(private LessonProgressService $progress)
    {
    }

    public function show(Request $request, $subjectId)
    {
        $userId = auth()->id();
        $subject = Subject::find($subjectId);

        if (!$subject) {
            return response()->json([
                'success' => false,
                'message' => 'Subject not found.',
                'data' => [],
            ], Response::HTTP_NOT_FOUND);
        }

        if (!$this->progress->isYearUnlocked($userId, (int) $subject->year_id)) {
            return response()->json([
                'success' => false,
                'message' => $this->progress->yearUnlockMessage($userId, (int) $subject->year_id)
                    ?? 'This year level is locked.',
                'data' => [],
            ], Response::HTTP_FORBIDDEN);
        }

        $modules = SubjectModule::where('subject_id', $subjectId)->orderBy('id')->get();

        $previousModuleCompleted = true;
        $payload = [];

        foreach ($modules as $index => $module) {
            $isUnlocked = $index === 0 || $previousModuleCompleted;
            $lessonsComplete = $this->progress->areModuleLessonsComplete($userId, $module);
            $isCompleted = $this->progress->isModuleCompleted($userId, $module);

            $payload[] = [
                'id' => $module->id,
                'name' => $module->name,
                'subject_id' => $module->subject_id,
                'is_unlocked' => $isUnlocked,
                'is_completed' => $isCompleted,
                'lessons_complete' => $lessonsComplete,
                'module_quiz_ready' => $lessonsComplete && !$this->progress->hasPassedModuleQuiz($userId, $module->id),
            ];

            $previousModuleCompleted = $isCompleted;
        }

        return response()->json([
            'success' => true,
            'message' => 'Subject modules retrieved successfully.',
            'data' => $payload,
        ], Response::HTTP_OK);
    }
}
