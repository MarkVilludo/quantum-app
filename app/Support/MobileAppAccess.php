<?php

namespace App\Support;

use App\Models\User;
use Illuminate\Http\JsonResponse;

class MobileAppAccess
{
    public static function isStudent(?User $user): bool
    {
        return $user !== null && $user->role === 'student';
    }

    public static function studentOnlyMessage(): string
    {
        $cms = CmsUrl::base();

        return 'The Quantum Realm mobile app is for student accounts only. Teachers and administrators should sign in on the web CMS at '.$cms.'.';
    }

    public static function studentOnlyResponse(): JsonResponse
    {
        return response()->json([
            'message' => self::studentOnlyMessage(),
            'mobile_app_student_only' => true,
        ], 403);
    }
}
