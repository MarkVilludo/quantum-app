<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Lesson;
use App\Models\LessonQuizResult;
use App\Models\Quiz;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LessonQuizResultController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'lesson_id' => 'required|exists:module_lessons,id',
            'answers' => 'required|array',
            'answers.*.quiz_id' => 'required|exists:lesson_quizzes,id',
            'answers.*.selected' => 'required|string',
        ]);

        // Check if the user already has a result for this lesson
        $existing = LessonQuizResult::where('user_id', Auth::id())
            ->where('lesson_id', $validated['lesson_id'])
            ->first();

        if ($existing) {
            return response()->json([
                'message' => 'You have already submitted answers for this lesson.',
                'result' => $existing,
            ], 409); // 409 Conflict
        }

        $score = 0;
        $total = count($validated['answers']);

        foreach ($validated['answers'] as $answer) {
            $quiz = Quiz::find($answer['quiz_id']);
            if ($quiz && $quiz->correct === $answer['selected']) {
                $score++;
            }
        }

        $result = LessonQuizResult::create([
            'user_id' => Auth::id(),
            'lesson_id' => $validated['lesson_id'],
            'score' => $score,
            'total' => $total,
            'answers' => $validated['answers'],
        ]);

        return response()->json([
            'message' => 'Quiz submitted successfully',
            'result' => $result,
        ]);
    }

    public function show($lessonId)
    {
        $result = LessonQuizResult::where('user_id', Auth::id())
            ->where('lesson_id', $lessonId)
            ->latest()
            ->first();

        if (!$result) {
            return response()->json(['message' => 'No result found.'], 404);
        }

        $lesson = Lesson::with(['quizzes'])->findOrFail($lessonId);

        // Map quizzes by ID for faster lookup
        $quizMap = $lesson->quizzes->keyBy('id');

        // Enrich answers with question, correct answer, and is_correct
        $detailedAnswers = collect($result->answers)->map(function ($answer) use ($quizMap) {
            $quiz = $quizMap[$answer['quiz_id']] ?? null;

            return [
                'quiz_id'    => $answer['quiz_id'],
                'question'   => $quiz?->question ?? 'Unknown question',
                'selected'   => $answer['selected'],
                'correct'    => $quiz?->correct ?? 'N/A',
                'is_correct' => $quiz && $quiz->correct === $answer['selected'],
            ];
        });

        return response()->json([
            'result' => [
                'id'        => $result->id,
                'user_id'   => $result->user_id,
                'lesson_id' => $result->lesson_id,
                'score'     => $result->score,
                'total'     => $result->total,
                'answers'   => $detailedAnswers,
            ],
            'lesson' => $lesson->only(['id', 'name']),
        ]);
    }
}
