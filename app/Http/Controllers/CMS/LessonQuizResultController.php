<?php

namespace App\Http\Controllers\CMS;

use App\Http\Controllers\Controller;
use App\Services\CmsStudentQuizResultsService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Inertia\Inertia;

class LessonQuizResultController extends Controller
{
    public function __construct(private CmsStudentQuizResultsService $quizResults)
    {
    }

    public function index(Request $request)
    {
        $viewer = auth()->user();

        if (!in_array($viewer->role, ['admin', 'teacher'], true)) {
            abort(Response::HTTP_FORBIDDEN, 'Only instructors and administrators can view student quiz results.');
        }

        $filters = $request->only(['search', 'user_id', 'year_id', 'subject_id', 'module_id', 'quiz_type']);
        if ($viewer->role === 'teacher' && $viewer->year_id) {
            $filters['year_id'] = (string) $viewer->year_id;
        }

        $results = $this->quizResults->paginate($request, $viewer);
        $filterOptions = $this->quizResults->filterOptions($viewer, $filters);

        return Inertia::render('StudentQuizResults/Index', [
            'results' => $results,
            'filters' => $filters,
            'filterOptions' => $filterOptions,
            'viewerRole' => $viewer->role,
        ]);
    }
}
