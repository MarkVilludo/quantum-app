<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            YearAndSubjectSeeder::class,
            SubjectModuleSeeder::class,
            ModuleLessonSeeder::class,
            QuizSeeder::class,
        ]);

        $admin = \App\Models\User::withTrashed()->firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'password' => 'password',
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );

        if ($admin->trashed()) {
            $admin->restore();
        }
        if (! $admin->hasVerifiedEmail()) {
            $admin->forceFill(['email_verified_at' => now()])->saveQuietly();
        }

        if (\App\Models\User::withTrashed()->where('role', 'teacher')->doesntExist()) {
            \App\Models\User::factory(3)->create([
                'role' => 'teacher',
                'password' => 'password',
            ]);
        }

        $this->call(ResetUserPasswordsSeeder::class);
        $this->call(AndrewVilludoUserSeeder::class);
    }
}
