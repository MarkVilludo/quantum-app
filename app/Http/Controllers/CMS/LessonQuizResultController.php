<?php

namespace App\Http\Controllers\CMS;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LessonQuizResult;
use App\Models\Quiz;
use App\Models\User;
use Inertia\Inertia;

class LessonQuizResultController extends Controller
{
    public function index(Request $request)
    {
        // Build the query with lesson and user relationships
        $query = LessonQuizResult::with(['user', 'lesson.quizzes']);

        // Filter by lesson name
        if ($request->search) {
            $query->whereHas('lesson', function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%");
            });
        }

        // Filter by user_id
        if ($request->user_id) {
            $query->where('user_id', $request->user_id);
        }

        // Paginate results
        $results = $query->latest()->paginate(10)->withQueryString();

        // Transform results with detailed answers
        $results->getCollection()->transform(function ($result) {
            $detailedAnswers = collect($result->answers)->map(function ($answer) {
                $questionDetail = Quiz::find($answer['quiz_id']);
                return [
                    'quiz_id'    => $answer['quiz_id'],
                    'question'   => $questionDetail?->question ?? 'Unknown question',
                    'selected'   => $answer['selected'],
                    'correct'    => $questionDetail?->correct ?? 'N/A',
                    'is_correct' => $answer['selected'] === ($questionDetail->correct ?? null),
                ];
            });

            return [
                'id'      => $result->id,
                'score'   => $result->score,
                'total'   => $result->total,
                'user'    => [
                    'id'   => $result->user->id,
                    'name' => $result->user->name,
                ],
                'lesson'  => [
                    'id'    => $result->lesson->id,
                    'title' => $result->lesson->name,
                ],
                'answers' => $detailedAnswers,
            ];
        });

        // Only get users who have results in the filtered lesson
        $filteredUserIds = LessonQuizResult::query()
            ->when($request->search, function ($q) use ($request) {
                $q->whereHas('lesson', function ($sub) use ($request) {
                    $sub->where('name', 'like', "%{$request->search}%");
                });
            })
            ->pluck('user_id')
            ->unique();

        $users = User::whereIn('id', $filteredUserIds)->select('id', 'name')->get();

        return Inertia::render('StudentQuizResults/Index', [
            'results' => $results,
            'users'   => $users,
            'filters' => $request->only('search', 'user_id'),
        ]);
    }
}
