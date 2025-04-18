<?php

namespace App\Http\Controllers\CMS;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LessonQuizResult;
use App\Models\Quiz;
use App\Models\User;
use Inertia\Inertia;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $userId = $request->user_id; // Get user_id from the request
        $search = $request->search;

        $query = LessonQuizResult::query();

        if (auth()->user()->role === 'student') {
            $query->where('user_id', auth()->user()->id);
        }

        // Optionally, search by lesson name
        if ($search) {
            $query->whereHas('lesson', function ($q) use ($search) {
                $q->where('title', 'like', "%$search%");
            });
        }

        // Paginate the results and load answers with related questions
        $results = $query->with(['lesson', 'user'])
            ->paginate(10);

        // Add the questions and correct answers for each result
        $results->getCollection()->transform(function ($result) {
            // Extract quiz ids from answers JSON field
            $quizIds = collect($result->answers)->pluck('quiz_id')->toArray();

            // Fetch the questions and their correct answers related to the quiz_ids
            $questions = Quiz::whereIn('id', $quizIds)
                ->get(['id', 'question', 'correct'])
                ->keyBy('id'); // Key by question ID
            // Map the answers to include question titles and correct answers
            $result->answers = collect($result->answers)->map(function ($answer) use ($questions) {

                $questionDetails = null;
                foreach ($questions as $question) {
                    if ($question->id == $answer['quiz_id']) {
                        $questionDetails = $question;
                    }
                }

                return [
                    'quiz_id' => $answer['quiz_id'],
                    'selected' => $answer['selected'],
                    'question_title' => $questionDetails ? $questionDetails->question : null,
                    'correct' => $questionDetails ? $questionDetails->correct : null,
                    'is_correct' => $answer['selected'] == $questionDetails->correct ? true : false,
                ];
            });

            return $result;
        });

        return Inertia::render('LoggedStudent/Index', [
            'results' => $results,
            'filters' => $request->only(['search', 'user_id']),
        ]);
    }
}
