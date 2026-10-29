<?php

namespace App\Http\Controllers\CMS;

use App\Http\Controllers\Controller;
use App\Services\CmsStudentProgressionService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Inertia\Inertia;

class StudentProgressionController extends Controller
{
    public function __construct(private CmsStudentProgressionService $progression)
    {
    }

    public function index(Request $request)
    {
        $viewer = auth()->user();

        if (!in_array($viewer->role, ['admin', 'teacher'], true)) {
            abort(Response::HTTP_FORBIDDEN, 'Only instructors and administrators can view student progression.');
        }

        $filters = $request->only(['search', 'year_id']);

        return Inertia::render('StudentProgression/Index', [
            'students' => $this->progression->paginateStudents($request, $viewer),
            'filters' => $filters,
            'filterOptions' => $this->progression->filterOptions($viewer),
            'viewerRole' => $viewer->role,
        ]);
    }

    public function myProgression()
    {
        $viewer = auth()->user();

        if ($viewer->role !== 'student') {
            abort(Response::HTTP_FORBIDDEN, 'This page is for student accounts.');
        }

        return Inertia::render('StudentProgression/MyProgression', $this->progression->progressionPayloadForUser($viewer));
    }
}
