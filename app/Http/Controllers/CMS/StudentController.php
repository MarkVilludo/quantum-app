<?php

namespace App\Http\Controllers\CMS;

use App\Http\Controllers\Controller;
use App\Services\CmsStudentQuizResultsService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Inertia\Inertia;

class StudentController extends Controller
{
    public function __construct(private CmsStudentQuizResultsService $quizResults)
    {
    }

    public function index(Request $request)
    {
        $viewer = auth()->user();

        if ($viewer->role !== 'student') {
            abort(Response::HTTP_FORBIDDEN, 'This page is for student accounts.');
        }

        $filters = $request->only(['search', 'quiz_type', 'subject_id']);
        $filters['user_id'] = (string) $viewer->id;

        $results = $this->quizResults->paginate($request, $viewer);
        $filterOptions = $this->quizResults->filterOptions($viewer, $filters);

        $results->setCollection(
            $results->getCollection()->map(fn (array $row) => $this->legacyStudentRowShape($row))
        );

        return Inertia::render('LoggedStudent/Index', [
            'results' => $results,
            'filters' => $filters,
            'filterOptions' => $filterOptions,
        ]);
    }

    /** Keeps older compiled JS working until `npm run build` is run. */
    private function legacyStudentRowShape(array $row): array
    {
        $moduleLabel = ($row['module_name'] ?? '—') !== '—'
            ? $row['module_name']
            : (($row['subject_name'] ?? 'Subject') . ' — ' . ($row['quiz_type_label'] ?? 'Quiz'));

        $row['lesson'] = ['name' => $moduleLabel, 'title' => $moduleLabel];

        if (!empty($row['answers']) && is_array($row['answers'])) {
            $row['answers'] = array_map(function (array $answer) {
                $answer['question_title'] = $answer['question'] ?? $answer['question_title'] ?? 'Question';

                return $answer;
            }, $row['answers']);
        }

        return $row;
    }
}
