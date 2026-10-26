<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Lesson;
use Illuminate\Http\Response;

class LessonQuizController extends Controller
{
    public function index($lessonId)
    {
        $lesson = Lesson::with('quizzes')->find($lessonId);

        if (!$lesson) {
            return response()->json([
                'success' => false,
                'message' => 'Lesson not found.',
                'data' => [],
            ], Response::HTTP_NOT_FOUND);
        }

        return response()->json([
            'success' => true,
            'message' => 'Lesson quizzes retrieved successfully.',
            'data' => $lesson->quizzes,
        ], Response::HTTP_OK);
    }
}
