<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Lesson;
use App\Models\Quiz;
use App\Models\Subject;
use App\Models\SubjectModule;
use App\Models\User;
use App\Services\LessonProgressService;
use App\Services\StudentEngagementService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ModuleQuizController extends Controller
{
    public const MODULE_QUIZ_QUESTION_COUNT = 15;

    public const PASS_PERCENT = 60;

    public function __construct(
        private LessonProgressService $progress,
        private StudentEngagementService $engagement,
    ) {
    }

    public function show(int $moduleId)
    {
        $userId = auth()->id();
        $module = SubjectModule::find($moduleId);

        if (!$module) {
            return response()->json(['success' => false, 'message' => 'Module not found.'], Response::HTTP_NOT_FOUND);
        }

        if ($blocked = $this->yearLockedResponse($userId, $module)) {
            return $blocked;
        }

        if (!$this->progress->areModuleLessonsComplete($userId, $module)) {
            return response()->json([
                'success' => false,
                'message' => 'Complete all lessons in this module before taking the module quiz.',
            ], Response::HTTP_FORBIDDEN);
        }

        $quizzes = $this->progress->moduleQuizQuestions($moduleId, self::MODULE_QUIZ_QUESTION_COUNT);

        return response()->json([
            'success' => true,
            'message' => 'Module quiz retrieved.',
            'module' => ['id' => $module->id, 'name' => $module->name],
            'pass_percent' => self::PASS_PERCENT,
            'already_passed' => $this->progress->hasPassedModuleQuiz($userId, $moduleId),
            'data' => $quizzes,
        ], Response::HTTP_OK);
    }

    public function submit(Request $request, int $moduleId)
    {
        $userId = auth()->id();
        $module = SubjectModule::find($moduleId);

        if (!$module) {
            return response()->json(['success' => false, 'message' => 'Module not found.'], Response::HTTP_NOT_FOUND);
        }

        if ($blocked = $this->yearLockedResponse($userId, $module)) {
            return $blocked;
        }

        if (!$this->progress->areModuleLessonsComplete($userId, $module)) {
            return response()->json([
                'success' => false,
                'message' => 'Complete all lessons before submitting the module quiz.',
            ], Response::HTTP_FORBIDDEN);
        }

        $validated = $request->validate([
            'answers' => 'required|array|min:1',
            'answers.*.quiz_id' => 'required|exists:lesson_quizzes,id',
            'answers.*.selected' => 'nullable|string|max:5000',
        ]);

        $lessonIds = Lesson::where('module_id', $moduleId)->pluck('id');
        $quizzes = Quiz::whereIn('lesson_id', $lessonIds)
            ->orderBy('lesson_id')
            ->orderBy('id')
            ->limit(self::MODULE_QUIZ_QUESTION_COUNT)
            ->get()
            ->keyBy('id');

        $score = 0;
        $total = $quizzes->count();
        $details = [];

        foreach ($validated['answers'] as $answer) {
            $quiz = $quizzes->get($answer['quiz_id']);
            if (!$quiz) {
                continue;
            }
            $expected = $this->resolveCorrectAnswerText($quiz);
            $isCorrect = $expected === $answer['selected'];
            if ($isCorrect) {
                $score++;
            }
            $details[] = [
                'quiz_id' => $quiz->id,
                'selected' => $answer['selected'],
                'correct' => $expected,
                'is_correct' => $isCorrect,
            ];
        }

        $passThreshold = $total > 0 ? (int) ceil($total * (self::PASS_PERCENT / 100)) : 0;
        $passed = $score >= $passThreshold;
        $firstPass = $passed && !$this->progress->hasPassedModuleQuiz($userId, $moduleId);

        $this->progress->storeModuleQuizResult($userId, $moduleId, $score, $total, $passed, $details);

        if (!$passed) {
            $this->progress->resetModuleLessonProgress($userId, $moduleId);
        }

        $percent = $total > 0 ? (int) round(($score / $total) * 100) : 0;

        $review = [];
        foreach ($details as $row) {
            $review[] = [
                'quiz_id' => $row['quiz_id'],
                'is_correct' => $row['is_correct'],
                'selected' => $row['selected'],
                'correct' => $row['correct'],
            ];
        }

        $engagement = null;
        $celebration = null;
        if ($passed) {
            $user = User::findOrFail($userId);
            $engagement = $this->engagement->recordModuleQuizPassed($user, $percent, $firstPass);
            $celebration = $this->engagement->celebrationPayload(
                ! empty($engagement['leveled_up']) ? 'Level up!' : 'Module quiz passed!',
                $engagement['xp_awarded'],
                $engagement,
                'quiz_pass'
            );
        }

        return response()->json([
            'success' => true,
            'passed' => $passed,
            'score' => $score,
            'total' => $total,
            'percent' => $percent,
            'pass_percent' => self::PASS_PERCENT,
            'review' => $review,
            'engagement' => $engagement,
            'celebration' => $celebration,
            'message' => $passed
                ? "You passed with {$score}/{$total} ({$percent}%)."
                : "Score {$score}/{$total} ({$percent}%). You need ".self::PASS_PERCENT."% to pass. Repeat the module from the first lesson.",
        ], Response::HTTP_OK);
    }

    private function yearLockedResponse(int $userId, SubjectModule $module): ?\Illuminate\Http\JsonResponse
    {
        $subject = Subject::find($module->subject_id);
        if ($subject && !$this->progress->isYearUnlocked($userId, (int) $subject->year_id)) {
            return response()->json([
                'success' => false,
                'message' => $this->progress->yearUnlockMessage($userId, (int) $subject->year_id)
                    ?? 'This year level is locked.',
            ], Response::HTTP_FORBIDDEN);
        }

        return null;
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
}
