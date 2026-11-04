<?php

namespace App\Services;

use App\Http\Controllers\Api\ModuleQuizController;
use App\Models\Lesson;
use App\Models\LessonCompletion;
use App\Models\ModuleQuizResult;
use App\Models\SubjectModule;
use Illuminate\Support\Facades\Cache;

class CurriculumXpService
{
    public function __construct(
        private QuantumRankService $ranks,
        private LessonProgressService $progress,
    ) {
    }

    /** Total Quantum XP when every lesson and module quiz in the CMS is done once. */
    public function maxEarnableXp(): int
    {
        return $this->ranks->maxTotalXp();
    }

    /** Lessons + module quizzes (first-time completions that award XP). */
    public function totalEngagementUnits(): int
    {
        return (int) Cache::remember(
            'curriculum.engagement_units',
            now()->addHour(),
            fn () => $this->countEngagementUnitsUncached()
        );
    }

    public function clearCachedTotals(): void
    {
        Cache::forget('curriculum.engagement_units');
    }

    public function completedEngagementUnitsForUser(int $userId): int
    {
        $lessons = LessonCompletion::query()->where('user_id', $userId)->count();
        $quizzes = ModuleQuizResult::query()
            ->where('user_id', $userId)
            ->where('passed', true)
            ->count();

        return $lessons + $quizzes;
    }

    public function quantumXpForUser(int $userId): int
    {
        return $this->xpFromCompletedUnits(
            $this->completedEngagementUnitsForUser($userId)
        );
    }

    public function xpFromCompletedUnits(int $completedUnits): int
    {
        $completedUnits = max(0, $completedUnits);
        $totalUnits = $this->totalEngagementUnits();

        if ($totalUnits === 0) {
            return 0;
        }

        $budget = $this->maxEarnableXp();

        if ($completedUnits >= $totalUnits) {
            return $budget;
        }

        $base = intdiv($budget, $totalUnits);
        $remainder = $budget % $totalUnits;
        $highCount = min($completedUnits, $remainder);

        return $highCount * ($base + 1) + ($completedUnits - $highCount) * $base;
    }

    /** @return array{completed: int, total: int, percent: int} */
    public function curriculumProgressForUser(int $userId): array
    {
        $total = $this->totalEngagementUnits();
        $completed = min($this->completedEngagementUnitsForUser($userId), $total);

        if ($total === 0) {
            return ['completed' => 0, 'total' => 0, 'percent' => 0];
        }

        $percent = $completed >= $total
            ? 100
            : (int) floor(($completed / $total) * 100);

        return [
            'completed' => $completed,
            'total' => $total,
            'percent' => min(100, max(0, $percent)),
        ];
    }

    /** Typical XP per lesson/quiz when the curriculum is fully completed (for UI copy). */
    public function indicativeUnitXp(): int
    {
        $total = $this->totalEngagementUnits();

        return $total > 0 ? max(1, (int) round($this->maxEarnableXp() / $total)) : 0;
    }

    /** @return list<array{label: string, xp: string}> */
    public function xpRewardLabels(): array
    {
        $unit = $this->indicativeUnitXp();

        return [
            [
                'label' => 'Complete a lesson (first time)',
                'xp' => $unit > 0 ? (string) $unit : '—',
            ],
            [
                'label' => 'Pass a module quiz (first time)',
                'xp' => $unit > 0 ? (string) $unit : '—',
            ],
        ];
    }

    public function syncUserQuantumXp(int $userId): int
    {
        $xp = $this->quantumXpForUser($userId);

        \App\Models\User::query()->whereKey($userId)->update(['quantum_xp' => $xp]);

        return $xp;
    }

    private function countEngagementUnitsUncached(): int
    {
        $lessons = Lesson::query()->count();
        $moduleQuizzes = 0;

        SubjectModule::query()->orderBy('id')->each(function (SubjectModule $module) use (&$moduleQuizzes) {
            $count = $this->progress
                ->moduleQuizQuestions($module->id, ModuleQuizController::MODULE_QUIZ_QUESTION_COUNT)
                ->count();
            if ($count > 0) {
                $moduleQuizzes++;
            }
        });

        return $lessons + $moduleQuizzes;
    }
}
