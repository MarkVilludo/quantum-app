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
        $subject = Subject::find($subjectId);
        if (!$subject) {
            return response()->json(['success' => false, 'message' => 'Subject not found.'], Response::HTTP_NOT_FOUND);
        }

        return response()->json([
            'success' => true,
            'data' => $this->progress->subjectProgress(auth()->id(), $subject),
        ], Response::HTTP_OK);
    }
}
