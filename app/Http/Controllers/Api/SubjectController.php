<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use App\Services\LessonProgressService;
use App\Services\StudentEngagementService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class SubjectController extends Controller
{
    public function __construct(
        private StudentEngagementService $engagement,
        private LessonProgressService $progress,
    ) {
    }

    public function index(Request $request, $year)
    {
        $userId = $request->user()?->id;
        if ($userId === null) {
            return response()->json([
                'success' => false,
                'message' => 'Authentication required.',
                'data' => [],
            ], Response::HTTP_UNAUTHORIZED);
        }

        $yearId = (int) $year;

        if (!$this->progress->isYearUnlocked($userId, $yearId)) {
            return response()->json([
                'success' => false,
                'message' => $this->progress->yearUnlockMessage($userId, $yearId)
                    ?? 'This year level is locked.',
                'data' => [],
            ], Response::HTTP_FORBIDDEN);
        }

        $subjects = Subject::where('year_id', $yearId)->get();

        if ($subjects->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No subjects found for the given year.',
                'data' => [],
            ], Response::HTTP_NOT_FOUND);
        }

        $payload = $subjects->map(function (Subject $subject) use ($userId) {
            return [
                'id' => $subject->id,
                'name' => $subject->name,
                'year_id' => $subject->year_id,
                'mission' => $this->engagement->defaultMissionForSubject($subject),
                'final_quiz_unlocked' => $this->progress->isFinalSubjectQuizUnlocked($userId, $subject->id),
                'final_quiz_passed' => $this->progress->hasPassedFinalSubjectQuiz($userId, $subject->id),
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'Subjects retrieved successfully.',
            'data' => $payload,
        ], Response::HTTP_OK);
    }
}
