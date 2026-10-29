<?php

namespace App\Services;

use App\Models\Lesson;
use App\Models\ModuleQuizResult;
use App\Models\Subject;
use App\Models\SubjectModule;
use App\Models\User;
use App\Models\Year;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class StudentEngagementService
{
    public const DAILY_LESSON_GOAL = 1;

    public const LESSON_XP = 15;

    public function __construct(
        private LessonProgressService $progress,
        private QuantumRankService $ranks,
    ) {
    }

    public function recordLessonCompleted(User $user, bool $firstCompletion): array
    {
        $previousXp = (int) $user->quantum_xp;

        if ($firstCompletion) {
            $this->touchDailyActivity($user, true);
            $user->quantum_xp = $previousXp + self::LESSON_XP;
            $user->save();

            return $this->buildEngagementPayload($user, self::LESSON_XP, $previousXp);
        }

        return $this->buildEngagementPayload($user, 0, $previousXp);
    }

    public function recordModuleQuizPassed(User $user, int $percent, bool $firstPass): array
    {
        $previousXp = (int) $user->quantum_xp;

        if (!$firstPass) {
            return $this->buildEngagementPayload($user, 0, $previousXp);
        }

        $this->touchDailyActivity($user, false);
        $bonus = $percent >= 100 ? 25 : ($percent >= 80 ? 15 : 0);
        $xp = 50 + $bonus;
        $user->quantum_xp = $previousXp + $xp;
        $user->save();

        return $this->buildEngagementPayload($user, $xp, $previousXp);
    }

    public function celebrationVariant(?array $engagement, string $default = 'xp'): string
    {
        if (! empty($engagement['leveled_up'])) {
            return 'level_up';
        }

        return $default;
    }

    public function celebrationPayload(string $title, int $xpAwarded, array $engagement, string $defaultVariant = 'xp'): array
    {
        return [
            'title' => $title,
            'body' => $this->celebrationBody($xpAwarded, $engagement),
            'variant' => $this->celebrationVariant($engagement, $defaultVariant),
        ];
    }

    public function celebrationBody(int $xpAwarded, array $engagement): string
    {
        $parts = [];
        if ($xpAwarded > 0) {
            $parts[] = 'You earned '.$xpAwarded.' Quantum XP.';
        }
        if (!empty($engagement['leveled_up'])) {
            $rank = $engagement['rank'];
            $parts[] = 'Level up! You are now '.$rank['title'].' · Level '.$rank['level'].'.';
        }

        return implode(' ', $parts);
    }

    public function dashboard(int $userId): array
    {
        $user = User::findOrFail($userId);
        $continue = $this->continueQuest($userId);
        $totalXp = (int) $user->quantum_xp;

        return [
            'quantum_xp' => $totalXp,
            'rank' => $this->ranks->progressFromTotalXp($totalXp),
            'rank_ladder' => $this->ranks->profileLadder($totalXp),
            'daily_streak' => (int) $user->daily_streak,
            'daily_lesson_goal' => self::DAILY_LESSON_GOAL,
            'lessons_completed_today' => $this->lessonsCompletedToday($user),
            'daily_goal_met' => $this->lessonsCompletedToday($user) >= self::DAILY_LESSON_GOAL,
            'continue_quest' => $continue,
            'year_progress' => $this->yearProgressSummary($userId),
        ];
    }

    /** @return list<array{id: string, label: string, earned: bool}> */
    public function badgesForUser(int $userId): array
    {
        $passedModules = ModuleQuizResult::where('user_id', $userId)->where('passed', true)->count();
        $perfect = ModuleQuizResult::where('user_id', $userId)->where('passed', true)
            ->whereColumn('score', 'total')->exists();

        $years = Year::orderBy('id')->get();
        $firstYearDone = $years->isNotEmpty() && $this->progress->isYearCompleted($userId, $years->first()->id);
        $fourthYear = $years->last();
        $fourthDone = $fourthYear && $this->progress->isYearCompleted($userId, $fourthYear->id);

        $definitions = [
            [
                'id' => 'first_lesson',
                'label' => 'First lesson cleared',
                'emoji' => '📖',
                'description' => 'You completed your first lesson in the Learning Galaxy.',
                'how_to_earn' => 'Finish any lesson activity or quick check in a module.',
                'earned' => DB::table('lesson_completions')->where('user_id', $userId)->exists(),
            ],
            [
                'id' => 'module_quiz',
                'label' => 'Module quiz champion',
                'emoji' => '🏆',
                'description' => 'You passed a module quiz and proved the material stuck.',
                'how_to_earn' => 'Complete all lessons in a module, then pass its module quiz.',
                'earned' => $passedModules >= 1,
            ],
            [
                'id' => 'perfect_quiz',
                'label' => 'Perfect module quiz',
                'emoji' => '💯',
                'description' => 'Flawless run — every question correct on a module quiz.',
                'how_to_earn' => 'Score 100% on any module quiz.',
                'earned' => $perfect,
            ],
            [
                'id' => 'year_one',
                'label' => 'First Year complete',
                'emoji' => '🪐',
                'description' => 'You mastered First Year: every subject final quiz passed.',
                'how_to_earn' => 'Pass the final subject quiz for each First Year subject.',
                'earned' => $firstYearDone,
            ],
            [
                'id' => 'graduate',
                'label' => 'Quantum graduate',
                'emoji' => '🎓',
                'description' => 'Full curriculum complete — all year-level final quizzes passed.',
                'how_to_earn' => 'Pass every final subject quiz across all years in the galaxy.',
                'earned' => $fourthDone,
            ],
        ];

        return $definitions;
    }

    public function continueQuest(int $userId): ?array
    {
        $years = Year::orderBy('id')->get();
        foreach ($years as $year) {
            if (!$this->progress->isYearUnlocked($userId, $year->id)) {
                continue;
            }
            $subjects = Subject::where('year_id', $year->id)->orderBy('id')->get();
            foreach ($subjects as $subject) {
                $modules = SubjectModule::where('subject_id', $subject->id)->orderBy('id')->get();
                $previousModuleCompleted = true;
                foreach ($modules as $module) {
                    $unlocked = $previousModuleCompleted;
                    if (!$unlocked) {
                        break 2;
                    }
                    if (!$this->progress->isModuleCompleted($userId, $module)) {
                        $lessons = $this->progress->decorateModuleLessons($userId, $module->id, true);
                        $idx = $this->progress->firstIncompleteLessonIndex($lessons);
                        if ($idx !== null) {
                            return [
                                'type' => 'lesson',
                                'year_id' => $year->id,
                                'year_name' => $year->name,
                                'subject_id' => $subject->id,
                                'subject_name' => $subject->name,
                                'module_id' => $module->id,
                                'module_name' => $module->name,
                                'lesson_index' => $idx,
                            ];
                        }
                        if ($this->progress->areModuleLessonsComplete($userId, $module)
                            && !$this->progress->hasPassedModuleQuiz($userId, $module->id)) {
                            return [
                                'type' => 'module_quiz',
                                'year_id' => $year->id,
                                'subject_id' => $subject->id,
                                'module_id' => $module->id,
                                'module_name' => $module->name,
                            ];
                        }
                    }
                    $previousModuleCompleted = $this->progress->isModuleCompleted($userId, $module);
                }
            }
        }

        return null;
    }

    /** @return list<array<string, mixed>> */
    public function yearProgressSummary(int $userId): array
    {
        return Year::orderBy('id')->get()->map(function (Year $year) use ($userId) {
            $modules = SubjectModule::whereIn(
                'subject_id',
                Subject::where('year_id', $year->id)->pluck('id')
            )->get();
            $total = $modules->count();
            $done = 0;
            foreach ($modules as $module) {
                if ($this->progress->isModuleCompleted($userId, $module)) {
                    $done++;
                }
            }

            return [
                'year_id' => $year->id,
                'year_name' => $year->name,
                'modules_completed' => $done,
                'modules_total' => $total,
                'percent' => $total > 0 ? (int) round(($done / $total) * 100) : 0,
                'is_unlocked' => $this->progress->isYearUnlocked($userId, $year->id),
                'is_completed' => $this->progress->isYearCompleted($userId, $year->id),
            ];
        })->values()->all();
    }

    public function defaultMissionForSubject(Subject $subject): string
    {
        if (!empty($subject->mission)) {
            return (string) $subject->mission;
        }

        return 'Complete every module and pass each quiz to master '.$subject->name.'.';
    }

    /** @return array{xp_awarded: int, total_xp: int, rank: array, leveled_up: bool, previous_level: int} */
    private function buildEngagementPayload(User $user, int $xpAwarded, int $previousXp): array
    {
        $totalXp = (int) $user->quantum_xp;
        $rankBefore = $this->ranks->progressFromTotalXp($previousXp);
        $rankAfter = $this->ranks->progressFromTotalXp($totalXp);

        return [
            'xp_awarded' => $xpAwarded,
            'total_xp' => $totalXp,
            'rank' => $rankAfter,
            'leveled_up' => $rankAfter['level'] > $rankBefore['level'],
            'previous_level' => $rankBefore['level'],
        ];
    }

    private function lessonsCompletedToday(User $user): int
    {
        $today = Carbon::today()->toDateString();
        if ($user->lessons_today_date !== $today) {
            return 0;
        }

        return (int) $user->lessons_today_count;
    }

    private function touchDailyActivity(User $user, bool $countLesson): void
    {
        $today = Carbon::today();
        $todayStr = $today->toDateString();

        if ($user->last_activity_date) {
            $last = Carbon::parse($user->last_activity_date);
            if ($last->isSameDay($today->copy()->subDay())) {
                $user->daily_streak = (int) $user->daily_streak + 1;
            } elseif (!$last->isSameDay($today)) {
                $user->daily_streak = 1;
            }
        } else {
            $user->daily_streak = 1;
        }

        $user->last_activity_date = $todayStr;

        if ($countLesson) {
            if ($user->lessons_today_date !== $todayStr) {
                $user->lessons_today_date = $todayStr;
                $user->lessons_today_count = 1;
            } else {
                $user->lessons_today_count = (int) $user->lessons_today_count + 1;
            }
        }

        $user->save();
    }
}
