<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Year;
use Illuminate\Database\Seeder;

class AndrewVilludoUserSeeder extends Seeder
{
    public const EMAIL = 'andrewvilludo321@gmail.com';

    /**
     * Dev/student account for app and CMS login + password reset testing.
     */
    public function run(): void
    {
        $email = User::normalizeEmail(self::EMAIL);
        $firstYearId = Year::query()->where('name', 'First Year')->value('id');

        $user = User::withTrashed()->firstOrCreate(
            ['email' => $email],
            [
                'name' => 'Andrew Villudo',
                'password' => 'password',
                'role' => 'student',
                'year_id' => $firstYearId,
            ]
        );

        if ($user->trashed()) {
            $user->restore();
        }

        $user->forceFill([
            'name' => 'Andrew Villudo',
            'role' => 'student',
            'year_id' => $firstYearId,
            'password' => 'password',
            'email_verified_at' => now(),
        ])->saveQuietly();

        $this->command?->info('Student account ready: '.self::EMAIL.' / password');
    }
}
