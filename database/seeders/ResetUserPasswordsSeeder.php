<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Year;
use Illuminate\Database\Seeder;

class ResetUserPasswordsSeeder extends Seeder
{
    /**
     * Set every user's password to "password" (local/dev convenience).
     * Creates default dev accounts when the users table is empty.
     */
    public function run(): void
    {
        if (User::query()->withTrashed()->count() === 0) {
            $this->command?->warn('No users found — creating default dev accounts…');
            $this->ensureCurriculumSeeded();
            $this->seedDefaultUsers();
        }

        $this->ensureSecondYearTeacher();

        User::query()->withTrashed()->each(function (User $user) {
            $user->forceFill(['password' => 'password'])->saveQuietly();
        });

        $this->command?->info('All user passwords set to "password".');
        $this->command?->line('  Admin: admin@example.com / password');
        $this->command?->line('  Teacher (2nd year): Harrypotter_v2@gmail.com / password');
    }

    private function ensureCurriculumSeeded(): void
    {
        if (Year::query()->exists()) {
            return;
        }

        $this->call([
            YearAndSubjectSeeder::class,
            SubjectModuleSeeder::class,
            ModuleLessonSeeder::class,
            QuizSeeder::class,
        ]);
    }

    private function seedDefaultUsers(): void
    {
        if (User::where('email', 'admin@example.com')->exists()) {
            return;
        }

        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => 'password',
            'role' => 'admin',
        ]);

        User::factory(3)->create([
            'role' => 'teacher',
            'password' => 'password',
        ]);
    }

    private function ensureSecondYearTeacher(): void
    {
        $secondYearId = Year::query()->where('name', 'Second Year')->value('id');

        $teacher = User::withTrashed()->firstOrCreate(
            ['email' => 'Harrypotter_v2@gmail.com'],
            [
                'name' => 'Harry Potter',
                'password' => 'password',
                'role' => 'teacher',
                'year_id' => $secondYearId,
            ]
        );

        if ($teacher->trashed()) {
            $teacher->restore();
        }

        $teacher->forceFill([
            'name' => 'Harry Potter',
            'role' => 'teacher',
            'year_id' => $secondYearId,
            'password' => 'password',
        ])->saveQuietly();
    }
}
