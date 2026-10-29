<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use App\Services\StudentEngagementService;
use Illuminate\Http\Response;

class SubjectController extends Controller
{
    public function __construct(private StudentEngagementService $engagement)
    {
    }

    public function index($year)
    {
        $subjects = Subject::where('year_id', $year)->get();

        if ($subjects->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No subjects found for the given year.',
                'data' => []
            ], Response::HTTP_NOT_FOUND);
        }

        $payload = $subjects->map(function (Subject $subject) {
            return [
                'id' => $subject->id,
                'name' => $subject->name,
                'year_id' => $subject->year_id,
                'mission' => $this->engagement->defaultMissionForSubject($subject),
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'Subjects retrieved successfully.',
            'data' => $payload,
        ], Response::HTTP_OK);
    }
}
