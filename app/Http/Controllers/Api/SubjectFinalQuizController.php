<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\Subject;
use App\Models\SubjectQuizResult;
use App\Services\LessonProgressService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class SubjectFinalQuizController extends Controller
{
    public function __construct(private LessonProgressService $progress)
    {
    }

    public function show(int $subjectId)
    {
        $userId = auth()->id();
        $subject = Subject::find($subjectId);

        if (!$subject) {
            return response()->json(['success' => false, 'message' => 'Subject not found.'], Response::HTTP_NOT_FOUND);
        }

        if (!$this->progress->isYearUnlocked($userId, (int) $subject->year_id)) {
            return response()->json([
                'success' => false,
                'message' => $this->progress->yearUnlockMessage($userId, (int) $subject->year_id)
                    ?? 'This year level is locked.',
            ], Response::HTTP_FORBIDDEN);
        }

        if (!$this->progress->isFinalSubjectQuizUnlocked($userId, $subjectId)) {
            return response()->json([
                'success' => false,
                'message' => 'Complete all modules and lesson activities to unlock the final quiz.',
            ], Response::HTTP_FORBIDDEN);
        }

        $quizzes = $this->progress->finalSubjectQuizQuestionsForAttempt($userId, $subjectId);

        return response()->json([
            'success' => true,
            'message' => 'Final subject quiz retrieved.',
            'subject' => ['id' => $subject->id, 'name' => $subject->name],
            'already_passed' => $this->progress->hasPassedFinalSubjectQuiz($userId, $subjectId),
            'question_count' => LessonProgressService::FINAL_SUBJECT_QUIZ_QUESTION_COUNT,
            'data' => $quizzes,
        ], Response::HTTP_OK);
    }

    public function submit(Request $request, int $subjectId)
    {
        $userId = auth()->id();
        $subject = Subject::find($subjectId);

        if (!$subject) {
            return response()->json(['success' => false, 'message' => 'Subject not found.'], Response::HTTP_NOT_FOUND);
        }

        if (!$this->progress->isYearUnlocked($userId, (int) $subject->year_id)) {
            return response()->json([
                'success' => false,
                'message' => 'This year level is locked.',
            ], Response::HTTP_FORBIDDEN);
        }

        if (!$this->progress->isFinalSubjectQuizUnlocked($userId, $subjectId)) {
            return response()->json([
                'success' => false,
                'message' => 'Final quiz is locked.',
            ], Response::HTTP_FORBIDDEN);
        }

        try {
            $validated = $request->validate([
                'answers' => 'required|array|min:1|max:'.LessonProgressService::FINAL_SUBJECT_QUIZ_QUESTION_COUNT,
                'answers.*.quiz_id' => 'required|integer|exists:lesson_quizzes,id',
                'answers.*.selected' => 'nullable|string|max:5000',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid final quiz submission.',
                'errors' => $e->errors(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $submittedQuizIds = collect($validated['answers'])->pluck('quiz_id')->map(fn ($id) => (int) $id)->all();
        $quizzes = $this->progress->finalSubjectQuizQuizzesForSubmit($userId, $subjectId, $submittedQuizIds)->keyBy('id');

        if ($quizzes->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Quiz session expired or invalid. Leave and start the final quiz again.',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $invalidIds = collect($submittedQuizIds)->filter(
            fn (int $id) => ! $quizzes->has($id)
        );
        if ($invalidIds->isNotEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Some answers do not belong to this subject\'s final quiz.',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $score = 0;
        $total = $quizzes->count();
        $details = [];

        foreach ($validated['answers'] as $answer) {
            $quiz = $quizzes->get((int) $answer['quiz_id']);
            if (! $quiz) {
                continue;
            }
            $expected = $this->resolveCorrectAnswerText($quiz);
            $selected = (string) ($answer['selected'] ?? '');
            $isCorrect = $selected !== '' && $expected === $selected;
            if ($isCorrect) {
                $score++;
            }
            $details[] = [
                'quiz_id' => $quiz->id,
                'selected' => $selected,
                'correct' => $expected,
                'is_correct' => $isCorrect,
            ];
        }

        $passThreshold = $total > 0 ? (int) ceil($total * 0.7) : 0;
        $passed = $score >= $passThreshold;

        try {
            DB::transaction(function () use ($userId, $subjectId, $score, $total, $passed, $details) {
                SubjectQuizResult::updateOrCreate(
                    ['user_id' => $userId, 'subject_id' => $subjectId],
                    [
                        'score' => $score,
                        'total' => $total,
                        'passed' => $passed,
                        'answers' => $details,
                    ]
                );
            });
        } catch (\Throwable $e) {
            Log::error('Final subject quiz submit failed', [
                'user_id' => $userId,
                'subject_id' => $subjectId,
                'answer_count' => count($details),
                'exception' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Could not save final quiz results. Please try again.',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $this->progress->clearFinalSubjectQuizAttempt($userId, $subjectId);

        return response()->json([
            'success' => true,
            'passed' => $passed,
            'score' => $score,
            'total' => $total,
            'message' => $passed
                ? 'Congratulations! You passed the final subject quiz.'
                : 'Keep studying and try the final quiz again.',
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
}
