<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\CurriculumXpService;
use Illuminate\Console\Command;

class SyncQuantumXp extends Command
{
    protected $signature = 'quantum-xp:sync
                            {identifier? : Student email or user ID; omit with --all}
                            {--all : Recalculate XP for every student account}';

    protected $description = 'Recalculate quantum_xp from lesson and module-quiz progress (scales to level 100 at full curriculum)';

    public function handle(CurriculumXpService $curriculumXp): int
    {
        $curriculumXp->clearCachedTotals();

        if ($this->option('all')) {
            $students = User::query()->where('role', 'student')->orderBy('id')->get();
            foreach ($students as $student) {
                $xp = $curriculumXp->syncUserQuantumXp((int) $student->id);
                $this->line("{$student->email}: {$xp} XP");
            }
            $this->info('Synced '.$students->count().' student(s).');

            return self::SUCCESS;
        }

        $identifier = trim((string) $this->argument('identifier'));
        if ($identifier === '') {
            $this->error('Provide a student email/ID or use --all.');

            return self::FAILURE;
        }

        $user = ctype_digit($identifier)
            ? User::query()->find((int) $identifier)
            : User::query()->where('email', $identifier)->first();

        if (! $user || $user->role !== 'student') {
            $this->error('Student not found.');

            return self::FAILURE;
        }

        $xp = $curriculumXp->syncUserQuantumXp((int) $user->id);
        $this->info("{$user->email}: {$xp} Quantum XP");

        return self::SUCCESS;
    }
}
