<?php

namespace App\Services;

use App\Http\Controllers\Api\ModuleQuizController;
use App\Models\Lesson;
use App\Models\LessonCompletion;
use App\Models\ModuleQuizResult;
use App\Models\Subject;
use App\Models\SubjectModule;
use App\Models\SubjectQuizResult;
use App\Models\User;
use App\Models\Year;
use Illuminate\Support\Facades\DB;

class DevStudentGraduationService
{
    public function __construct(
        private LessonProgressService $progress,
        private GraduationService $graduation,
    ) {
    }

    /**
     * Mark every lesson, module quiz, and final subject quiz as passed for all CMS years/subjects.
     *
     * @return array<string, mixed>
     */
    public function completeAllCurriculumForUser(User $user): array
    {
        return DB::transaction(function () use ($user) {
            $userId = (int) $user->id;
            $summary = [
                'user_id' => $userId,
                'email' => $user->email,
                'years' => 0,
                'subjects' => 0,
                'modules' => 0,
                'lessons' => 0,
                'module_quizzes' => 0,
                'final_subject_quizzes' => 0,
            ];

            $years = Year::query()->orderBy('id')->get();
            $summary['years'] = $years->count();

            foreach ($years as $year) {
                $subjects = Subject::query()->where('year_id', $year->id)->orderBy('id')->get();

                foreach ($subjects as $subject) {
                    $summary['subjects']++;
                    $modules = SubjectModule::query()
                        ->where('subject_id', $subject->id)
                        ->orderBy('id')
                        ->get();

                    foreach ($modules as $module) {
                        $summary['modules']++;
                        $lessons = Lesson::query()
                            ->where('module_id', $module->id)
                            ->orderBy('id')
                            ->get();

                        foreach ($lessons as $lesson) {
                            LessonCompletion::firstOrCreate(
                                ['user_id' => $userId, 'lesson_id' => $lesson->id],
                                ['mode' => 'dev-complete-all']
                            );
                            $summary['lessons']++;
                        }

                        $quizTotal = $this->progress
                            ->moduleQuizQuestions($module->id, ModuleQuizController::MODULE_QUIZ_QUESTION_COUNT)
                            ->count();
                        if ($quizTotal > 0) {
                            ModuleQuizResult::updateOrCreate(
                                ['user_id' => $userId, 'module_id' => $module->id],
                                [
                                    'score' => $quizTotal,
                                    'total' => $quizTotal,
                                    'passed' => true,
                                    'answers' => [],
                                ]
                            );
                            $summary['module_quizzes']++;
                        }
                    }

                    $finalTotal = min(
                        LessonProgressService::FINAL_SUBJECT_QUIZ_QUESTION_COUNT,
                        $this->progress->finalSubjectQuizQuestions($subject->id)->count()
                    );
                    if ($finalTotal > 0) {
                        SubjectQuizResult::updateOrCreate(
                            ['user_id' => $userId, 'subject_id' => $subject->id],
                            [
                                'score' => $finalTotal,
                                'total' => $finalTotal,
                                'passed' => true,
                                'answers' => [],
                            ]
                        );
                        $summary['final_subject_quizzes']++;
                    }

                    $this->progress->clearFinalSubjectQuizAttempt($userId, $subject->id);
                }
            }

            $lessonXp = LessonCompletion::where('user_id', $userId)->count()
                * StudentEngagementService::LESSON_XP;
            $moduleQuizXp = ModuleQuizResult::where('user_id', $userId)
                ->where('passed', true)
                ->count()
                * (50 + 25);

            $user->quantum_xp = $lessonXp + $moduleQuizXp;
            $user->last_activity_date = now()->toDateString();
            $user->save();

            $this->graduation->ensureRecordedForDev($user->fresh());

            $summary['quantum_xp'] = (int) $user->quantum_xp;
            $summary['all_years_completed'] = $years->every(
                fn (Year $year) => $this->progress->isYearCompleted($userId, $year->id)
            );

            return $summary;
        });
    }

    public function toolsEnabled(): bool
    {
        return (bool) config('quantum_dev.progression_tools_enabled', false);
    }
}
