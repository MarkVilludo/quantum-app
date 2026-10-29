<?php

namespace App\Http\Controllers\CMS;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\CmsStudentProgressionService;
use App\Services\DevStudentGraduationService;
use App\Services\GraduationService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Inertia\Inertia;

class StudentProgressionController extends Controller
{
    public function __construct(
        private CmsStudentProgressionService $progression,
        private DevStudentGraduationService $devGraduation,
        private GraduationService $graduation,
    ) {
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
            'devProgressionToolsEnabled' => $this->devGraduation->toolsEnabled(),
        ]);
    }

    public function completeAllDev(User $user)
    {
        $viewer = auth()->user();

        if (! in_array($viewer->role, ['admin', 'teacher'], true)) {
            abort(Response::HTTP_FORBIDDEN, 'Only instructors and administrators can use this action.');
        }

        if (! $this->devGraduation->toolsEnabled()) {
            abort(Response::HTTP_FORBIDDEN, 'Dev progression tools are disabled on this environment.');
        }

        if ($user->role !== 'student') {
            return back()->with('error', 'Only student accounts can be marked as fully graduated.');
        }

        $summary = $this->devGraduation->completeAllCurriculumForUser($user);

        return back()->with('success', sprintf(
            'Marked %s as fully graduated: %d lessons, %d module quizzes, %d final quizzes — %d Quantum XP.',
            $user->email,
            $summary['lessons'],
            $summary['module_quizzes'],
            $summary['final_subject_quizzes'],
            $summary['quantum_xp']
        ));
    }

    public function myProgression()
    {
        $viewer = auth()->user();

        if ($viewer->role !== 'student') {
            abort(Response::HTTP_FORBIDDEN, 'This page is for student accounts.');
        }

        return Inertia::render('StudentProgression/MyProgression', $this->progression->progressionPayloadForUser($viewer));
    }

    public function certificate()
    {
        $viewer = auth()->user();

        if ($viewer->role !== 'student') {
            abort(Response::HTTP_FORBIDDEN, 'This certificate is for student accounts.');
        }

        return response($this->graduation->certificateHtml($viewer))
            ->header('Content-Type', 'text/html; charset=UTF-8');
    }
}
