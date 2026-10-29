<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Year;
use App\Services\LessonProgressService;
use App\Services\StudentEngagementService;
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

    public function galaxy(LessonProgressService $progress, StudentEngagementService $engagement)
    {
        $userId = auth()->id();
        $years = $progress->galaxyYearsForUser($userId);
        $summary = collect($engagement->yearProgressSummary($userId))->keyBy('year_id');

        $data = collect($years)->map(function (array $year) use ($summary) {
            $extra = $summary->get($year['id'], []);

            return array_merge($year, [
                'modules_completed' => $extra['modules_completed'] ?? 0,
                'modules_total' => $extra['modules_total'] ?? 0,
                'progress_percent' => $extra['percent'] ?? 0,
            ]);
        })->values()->all();

        return response()->json([
            'success' => true,
            'message' => 'Learning galaxy years retrieved successfully.',
            'data' => $data,
        ], Response::HTTP_OK);
    }
}
