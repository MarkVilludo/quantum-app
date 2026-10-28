<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Lesson;
use App\Models\Quiz;
use App\Models\Subject;
use App\Models\SubjectQuizResult;
use App\Services\LessonProgressService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

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

        if (!$this->progress->isFinalSubjectQuizUnlocked($userId, $subjectId)) {
            return response()->json([
                'success' => false,
                'message' => 'Complete all modules and lesson activities to unlock the final quiz.',
            ], Response::HTTP_FORBIDDEN);
        }

        $lessonIds = Lesson::where('subject_id', $subjectId)->pluck('id');
        $quizzes = Quiz::whereIn('lesson_id', $lessonIds)->orderBy('lesson_id')->orderBy('id')->get();

        return response()->json([
            'success' => true,
            'message' => 'Final subject quiz retrieved.',
            'subject' => ['id' => $subject->id, 'name' => $subject->name],
            'already_passed' => $this->progress->hasPassedFinalSubjectQuiz($userId, $subjectId),
            'data' => $quizzes,
        ], Response::HTTP_OK);
    }

    public function submit(Request $request, int $subjectId)
    {
        $userId = auth()->id();

        if (!$this->progress->isFinalSubjectQuizUnlocked($userId, $subjectId)) {
            return response()->json([
                'success' => false,
                'message' => 'Final quiz is locked.',
            ], Response::HTTP_FORBIDDEN);
        }

        $validated = $request->validate([
            'answers' => 'required|array|min:1',
            'answers.*.quiz_id' => 'required|exists:lesson_quizzes,id',
            'answers.*.selected' => 'required|string',
        ]);

        $lessonIds = Lesson::where('subject_id', $subjectId)->pluck('id');
        $quizzes = Quiz::whereIn('lesson_id', $lessonIds)->get()->keyBy('id');

        $score = 0;
        $total = $quizzes->count();
        $details = [];

        foreach ($validated['answers'] as $answer) {
            $quiz = $quizzes->get($answer['quiz_id']);
            if (!$quiz) {
                continue;
            }
            $expected = match (strtolower((string) $quiz->correct)) {
                'answer1' => $quiz->answer1,
                'answer2' => $quiz->answer2,
                'answer3' => $quiz->answer3,
                'answer4' => $quiz->answer4,
                default => (string) $quiz->correct,
            };
            $isCorrect = $expected === $answer['selected'];
            if ($isCorrect) {
                $score++;
            }
            $details[] = [
                'quiz_id' => $quiz->id,
                'selected' => $answer['selected'],
                'correct' => $quiz->correct,
                'is_correct' => $isCorrect,
            ];
        }

        $passThreshold = $total > 0 ? (int) ceil($total * 0.7) : 0;
        $passed = $score >= $passThreshold;

        SubjectQuizResult::updateOrCreate(
            ['user_id' => $userId, 'subject_id' => $subjectId],
            [
                'score' => $score,
                'total' => $total,
                'passed' => $passed,
                'answers' => $details,
            ]
        );

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
}
