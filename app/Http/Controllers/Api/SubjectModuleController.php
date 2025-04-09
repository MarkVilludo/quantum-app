<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SubjectModule;
use Illuminate\Http\Response;

class SubjectModuleController extends Controller
{
    public function show($subject)
    {
        $subjects = SubjectModule::where('subject_id', $subject)->get();

        if ($subjects->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No subjects modules found for the given subject.',
                'data' => []
            ], Response::HTTP_NOT_FOUND);
        }

        return response()->json([
            'success' => true,
            'message' => 'Subjects modules retrieved successfully.',
            'data' => $subjects
        ], Response::HTTP_OK);
    }
}
