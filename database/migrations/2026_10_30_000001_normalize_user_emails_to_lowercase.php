<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        User::withTrashed()->get()->each(function (User $user) {
            $normalized = User::normalizeEmail($user->email);
            if ($normalized !== '' && $normalized !== $user->email) {
                $user->forceFill(['email' => $normalized])->saveQuietly();
            }
        });
    }

    public function down(): void
    {
        //
    }
};
