<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Year;
use App\Services\LessonProgressService;
use Illuminate\Http\Response;

class YearController extends Controller
{
    public function index()
    {
        $years = Year::query()->orderBy('id')->get();

        return response()->json([
            'success' => true,
            'message' => 'Years retrieved successfully.',
            'data' => $years,
        ], Response::HTTP_OK);
    }

    public function galaxy(LessonProgressService $progress)
    {
        $userId = auth()->id();

        return response()->json([
            'success' => true,
            'message' => 'Learning galaxy years retrieved successfully.',
            'data' => $progress->galaxyYearsForUser($userId),
        ], Response::HTTP_OK);
    }
}
