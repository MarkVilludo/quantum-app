<?php

namespace App\Services;

use App\Models\Lesson;
use App\Models\LessonCompletion;
use App\Models\ModuleQuizResult;
use App\Models\Quiz;
use App\Models\Subject;
use App\Models\SubjectModule;
use App\Models\SubjectQuizResult;
use App\Models\Year;
use Illuminate\Support\Collection;

class LessonProgressService
{
    public function __construct(private LessonSlidePresenter $slidePresenter)
    {
    }
    public function isLessonCompleted(int $userId, int $lessonId): bool
    {
        return LessonCompletion::where('user_id', $userId)
            ->where('lesson_id', $lessonId)
            ->exists();
    }

    public function markLessonCompleted(int $userId, int $lessonId, string $mode): LessonCompletion
    {
        return LessonCompletion::firstOrCreate(
            ['user_id' => $userId, 'lesson_id' => $lessonId],
            ['mode' => $mode]
        );
    }

    /** @return Collection<int, Lesson> */
    public function orderedLessonsForModule(int $moduleId): Collection
    {
        return Lesson::where('module_id', $moduleId)->orderBy('id')->get();
    }

    public function areModuleLessonsComplete(int $userId, SubjectModule $module): bool
    {
        $lessons = $this->orderedLessonsForModule($module->id);
        if ($lessons->isEmpty()) {
            return false;
        }

        foreach ($lessons as $lesson) {
            if (!$this->isLessonCompleted($userId, $lesson->id)) {
                return false;
            }
        }

        return true;
    }

    public function hasPassedModuleQuiz(int $userId, int $moduleId): bool
    {
        return ModuleQuizResult::where('user_id', $userId)
            ->where('module_id', $moduleId)
            ->where('passed', true)
            ->exists();
    }

    public function isModuleCompleted(int $userId, SubjectModule $module): bool
    {
        return $this->areModuleLessonsComplete($userId, $module)
            && $this->hasPassedModuleQuiz($userId, $module->id);
    }

    public function isModuleQuizReady(int $userId, int $moduleId): bool
    {
        $module = SubjectModule::find($moduleId);
        if (!$module) {
            return false;
        }

        return $this->areModuleLessonsComplete($userId, $module)
            && !$this->hasPassedModuleQuiz($userId, $moduleId);
    }

    public function storeModuleQuizResult(
        int $userId,
        int $moduleId,
        int $score,
        int $total,
        bool $passed,
        array $answers
    ): ModuleQuizResult {
        return ModuleQuizResult::updateOrCreate(
            ['user_id' => $userId, 'module_id' => $moduleId],
            [
                'score' => $score,
                'total' => $total,
                'passed' => $passed,
                'answers' => $answers,
            ]
        );
    }

    public function resetModuleLessonProgress(int $userId, int $moduleId): void
    {
        $lessonIds = $this->orderedLessonsForModule($moduleId)->pluck('id');
        LessonCompletion::where('user_id', $userId)
            ->whereIn('lesson_id', $lessonIds)
            ->delete();
    }

    public function moduleQuizQuestions(int $moduleId, int $limit = 15): Collection
    {
        $lessonIds = Lesson::where('module_id', $moduleId)->orderBy('id')->pluck('id');

        return Quiz::whereIn('lesson_id', $lessonIds)
            ->orderBy('lesson_id')
            ->orderBy('id')
            ->limit($limit)
            ->get();
    }

    public function isSubjectModulesCompleted(int $userId, int $subjectId): bool
    {
        $modules = SubjectModule::where('subject_id', $subjectId)->orderBy('id')->get();
        foreach ($modules as $module) {
            if (!$this->isModuleCompleted($userId, $module)) {
                return false;
            }
        }

        return $modules->isNotEmpty();
    }

    /** @return Collection<int, Year> */
    public function orderedYears(): Collection
    {
        return Year::query()->orderBy('id')->get();
    }

    public function isYearCompleted(int $userId, int $yearId): bool
    {
        $subjects = Subject::where('year_id', $yearId)->orderBy('id')->get();
        if ($subjects->isEmpty()) {
            return false;
        }

        foreach ($subjects as $subject) {
            if (!$this->isSubjectModulesCompleted($userId, $subject->id)) {
                return false;
            }
        }

        return true;
    }

    public function isYearUnlocked(int $userId, int $yearId): bool
    {
        $years = $this->orderedYears();
        if ($years->isEmpty()) {
            return false;
        }

        $firstYearId = (int) $years->first()->id;
        if ((int) $yearId === $firstYearId) {
            return true;
        }

        $previousYearId = null;
        foreach ($years as $year) {
            if ((int) $year->id === (int) $yearId) {
                return $previousYearId !== null && $this->isYearCompleted($userId, $previousYearId);
            }
            $previousYearId = (int) $year->id;
        }

        return false;
    }

    public function galaxyYearsForUser(int $userId): array
    {
        return $this->orderedYears()->map(function (Year $year) use ($userId) {
            return [
                'id' => $year->id,
                'name' => $year->name,
                'description' => '',
                'is_unlocked' => $this->isYearUnlocked($userId, $year->id),
                'is_completed' => $this->isYearCompleted($userId, $year->id),
            ];
        })->values()->all();
    }

    public function isFinalSubjectQuizUnlocked(int $userId, int $subjectId): bool
    {
        return $this->isSubjectModulesCompleted($userId, $subjectId);
    }

    public function hasPassedFinalSubjectQuiz(int $userId, int $subjectId): bool
    {
        return SubjectQuizResult::where('user_id', $userId)
            ->where('subject_id', $subjectId)
            ->where('passed', true)
            ->exists();
    }

    public function decorateModuleLessons(int $userId, int $moduleId, bool $moduleUnlocked): array
    {
        $lessons = $this->orderedLessonsForModule($moduleId);
        $result = [];

        foreach ($lessons as $index => $lesson) {
            $completed = $this->isLessonCompleted($userId, $lesson->id);
            $priorDone = $index === 0 || $this->isLessonCompleted($userId, $lessons[$index - 1]->id);
            $canAccess = $moduleUnlocked && $priorDone && !$completed;

            $slides = $this->slidePresenter->normalizeSlides($lesson->data, $lesson->name);

            $result[] = [
                'id' => $lesson->id,
                'name' => $lesson->name,
                'module_id' => $lesson->module_id,
                'subject_id' => $lesson->subject_id,
                'year_id' => $lesson->year_id,
                'data' => $slides,
                'slide_count' => count($slides),
                'activity' => $lesson->activity,
                'is_completed' => $completed,
                'is_unlocked' => $canAccess,
                'can_access' => $canAccess,
            ];
        }

        return $result;
    }

    public function firstIncompleteLessonIndex(array $decoratedLessons): ?int
    {
        foreach ($decoratedLessons as $index => $lesson) {
            if (empty($lesson['is_completed']) && !empty($lesson['can_access'])) {
                return $index;
            }
        }

        return null;
    }

    public function subjectProgress(int $userId, Subject $subject): array
    {
        $modules = SubjectModule::where('subject_id', $subject->id)->orderBy('id')->get();
        $previousModuleCompleted = true;
        $moduleRows = [];

        foreach ($modules as $index => $module) {
            $isUnlocked = $index === 0 || $previousModuleCompleted;
            $isCompleted = $this->isModuleCompleted($userId, $module);
            $lessonsComplete = $this->areModuleLessonsComplete($userId, $module);
            $moduleRows[] = [
                'id' => $module->id,
                'name' => $module->name,
                'is_unlocked' => $isUnlocked,
                'is_completed' => $isCompleted,
                'lessons_complete' => $lessonsComplete,
                'module_quiz_ready' => $lessonsComplete && !$this->hasPassedModuleQuiz($userId, $module->id),
            ];
            $previousModuleCompleted = $isCompleted;
        }

        return [
            'subject_id' => $subject->id,
            'modules' => $moduleRows,
            'final_quiz_unlocked' => $this->isFinalSubjectQuizUnlocked($userId, $subject->id),
            'final_quiz_passed' => $this->hasPassedFinalSubjectQuiz($userId, $subject->id),
        ];
    }
}
