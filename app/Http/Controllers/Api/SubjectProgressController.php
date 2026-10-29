<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use App\Services\LessonProgressService;
use Illuminate\Http\Response;

class SubjectProgressController extends Controller
{
    public function __construct(private LessonProgressService $progress)
    {
    }

    public function show(int $subjectId)
    {
        $userId = auth()->id();
        $subject = Subject::find($subjectId);
        if (!$subject) {
            return response()->json(['success' => false, 'message' => 'Subject not found.'], Response::HTTP_NOT_FOUND);
        }

        if (!$this->progress->isYearUnlocked($userId, (int) $subject->year_id)) {
            return response()->json([
                'success' => false,
                'message' => $this->progress->yearUnlockMessage($userId, (int) $subject->year_id)
                    ?? 'This year level is locked.',
            ], Response::HTTP_FORBIDDEN);
        }

        return response()->json([
            'success' => true,
            'data' => $this->progress->subjectProgress($userId, $subject),
        ], Response::HTTP_OK);
    }
}
