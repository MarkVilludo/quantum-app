<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Lesson;
use App\Models\Quiz;
use App\Models\User;
use App\Services\LessonProgressService;
use App\Services\StudentEngagementService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class LessonActivityController extends Controller
{
    public function __construct(
        private LessonProgressService $progress,
        private StudentEngagementService $engagement,
    ) {
    }

    public function submitMicroCheck(Request $request)
    {
        $validated = $request->validate([
            'lesson_id' => 'required|exists:module_lessons,id',
            'slide_step' => 'required|integer|min:1',
            'selected' => 'required|string',
        ]);

        $userId = auth()->id();
        $lesson = Lesson::findOrFail($validated['lesson_id']);
        $this->assertLessonAccessible($userId, $lesson->id, $lesson->module_id);

        $decorated = $this->progress->decorateModuleLessons($userId, $lesson->module_id, true);
        $lessonRow = collect($decorated)->firstWhere('id', $lesson->id);
        if (!$lessonRow) {
            return response()->json(['success' => false, 'message' => 'Lesson not found.'], Response::HTTP_NOT_FOUND);
        }
        $slide = collect($lessonRow['data'] ?? [])->firstWhere('step', (int) $validated['slide_step']);
        $check = $slide['micro_check'] ?? null;

        if (!$check) {
            return response()->json(['success' => true, 'correct' => true], Response::HTTP_OK);
        }

        $correct = trim($validated['selected']) === trim((string) ($check['correct'] ?? ''));

        return response()->json([
            'success' => true,
            'correct' => $correct,
            'message' => $correct ? 'Nice! Keep going.' : 'Re-read this slide, then try again.',
            'correct_answer' => $check['correct'] ?? '',
        ], Response::HTTP_OK);
    }

    public function submitNovice(Request $request)
    {
        $validated = $request->validate([
            'lesson_id' => 'required|exists:module_lessons,id',
            'answer' => 'required|string',
            'used_hint' => 'sometimes|boolean',
        ]);

        $userId = auth()->id();
        $user = User::findOrFail($userId);
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
        $type = strtolower((string) ($activity['type'] ?? 'text'));
        $given = trim($validated['answer']);
        $correct = $this->evaluateActivityAnswer($activity, $type, $given);

        if (!$correct) {
            return response()->json([
                'success' => false,
                'correct' => false,
                'message' => 'Incorrect answer. Review the lesson and try again.',
                'hint' => $activity['hint'] ?? null,
            ], Response::HTTP_OK);
        }

        $firstCompletion = !$this->progress->isLessonCompleted($userId, $lesson->id);
        $this->progress->markLessonCompleted($userId, $lesson->id, 'novice');
        $xp = $this->engagement->recordLessonCompleted($user, $firstCompletion);

        return response()->json([
            'success' => true,
            'correct' => true,
            'message' => 'Activity completed.',
            'celebration' => [
                'title' => !empty($xp['leveled_up']) ? 'Level up!' : 'Lesson complete!',
                'body' => $this->engagement->celebrationBody($xp['xp_awarded'], $xp),
            ],
            'engagement' => $xp,
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
        $user = User::findOrFail($userId);
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
                'correct_answer' => $expected,
            ], Response::HTTP_OK);
        }

        $firstCompletion = !$this->progress->isLessonCompleted($userId, $lesson->id);
        $this->progress->markLessonCompleted($userId, $lesson->id, 'quiz');
        $xp = $this->engagement->recordLessonCompleted($user, $firstCompletion);

        return response()->json([
            'success' => true,
            'correct' => true,
            'message' => 'Activity completed.',
            'celebration' => [
                'title' => !empty($xp['leveled_up']) ? 'Level up!' : 'Lesson complete!',
                'body' => $this->engagement->celebrationBody($xp['xp_awarded'], $xp),
            ],
            'engagement' => $xp,
        ], Response::HTTP_OK);
    }

    private function evaluateActivityAnswer(array $activity, string $type, string $given): bool
    {
        $expected = $this->normalizeActivityAnswer((string) ($activity['correct_answer'] ?? ''));
        $givenNorm = $this->normalizeActivityAnswer($given);

        return strcasecmp($givenNorm, $expected) === 0;
    }

    private function normalizeActivityAnswer(string $value): string
    {
        $value = trim($value);
        $value = str_replace(
            ["\u{2018}", "\u{2019}", "\u{201C}", "\u{201D}", "\u{00A0}"],
            ["'", "'", '"', '"', ' '],
            $value
        );

        return preg_replace('/\s+/', ' ', $value) ?? $value;
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
