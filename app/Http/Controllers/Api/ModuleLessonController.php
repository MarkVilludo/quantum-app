<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Lesson;
use Illuminate\Http\Response;

class ModuleLessonController extends Controller
{
    public function show($moduleId)
    {
        $moduleLessons = Lesson::where('module_id', $moduleId)->with('quizzes')->get();

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
