<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Lesson;
use App\Services\LessonSlidePresenter;
use Illuminate\Http\Response;

class ModuleLessonController extends Controller
{
    public function __construct(private LessonSlidePresenter $slidePresenter)
    {
    }

    public function show($moduleId)
    {
        $moduleLessons = Lesson::where('module_id', $moduleId)
            ->with('quizzes')
            ->orderBy('id')
            ->get()
            ->map(function (Lesson $lesson) {
                $slides = $this->slidePresenter->normalizeSlides($lesson->data, $lesson->name);
                $lesson->setAttribute('data', $slides);
                $lesson->setAttribute('slide_count', count($slides));

                return $lesson;
            });

        if ($moduleLessons->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No lessons found for the given module.',
                'data' => []
            ], Response::HTTP_NOT_FOUND);
        }

        return response()->json([
            'success' => true,
            'message' => 'Module lessons retrieved successfully.',
            'data' => $moduleLessons
        ], Response::HTTP_OK);
    }
}
