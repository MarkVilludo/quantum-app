<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Lesson;
use App\Models\Quiz;
use App\Services\LessonProgressService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class LessonActivityController extends Controller
{
    public function __construct(private LessonProgressService $progress)
    {
    }

    public function submitNovice(Request $request)
    {
        $validated = $request->validate([
            'lesson_id' => 'required|exists:module_lessons,id',
            'answer' => 'required|string',
        ]);

        $userId = auth()->id();
        $lesson = Lesson::findOrFail($validated['lesson_id']);
        $this->assertLessonAccessible($userId, $lesson->id, $lesson->module_id);

        $activities = $lesson->activity ?? [];
        if (empty($activities)) {
            return response()->json([
                'success' => false,
                'message' => 'No activity configured for this lesson.',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $activity = $activities[0];
        $expected = trim((string) ($activity['correct_answer'] ?? ''));
        $given = trim($validated['answer']);

        if (strcasecmp($given, $expected) !== 0) {
            return response()->json([
                'success' => false,
                'correct' => false,
                'message' => 'Incorrect answer. Review the lesson and try again.',
            ], Response::HTTP_OK);
        }

        $this->progress->markLessonCompleted($userId, $lesson->id, 'novice');

        return response()->json([
            'success' => true,
            'correct' => true,
            'message' => 'Activity completed.',
        ], Response::HTTP_OK);
    }

    public function submitQuizMode(Request $request)
    {
        $validated = $request->validate([
            'lesson_id' => 'required|exists:module_lessons,id',
            'quiz_id' => 'required|exists:lesson_quizzes,id',
            'selected' => 'required|string',
        ]);

        $userId = auth()->id();
        $lesson = Lesson::findOrFail($validated['lesson_id']);
        $this->assertLessonAccessible($userId, $lesson->id, $lesson->module_id);

        $quiz = Quiz::where('lesson_id', $lesson->id)->where('id', $validated['quiz_id'])->first();
        if (!$quiz) {
            return response()->json([
                'success' => false,
                'message' => 'Quiz question not found for this lesson.',
            ], Response::HTTP_NOT_FOUND);
        }

        $expected = $this->resolveCorrectAnswerText($quiz);
        if ($expected !== $validated['selected']) {
            return response()->json([
                'success' => false,
                'correct' => false,
                'message' => 'Incorrect answer. Review the lesson and try again.',
            ], Response::HTTP_OK);
        }

        $this->progress->markLessonCompleted($userId, $lesson->id, 'quiz');

        return response()->json([
            'success' => true,
            'correct' => true,
            'message' => 'Activity completed.',
        ], Response::HTTP_OK);
    }

    private function resolveCorrectAnswerText(Quiz $quiz): string
    {
        $key = strtolower((string) $quiz->correct);
        return match ($key) {
            'answer1' => $quiz->answer1,
            'answer2' => $quiz->answer2,
            'answer3' => $quiz->answer3,
            'answer4' => $quiz->answer4,
            default => (string) $quiz->correct,
        };
    }

    private function assertLessonAccessible(int $userId, int $lessonId, int $moduleId): void
    {
        $lessons = $this->progress->decorateModuleLessons($userId, $moduleId, true);
        $target = collect($lessons)->firstWhere('id', $lessonId);

        if (!$target || empty($target['can_access'])) {
            abort(Response::HTTP_FORBIDDEN, 'This lesson is locked.');
        }
    }
}
