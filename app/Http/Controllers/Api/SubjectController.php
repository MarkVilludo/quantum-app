<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Subject;
use Illuminate\Http\Response;

class SubjectController extends Controller
{
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

        return response()->json([
            'success' => true,
            'message' => 'Subjects retrieved successfully.',
            'data' => $subjects
        ], Response::HTTP_OK);
    }
}
