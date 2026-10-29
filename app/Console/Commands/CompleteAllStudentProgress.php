<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\DevStudentGraduationService;
use Illuminate\Console\Command;

class CompleteAllStudentProgress extends Command
{
    protected $signature = 'student:complete-all
                            {identifier : Student email or numeric user ID}
                            {--force : Allow running when dev tools are disabled in config}
                            {--yes : Skip confirmation prompt}';

    protected $description = 'Dev utility: mark all lessons, module quizzes, and final subject quizzes passed for every year in the CMS';

    public function handle(DevStudentGraduationService $graduation): int
    {
        if (! $graduation->toolsEnabled() && ! $this->option('force')) {
            $this->error('Dev progression tools are disabled. Set QUANTUM_DEV_TOOLS=true or use --force (not recommended on production).');

            return self::FAILURE;
        }

        $identifier = trim((string) $this->argument('identifier'));
        $user = $this->resolveStudent($identifier);

        if (! $user) {
            $this->error("No student account found for: {$identifier}");

            return self::FAILURE;
        }

        if ($user->role !== 'student') {
            $this->error("User {$user->email} is role \"{$user->role}\" — only student accounts are supported.");

            return self::FAILURE;
        }

        if (! $this->option('yes') && ! $this->confirm("Complete entire curriculum for {$user->name} ({$user->email})?", true)) {
            $this->info('Cancelled.');

            return self::SUCCESS;
        }

        $summary = $graduation->completeAllCurriculumForUser($user);

        $this->info('Student marked as fully graduated across CMS curriculum.');
        $this->table(
            ['Metric', 'Count / value'],
            [
                ['Years in CMS', $summary['years']],
                ['Subjects', $summary['subjects']],
                ['Modules', $summary['modules']],
                ['Lessons completed', $summary['lessons']],
                ['Module quizzes passed', $summary['module_quizzes']],
                ['Final subject quizzes passed', $summary['final_subject_quizzes']],
                ['Quantum XP', $summary['quantum_xp']],
                ['All years completed (progression API)', $summary['all_years_completed'] ? 'yes' : 'no'],
            ]
        );

        return self::SUCCESS;
    }

    private function resolveStudent(string $identifier): ?User
    {
        if (ctype_digit($identifier)) {
            return User::query()->find((int) $identifier);
        }

        return User::query()->where('email', $identifier)->first();
    }
}
